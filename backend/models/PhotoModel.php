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
    public function create(
        int $positionId,
        string $direction,
        string $filename,
        string $originalFilename,
        string $depthMapFilename,
        bool $processed
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO photos
               (position_id, direction, filename, original_filename,
                depth_map_filename, processed, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([
            $positionId,
            $direction,
            $filename,
            $originalFilename,
            $depthMapFilename,
            $processed ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
