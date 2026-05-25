<?php

/**
 * AdminModel — consultas de supervisión global solo para el rol admin.
 *
 * Todas las consultas son SELECT sin parámetros de entrada del usuario;
 * no se exponen datos privados ni se permiten modificaciones.
 * Se usan prepared statements para uniformidad con el resto del proyecto.
 */
class AdminModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    // ── Contadores globales ───────────────────────────────────────────────────

    public function countUsers(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countBusinesses(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM businesses WHERE deleted_at IS NULL');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countTours(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM tours WHERE deleted_at IS NULL');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countPositions(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM positions WHERE deleted_at IS NULL');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countPhotos(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM photos WHERE deleted_at IS NULL');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countQrCodes(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM qr_codes');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countQrScans(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM qr_scans');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ── Últimos registros para supervisión ───────────────────────────────────

    /**
     * Devuelve los últimos usuarios registrados.
     * No devuelve contraseñas ni tokens sensibles.
     */
    public function getLatestUsers(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50)); // clamp: nunca 0 ni valores excesivos
        $stmt  = $this->db->prepare(
            'SELECT id, name, email, role, email_verified, created_at
             FROM users
             ORDER BY created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Devuelve los últimos negocios creados con datos del propietario y plan.
     * Solo negocios activos (sin soft delete).
     */
    public function getLatestBusinesses(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        $stmt  = $this->db->prepare(
            'SELECT b.id, b.name, b.slug, b.plan_id, b.created_at,
                    u.name  AS owner_name,
                    u.email AS owner_email
             FROM businesses b
             JOIN users u ON b.user_id = u.id
             WHERE b.deleted_at IS NULL
             ORDER BY b.created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Devuelve los últimos tours creados con nombre de negocio y estado publicado.
     * Solo tours y negocios activos (sin soft delete).
     */
    public function getLatestTours(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        $stmt  = $this->db->prepare(
            'SELECT t.id, t.title, t.slug, t.is_published, t.created_at,
                    b.name AS business_name,
                    b.slug AS business_slug
             FROM tours t
             JOIN businesses b ON t.business_id = b.id
             WHERE t.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY t.created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
