<?php

class BusinessModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function slugExists(string $slug): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM businesses WHERE slug = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() !== false;
    }

    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM businesses WHERE user_id = ? AND deleted_at IS NULL');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE businesses SET deleted_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function create(
        int $userId,
        string $name,
        string $slug,
        ?string $description,
        ?string $phone,
        ?string $address
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO businesses
               (user_id, name, slug, description, phone, address, plan_id, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
        );
        $stmt->execute([$userId, $name, $slug, $description, $phone, $address, PLAN_FREE]);
        return (int) $this->db->lastInsertId();
    }
}
