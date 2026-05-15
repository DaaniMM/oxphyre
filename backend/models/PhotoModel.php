<?php

class PhotoModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    // Devuelve todas las fotos de una posición ordenadas por dirección (N, S, E, O)
    public function getByPosition(int $positionId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM photos
             WHERE position_id = ? AND deleted_at IS NULL
             ORDER BY direction ASC, created_at ASC'
        );
        $stmt->execute([$positionId]);
        return $stmt->fetchAll();
    }

    public function getByPositionAndDirection(int $positionId, string $direction): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM photos
             WHERE position_id = ? AND direction = ? AND deleted_at IS NULL
             ORDER BY created_at DESC, id DESC
             LIMIT 1'
        );
        $stmt->execute([$positionId, $direction]);
        $photo = $stmt->fetch();
        return $photo ?: null;
    }

    // Devuelve las posiciones visitables de un tour sin hacer una consulta por
    // cada card del dashboard. Una posicion solo puede previsualizarse si tiene
    // panoramica principal 360; las fotos detalle N/S/E/O son opcionales.
    public function getPanoramaPositionIdsByTour(int $tourId): array
    {
        $stmt = $this->db->prepare(
            "SELECT positions.id
             FROM positions
             WHERE positions.tour_id = ?
               AND positions.deleted_at IS NULL
               AND EXISTS (
                   SELECT 1
                   FROM photos
                   WHERE photos.position_id = positions.id
                     AND photos.deleted_at IS NULL
                     AND photos.direction = '360'
               )
             ORDER BY positions.order_index ASC"
        );
        $stmt->execute([$tourId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function softDeleteByPositionAndDirection(int $positionId, string $direction): void
    {
        $stmt = $this->db->prepare(
            'UPDATE photos
             SET deleted_at = NOW()
             WHERE position_id = ? AND direction = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$positionId, $direction]);
    }

    // Registra una foto en BD.
    // processed=true significa que MiDaS generó el mapa de profundidad correctamente.
    // depth_map_filename queda vacío si el procesado falló — se puede reintentar después.
    // Los campos R2 son opcionales para preparar Fase 2A sin obligar al upload
    // actual a usar R2. Las llamadas antiguas siguen guardando fotos locales:
    // storage_provider='local', storage_key=NULL y public_url=NULL.
    public function create(
        int $positionId,
        string $direction,
        string $filename,
        string $originalFilename,
        string $depthMapFilename,
        bool $processed,
        string $storageProvider = 'local',
        ?string $storageKey = null,
        ?string $publicUrl = null
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO photos
               (position_id, direction, filename, original_filename,
                depth_map_filename, processed, storage_provider,
                storage_key, public_url, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([
            $positionId,
            $direction,
            $filename,
            $originalFilename,
            $depthMapFilename,
            $processed ? 1 : 0,
            $storageProvider,
            $storageKey,
            $publicUrl,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
