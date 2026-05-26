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

    // ── Métodos Pro ──────────────────────────────────────────────────────────

    /**
     * Counts QR scans grouped by device_type for a given user.
     * Values stored by QrController: 'mobile' | 'tablet' | 'desktop' | 'unknown'.
     * Returns associative array, e.g. ['mobile' => 12, 'desktop' => 3].
     */
    public function getDeviceTypeCounts(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(qs.device_type, "unknown") AS device_type, COUNT(*) AS cnt
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             JOIN tours t     ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL
             GROUP BY qs.device_type
             ORDER BY cnt DESC'
        );
        $stmt->execute([$userId]);
        $rows   = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $type          = (string) ($row['device_type'] ?? 'unknown');
            $result[$type] = (int) $row['cnt'];
        }
        return $result;
    }

    /**
     * Returns up to $limit tours sorted by total QR scan count (descending).
     * Includes tours with 0 scans so the user sees all tours.
     * Each row has 'tour_name' (string) and 'scan_count' (int).
     */
    public function getTourScanRanking(int $userId, int $limit = 5): array
    {
        // (int) cast + max/min clamp es suficiente para blindar LIMIT.
        // PDO con LIMIT ? puede dar problemas de tipo en algunos drivers MySQL;
        // concatenar el entero ya validado es la forma más segura y estándar.
        $limit = max(1, min((int) $limit, 20));
        $stmt  = $this->db->prepare(
            'SELECT t.name AS tour_name, COUNT(qs.id) AS scan_count
             FROM tours t
             JOIN businesses b  ON t.business_id = b.id
             LEFT JOIN qr_codes qc ON qc.tour_id = t.id
             LEFT JOIN qr_scans qs ON qs.qr_code_id = qc.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL
             GROUP BY t.id, t.name
             ORDER BY scan_count DESC
             LIMIT ' . (int) $limit
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Returns QR scan totals for the last 7 days and the 7 days before that.
     * Used to compute week-over-week trend in the Pro dashboard.
     * Returns ['last7' => int, 'prev7' => int].
     */
    public function getWeekComparison(int $userId): array
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
               AND qs.scanned_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)'
        );
        $stmt->execute([$userId]);
        $last7 = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             JOIN tours t     ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE b.user_id = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND qs.scanned_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
               AND qs.scanned_at < DATE_SUB(CURDATE(), INTERVAL 7 DAY)'
        );
        $stmt->execute([$userId]);
        $prev7 = (int) $stmt->fetchColumn();

        return ['last7' => $last7, 'prev7' => $prev7];
    }
}
