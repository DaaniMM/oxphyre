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

    public function getValidForPublic(int $positionId, int $tourId): array
    {
        if ($positionId <= 0 || $tourId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT h.id,
                    h.yaw_rad,
                    h.pitch_rad,
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
                'label'            => (string) ($row['hotspot_label'] ?? ''),
                'targetPositionId' => (int)    $row['target_position_id'],
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
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
