<?php

class DashboardModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function countTours(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM tours t
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countBusinesses(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM businesses WHERE user_id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countQrScansLast30Days(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             JOIN tours t     ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND qs.scanned_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
