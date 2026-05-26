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

    public function countTotalQrScans(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             JOIN tours t     ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getLastQrScanAt(int $userId): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT MAX(qs.scanned_at)
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             JOIN tours t     ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL'
        );
        $stmt->execute([$userId]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null) ? (string) $val : null;
    }

    public function countPublishedTours(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM tours t
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.is_published = 1
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countPositions(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM positions p
             JOIN tours t      ON p.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND p.deleted_at IS NULL
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * QR scan counts per calendar day for the last $days days (including today).
     * Returns array keyed by 'Y-m-d' => count. Days with no scans are absent.
     */
    public function getQrScansByDay(int $userId, int $days = 7): array
    {
        $days = max(1, min($days, 30));
        $stmt = $this->db->prepare(
            'SELECT DATE(qs.scanned_at) AS scan_date, COUNT(*) AS scan_count
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             JOIN tours t     ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND qs.scanned_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY DATE(qs.scanned_at)
             ORDER BY scan_date ASC'
        );
        $stmt->execute([$userId, $days - 1]);
        $rows   = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['scan_date']] = (int) $row['scan_count'];
        }
        return $result;
    }
}
