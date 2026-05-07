<?php

class PositionModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

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
}
