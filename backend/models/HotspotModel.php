<?php

class HotspotModel
{
    private const TYPE_NAVIGATION = 'navigation';
    private const LABEL_MAX_LENGTH = 80;

    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function listByPosition(int $positionId): array
    {
        if ($positionId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT *
             FROM hotspots
             WHERE position_id = ?
               AND deleted_at IS NULL
             ORDER BY created_at ASC, id ASC'
        );
        $stmt->execute([$positionId]);

        return $stmt->fetchAll();
    }

    public function listPublicByPosition(int $positionId): array
    {
        if ($positionId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT *
             FROM hotspots
             WHERE position_id = ?
               AND type = ?
               AND is_active = 1
               AND needs_review = 0
               AND deleted_at IS NULL
             ORDER BY created_at ASC, id ASC'
        );
        $stmt->execute([$positionId, self::TYPE_NAVIGATION]);

        return $stmt->fetchAll();
    }

    public function listDashboardByPosition(int $positionId, int $tourId): array
    {
        if ($positionId <= 0 || $tourId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT h.id,
                    h.target_position_id,
                    h.label,
                    h.texture_x,
                    h.texture_y,
                    h.is_active,
                    h.needs_review,
                    h.created_at,
                    h.updated_at,
                    p_dest.name AS target_position_name
             FROM hotspots h
             JOIN positions p_origin
                  ON p_origin.id = h.position_id
                 AND p_origin.tour_id = ?
                 AND p_origin.deleted_at IS NULL
             JOIN positions p_dest
                  ON p_dest.id = h.target_position_id
                 AND p_dest.tour_id = p_origin.tour_id
                 AND p_dest.deleted_at IS NULL
             WHERE h.position_id = ?
               AND h.type = ?
               AND h.deleted_at IS NULL
               AND EXISTS (
                   SELECT 1
                   FROM photos ph_dest
                   WHERE ph_dest.position_id = p_dest.id
                     AND ph_dest.direction = \'360\'
                     AND ph_dest.deleted_at IS NULL
                   LIMIT 1
               )
             ORDER BY h.created_at ASC, h.id ASC'
        );
        $stmt->execute([$tourId, $positionId, self::TYPE_NAVIGATION]);

        return array_map([$this, 'formatDashboardRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getValidForPublic(int $positionId, int $tourId): array
    {
        if ($positionId <= 0 || $tourId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT h.id,
                    h.yaw_rad,
                    h.pitch_rad,
                    h.texture_x,
                    h.texture_y,
                    COALESCE(NULLIF(h.label, \'\'), NULLIF(h.title, \'\'), p_dest.name) AS hotspot_label,
                    h.target_position_id
             FROM hotspots h
             JOIN positions p_dest
                  ON p_dest.id = h.target_position_id
                 AND p_dest.tour_id = ?
                 AND p_dest.deleted_at IS NULL
             WHERE h.position_id = ?
               AND h.type = ?
               AND h.is_active = 1
               AND h.needs_review = 0
               AND h.deleted_at IS NULL
               AND h.texture_x IS NOT NULL
               AND h.texture_y IS NOT NULL
               AND h.texture_x BETWEEN 0 AND 1
               AND h.texture_y BETWEEN 0 AND 1
               AND EXISTS (
                   SELECT 1
                   FROM photos ph_dest
                   WHERE ph_dest.position_id = p_dest.id
                     AND ph_dest.direction = \'360\'
                     AND ph_dest.deleted_at IS NULL
                   LIMIT 1
               )
             ORDER BY h.created_at ASC, h.id ASC'
        );
        $stmt->execute([$tourId, $positionId, self::TYPE_NAVIGATION]);

        return array_map(static function (array $row): array {
            return [
                'id'               => (int)    $row['id'],
                'yawRad'           => (float)  $row['yaw_rad'],
                'pitchRad'         => (float)  $row['pitch_rad'],
                'textureX'         => (float)  $row['texture_x'],
                'textureY'         => (float)  $row['texture_y'],
                'label'            => (string) ($row['hotspot_label'] ?? ''),
                'targetPositionId' => (int)    $row['target_position_id'],
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getByIdInPositionTour(int $id, int $positionId, int $tourId): ?array
    {
        if ($id <= 0 || $positionId <= 0 || $tourId <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT h.*
             FROM hotspots h
             JOIN positions p_origin
                  ON p_origin.id = h.position_id
                 AND p_origin.tour_id = ?
                 AND p_origin.deleted_at IS NULL
             WHERE h.id = ?
               AND h.position_id = ?
               AND h.type = ?
               AND h.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$tourId, $id, $positionId, self::TYPE_NAVIGATION]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    public function createNavigation(array $data): int
    {
        $positionId = (int) ($data['position_id'] ?? 0);
        $targetPositionId = (int) ($data['target_position_id'] ?? 0);

        if ($positionId <= 0 || $targetPositionId <= 0) {
            return 0;
        }

        $yawRad = $this->normalizeCoordinate($data['yaw_rad'] ?? null, -M_PI, M_PI);
        $pitchRad = $this->normalizeCoordinate($data['pitch_rad'] ?? null, -M_PI / 2, M_PI / 2);

        if ($yawRad === null || $pitchRad === null) {
            return 0;
        }

        $panoramaPhotoId = (int) ($data['panorama_photo_id'] ?? 0);
        $label = $this->sanitizeLabel($data['label'] ?? null);
        $needsReview = !empty($data['needs_review']) ? 1 : 0;
        $isActive = array_key_exists('is_active', $data) ? (!empty($data['is_active']) ? 1 : 0) : 1;

        // El hotspot se guarda como coordenada angular sobre el cilindro de la
        // panoramica principal. panorama_photo_id permite detectar mas adelante
        // si el punto fue colocado sobre una panoramica que ya se sustituyo.
        $stmt = $this->db->prepare(
            'INSERT INTO hotspots
                (position_id, target_position_id, panorama_photo_id, type, label,
                 yaw_rad, pitch_rad, needs_review, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $positionId,
            $targetPositionId,
            $panoramaPhotoId > 0 ? $panoramaPhotoId : null,
            self::TYPE_NAVIGATION,
            $label,
            $yawRad,
            $pitchRad,
            $needsReview,
            $isActive,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createNavigationFromTexture(array $data): int
    {
        $positionId = (int) ($data['position_id'] ?? 0);
        $targetPositionId = (int) ($data['target_position_id'] ?? 0);

        if ($positionId <= 0 || $targetPositionId <= 0 || $positionId === $targetPositionId) {
            return 0;
        }

        $textureX = $this->normalizeCoordinate($data['texture_x'] ?? null, 0, 1);
        $textureY = $this->normalizeCoordinate($data['texture_y'] ?? null, 0, 1);

        if ($textureX === null || $textureY === null) {
            return 0;
        }

        $legacyAngles = $this->deriveLegacyAngles($textureX, $textureY);
        $panoramaPhotoId = (int) ($data['panorama_photo_id'] ?? 0);
        $label = $this->sanitizeLabel($data['label'] ?? null);
        $needsReview = !empty($data['needs_review']) ? 1 : 0;
        $isActive = array_key_exists('is_active', $data) ? (!empty($data['is_active']) ? 1 : 0) : 1;

        // texture_x/texture_y son la fuente principal del editor y del render.
        // yaw_rad/pitch_rad se rellenan como datos legacy derivados para mantener
        // compatibilidad con columnas antiguas y futuras herramientas de diagnostico.
        $stmt = $this->db->prepare(
            'INSERT INTO hotspots
                (position_id, target_position_id, panorama_photo_id, type, label,
                 yaw_rad, pitch_rad, texture_x, texture_y,
                 needs_review, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $positionId,
            $targetPositionId,
            $panoramaPhotoId > 0 ? $panoramaPhotoId : null,
            self::TYPE_NAVIGATION,
            $label,
            $legacyAngles['yaw_rad'],
            $legacyAngles['pitch_rad'],
            $textureX,
            $textureY,
            $needsReview,
            $isActive,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateTextureScoped(int $id, int $positionId, int $tourId, float $textureX, float $textureY): bool
    {
        if ($id <= 0 || $positionId <= 0 || $tourId <= 0) {
            return false;
        }

        $textureX = $this->normalizeCoordinate($textureX, 0, 1);
        $textureY = $this->normalizeCoordinate($textureY, 0, 1);

        if ($textureX === null || $textureY === null) {
            return false;
        }

        $legacyAngles = $this->deriveLegacyAngles($textureX, $textureY);

        // Guardar/recolocar confirma que la flecha fue revisada sobre la panorámica actual.
        $stmt = $this->db->prepare(
            'UPDATE hotspots h
             JOIN positions p_origin
                  ON p_origin.id = h.position_id
                 AND p_origin.tour_id = ?
                 AND p_origin.deleted_at IS NULL
             SET h.texture_x = ?,
                 h.texture_y = ?,
                 h.yaw_rad = ?,
                 h.pitch_rad = ?,
                 h.needs_review = 0,
                 h.updated_at = NOW()
             WHERE h.id = ?
               AND h.position_id = ?
               AND h.type = ?
               AND h.deleted_at IS NULL'
        );

        return $stmt->execute([
            $tourId,
            $textureX,
            $textureY,
            $legacyAngles['yaw_rad'],
            $legacyAngles['pitch_rad'],
            $id,
            $positionId,
            self::TYPE_NAVIGATION,
        ]);
    }

    public function softDelete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE hotspots
             SET deleted_at = NOW()
             WHERE id = ?
               AND deleted_at IS NULL'
        );

        return $stmt->execute([$id]);
    }

    public function softDeleteScoped(int $id, int $positionId, int $tourId): bool
    {
        if ($id <= 0 || $positionId <= 0 || $tourId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE hotspots h
             JOIN positions p_origin
                  ON p_origin.id = h.position_id
                 AND p_origin.tour_id = ?
                 AND p_origin.deleted_at IS NULL
             SET h.deleted_at = NOW(),
                 h.updated_at = NOW()
             WHERE h.id = ?
               AND h.position_id = ?
               AND h.type = ?
               AND h.deleted_at IS NULL'
        );

        return $stmt->execute([$tourId, $id, $positionId, self::TYPE_NAVIGATION]);
    }

    public function setActive(int $id, bool $active): bool
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE hotspots
             SET is_active = ?
             WHERE id = ?
               AND deleted_at IS NULL'
        );

        return $stmt->execute([$active ? 1 : 0, $id]);
    }

    public function setActiveScoped(int $id, int $positionId, int $tourId, bool $active): bool
    {
        if ($id <= 0 || $positionId <= 0 || $tourId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE hotspots h
             JOIN positions p_origin
                  ON p_origin.id = h.position_id
                 AND p_origin.tour_id = ?
                 AND p_origin.deleted_at IS NULL
             SET h.is_active = ?,
                 h.updated_at = NOW()
             WHERE h.id = ?
               AND h.position_id = ?
               AND h.type = ?
               AND h.deleted_at IS NULL'
        );

        return $stmt->execute([$tourId, $active ? 1 : 0, $id, $positionId, self::TYPE_NAVIGATION]);
    }

    public function markNeedsReviewByPosition(int $positionId): bool
    {
        if ($positionId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE hotspots
             SET needs_review = 1
             WHERE position_id = ?
               AND type = ?
               AND deleted_at IS NULL'
        );

        return $stmt->execute([$positionId, self::TYPE_NAVIGATION]);
    }

    private function formatDashboardRow(array $row): array
    {
        return [
            'id'                 => (int) $row['id'],
            'targetPositionId'   => (int) $row['target_position_id'],
            'targetPositionName' => (string) ($row['target_position_name'] ?? ''),
            'label'              => (string) ($row['label'] ?? ''),
            'textureX'           => $row['texture_x'] !== null ? (float) $row['texture_x'] : null,
            'textureY'           => $row['texture_y'] !== null ? (float) $row['texture_y'] : null,
            'isActive'           => (bool) $row['is_active'],
            'needsReview'        => (bool) $row['needs_review'],
            'createdAt'          => $row['created_at'] ?? null,
            'updatedAt'          => $row['updated_at'] ?? null,
        ];
    }

    private function deriveLegacyAngles(float $textureX, float $textureY): array
    {
        return [
            'yaw_rad' => ($textureX - 0.5) * 2 * M_PI,
            'pitch_rad' => (0.5 - $textureY) * M_PI,
        ];
    }

    private function normalizeCoordinate(mixed $value, float $min, float $max): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }

        $coordinate = (float) $value;
        if ($coordinate < $min || $coordinate > $max) {
            return null;
        }

        return $coordinate;
    }

    private function sanitizeLabel(mixed $label): ?string
    {
        if ($label === null) {
            return null;
        }

        $clean = trim(strip_tags((string) $label));
        if ($clean === '') {
            return null;
        }

        return mb_substr($clean, 0, self::LABEL_MAX_LENGTH);
    }
}
