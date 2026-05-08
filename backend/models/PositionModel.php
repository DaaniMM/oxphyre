<?php

class PositionModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    // Devuelve todas las posiciones de un tour ordenadas por orden
    public function getByTour(int $tourId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM positions
             WHERE tour_id = ? AND deleted_at IS NULL
             ORDER BY order_index ASC'
        );
        $stmt->execute([$tourId]);
        return $stmt->fetchAll();
    }

    // Busca una posición por ID verificando que pertenece al tour dado (ownership check)
    public function getByIdAndTour(int $id, int $tourId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM positions
             WHERE id = ? AND tour_id = ? AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$id, $tourId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    // Cuenta cuántas posiciones activas tiene un tour (para aplicar límites de plan)
    public function countByTour(int $tourId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM positions WHERE tour_id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$tourId]);
        return (int) $stmt->fetchColumn();
    }

    // Crea una nueva posición y devuelve su ID
    public function create(int $tourId, string $name, int $orderIndex): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO positions (tour_id, name, order_index, created_at)
             VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$tourId, $name, $orderIndex]);
        return (int) $this->db->lastInsertId();
    }

    // Soft delete: nunca borramos físicamente (regla global del proyecto)
    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE positions SET deleted_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$id]);
    }
}
