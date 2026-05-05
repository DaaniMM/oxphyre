<?php

class TourModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function getByBusiness(int $businessId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, title, description, slug, is_published, created_at
             FROM tours
             WHERE business_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC'
        );
        $stmt->execute([$businessId]);
        return $stmt->fetchAll();
    }
}
