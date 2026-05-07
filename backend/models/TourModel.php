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

    public function countByBusiness(int $businessId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM tours WHERE business_id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$businessId]);
        return (int) $stmt->fetchColumn();
    }

    public function slugExistsInBusiness(int $businessId, string $slug): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM tours WHERE business_id = ? AND slug = ? AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([$businessId, $slug]);
        return $stmt->fetchColumn() !== false;
    }

    public function create(int $businessId, string $title, ?string $description, string $slug): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tours
               (business_id, title, description, slug, is_published, views_count, created_at, updated_at)
             VALUES (?, ?, ?, ?, 0, 0, NOW(), NOW())'
        );
        $stmt->execute([$businessId, $title, $description, $slug]);
        return (int) $this->db->lastInsertId();
    }
}
