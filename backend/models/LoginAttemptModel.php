<?php

class LoginAttemptModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    // Registra un intento de login. Para registro, pasar email vacío y solo IP.
    public function record(string $email, string $ip): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO login_attempts (email, ip_address, attempted_at) VALUES (?, ?, NOW())'
        );
        $stmt->execute([$email, $ip]);
    }

    // Cuenta intentos recientes por email O ip dentro de los últimos $minutes minutos.
    public function countRecent(string $email, string $ip, int $minutes = 15): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE (email = ? OR ip_address = ?)
               AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)'
        );
        $stmt->execute([$email, $ip, $minutes]);
        return (int) $stmt->fetchColumn();
    }

    // Cuenta intentos recientes solo por IP (para rate limiting de registro).
    public function countRecentByIp(string $ip, int $minutes = 60): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE ip_address = ?
               AND email = ""
               AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)'
        );
        $stmt->execute([$ip, $minutes]);
        return (int) $stmt->fetchColumn();
    }

    // Elimina intentos con más de 24 horas para no acumular basura en BD.
    public function clearOld(): void
    {
        $this->db->exec(
            'DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)'
        );
    }
}
