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
             WHERE position_id = ?
             ORDER BY direction ASC'
        );
        $stmt->execute([$positionId]);
        return $stmt->fetchAll();
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
