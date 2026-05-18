<?php

class QrScanModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function recordScan(int $qrCodeId, string $ipHash, string $deviceType): bool
    {
        if ($qrCodeId <= 0 || !preg_match('/^[a-f0-9]{64}$/', $ipHash)) {
            return false;
        }

        if (!in_array($deviceType, ['mobile', 'tablet', 'desktop', 'unknown'], true)) {
            $deviceType = 'unknown';
        }

        if ($this->isDuplicate($qrCodeId, $ipHash)) {
            return false;
        }

        // QR 2A guarda solo datos minimos y pseudonimizados: hash de IP,
        // tipo de dispositivo y timestamp. IP, user agent completo y pais
        // quedan en NULL para reducir superficie de privacidad.
        $stmt = $this->db->prepare(
            'INSERT INTO qr_scans (qr_code_id, ip_address, ip_hash, user_agent, device_type, country, scanned_at)
             VALUES (?, NULL, ?, NULL, ?, NULL, NOW())'
        );

        return $stmt->execute([$qrCodeId, $ipHash, $deviceType]);
    }

    public function isDuplicate(int $qrCodeId, string $ipHash, int $minutes = 30): bool
    {
        if ($qrCodeId <= 0 || !preg_match('/^[a-f0-9]{64}$/', $ipHash)) {
            return true;
        }

        $minutes = max(1, min($minutes, 1440));
        $stmt = $this->db->prepare(
            'SELECT 1
             FROM qr_scans
             WHERE qr_code_id = ?
               AND ip_hash = ?
               AND scanned_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
             LIMIT 1'
        );
        $stmt->execute([$qrCodeId, $ipHash, $minutes]);

        return $stmt->fetchColumn() !== false;
    }

    public function countByQrCode(int $qrCodeId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM qr_scans
             WHERE qr_code_id = ?'
        );
        $stmt->execute([$qrCodeId]);

        return (int) $stmt->fetchColumn();
    }

    public function countByTour(int $tourId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM qr_scans qs
             JOIN qr_codes qc ON qs.qr_code_id = qc.id
             WHERE qc.tour_id = ?'
        );
        $stmt->execute([$tourId]);

        return (int) $stmt->fetchColumn();
    }
}
