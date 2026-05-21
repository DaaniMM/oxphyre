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

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, slug, description, phone, address, city, postal_code, country, plan_id, created_at
             FROM businesses
             WHERE user_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getBySlug(string $slug, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM businesses
             WHERE slug = ? AND user_id = ? AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$slug, $userId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function update(
        int $id,
        string $name,
        ?string $description,
        ?string $phone,
        ?string $address,
        ?string $city,
        ?string $postalCode,
        ?string $country
    ): void
    {
        $stmt = $this->db->prepare(
            'UPDATE businesses
             SET name = ?, description = ?, phone = ?, address = ?, city = ?, postal_code = ?, country = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$name, $description, $phone, $address, $city, $postalCode, $country, $id]);
    }

    public function saveGeocoding(
        int $id,
        ?string $address,
        ?string $city,
        ?string $postalCode,
        ?string $country,
        float $lat,
        float $lng,
        string $provider
    ): void {
        $stmt = $this->db->prepare(
            'UPDATE businesses
             SET address = ?, city = ?, postal_code = ?, country = ?,
                 latitude = ?, longitude = ?, geocoded_at = NOW(), geocoding_provider = ?,
                 updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$address, $city, $postalCode, $country, $lat, $lng, $provider, $id]);
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE businesses
             SET slug = CONCAT(slug, '-deleted-', id), deleted_at = NOW(), updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$id]);
    }

    public function releaseDeletedSlug(string $slug): void
    {
        $stmt = $this->db->prepare(
            "UPDATE businesses
             SET slug = CONCAT(slug, '-deleted-', id), updated_at = NOW()
             WHERE slug = ? AND deleted_at IS NOT NULL AND slug NOT LIKE '%-deleted-%'"
        );
        $stmt->execute([$slug]);
    }

    // Acceso público: busca negocio por slug sin filtrar por user_id
    public function getBySlugPublic(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM businesses WHERE slug = ? AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function create(
        int $userId,
        string $name,
        string $slug,
        ?string $description,
        ?string $phone,
        ?string $address,
        ?string $city,
        ?string $postalCode,
        ?string $country,
        ?int $planId = null
    ): int {
        $safePlanId = in_array($planId, [PLAN_FREE, PLAN_PRO, PLAN_BUSINESS], true) ? $planId : PLAN_FREE;

        $stmt = $this->db->prepare(
            'INSERT INTO businesses
               (user_id, name, slug, description, phone, address, city, postal_code, country, plan_id, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
        );
        $stmt->execute([$userId, $name, $slug, $description, $phone, $address, $city, $postalCode, $country, $safePlanId]);
        return (int) $this->db->lastInsertId();
    }
}
