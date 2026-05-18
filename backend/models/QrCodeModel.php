<?php

class QrCodeModel
{
    private const TOKEN_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    private const TOKEN_LENGTH = 12;

    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function getOrCreateForTour(int $tourId): array
    {
        $existing = $this->findByTourId($tourId);
        if ($existing && !empty($existing['token'])) {
            return $existing;
        }

        if ($existing) {
            $token = $this->generateUniqueToken();
            $stmt = $this->db->prepare(
                'UPDATE qr_codes
                 SET token = ?, filename = ?
                 WHERE id = ?'
            );
            $stmt->execute([$token, $this->buildFilename($token), (int) $existing['id']]);

            return $this->findByTourId($tourId) ?: ['token' => $token];
        }

        $token = $this->generateUniqueToken();
        $stmt = $this->db->prepare(
            'INSERT INTO qr_codes (tour_id, token, filename, total_scans, created_at)
             VALUES (?, ?, ?, 0, NOW())'
        );
        $stmt->execute([$tourId, $token, $this->buildFilename($token)]);

        return $this->findByTourId($tourId) ?: ['token' => $token];
    }

    public function findPublicTargetByToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                qc.id AS qr_id,
                qc.token,
                t.id AS tour_id,
                t.slug AS tour_slug,
                t.is_published,
                b.id AS business_id,
                b.slug AS business_slug
             FROM qr_codes qc
             JOIN tours t ON qc.tour_id = t.id
             JOIN businesses b ON t.business_id = b.id
             WHERE qc.token = ?
               AND t.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function findByTourId(int $tourId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM qr_codes WHERE tour_id = ? LIMIT 1'
        );
        $stmt->execute([$tourId]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = $this->generateBase62Token();
        } while ($this->tokenExists($token));

        return $token;
    }

    private function generateBase62Token(): string
    {
        $token = '';
        $alphabetLength = strlen(self::TOKEN_ALPHABET);
        $maxByte = intdiv(256, $alphabetLength) * $alphabetLength - 1;

        while (strlen($token) < self::TOKEN_LENGTH) {
            $byte = ord(random_bytes(1));
            if ($byte > $maxByte) {
                continue;
            }

            $token .= self::TOKEN_ALPHABET[$byte % $alphabetLength];
        }

        return $token;
    }

    private function tokenExists(string $token): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM qr_codes WHERE token = ? LIMIT 1');
        $stmt->execute([$token]);

        return $stmt->fetchColumn() !== false;
    }

    private function buildFilename(string $token): string
    {
        return 'qr_' . $token . '.png';
    }
}
