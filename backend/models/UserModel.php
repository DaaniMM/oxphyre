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
            'SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1'
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

    public function create(string $name, string $email, string $hashedPassword): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, created_at, updated_at)
             VALUES (?, ?, ?, "user", NOW(), NOW())'
        );
        $stmt->execute([$name, $email, $hashedPassword]);
        return (int) $this->db->lastInsertId();
    }
}
