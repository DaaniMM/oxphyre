<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, password, role, email_verified
             FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetchColumn() !== false;
    }

    public function create(string $name, string $email, string $hashedPassword, string $verificationToken): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, email_verified, verification_token, created_at, updated_at)
             VALUES (?, ?, ?, "business_free", 0, ?, NOW(), NOW())'
        );
        $stmt->execute([$name, $email, $hashedPassword, $verificationToken]);
        return (int) $this->db->lastInsertId();
    }

    // Marca el email como verificado y elimina el token. Devuelve true si se actualizó.
    public function verifyEmail(string $token): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET email_verified = 1, verification_token = NULL, updated_at = NOW()
             WHERE verification_token = ? AND email_verified = 0'
        );
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // Busca usuario por reset_token válido (no expirado).
    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email FROM users
             WHERE reset_token = ? AND reset_token_expires > NOW() LIMIT 1'
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updatePassword(int $userId, string $hashedPassword): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET password = ?, reset_token = NULL, reset_token_expires = NULL, updated_at = NOW()
             WHERE id = ?'
        );
        $stmt->execute([$hashedPassword, $userId]);
    }

    public function saveResetToken(string $email, string $token, string $expiresAt): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET reset_token = ?, reset_token_expires = ?, updated_at = NOW()
             WHERE email = ?'
        );
        $stmt->execute([$token, $expiresAt, $email]);
    }
}
