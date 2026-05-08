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

    public function getBySlugAndBusiness(string $slug, int $businessId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM tours
             WHERE slug = ? AND business_id = ? AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$slug, $businessId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    // Acceso público: requiere is_published=1, no verifica propiedad del usuario
    public function getBySlugAndBusinessPublic(int $businessId, string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM tours
             WHERE business_id = ? AND slug = ? AND is_published = 1 AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$businessId, $slug]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
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

    public function update(int $id, string $title, ?string $description, bool $isPublished): void
    {
        $stmt = $this->db->prepare(
            'UPDATE tours
             SET title = ?, description = ?, is_published = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$title, $description, $isPublished ? 1 : 0, $id]);
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE tours SET deleted_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function softDeleteByBusiness(int $businessId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE tours SET deleted_at = NOW() WHERE business_id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$businessId]);
    }
}
