<?php

class ContactMessageModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO contact_messages
                (name, business_or_lastname, email, phone, inquiry_type, plan_interest,
                 message, privacy_accepted, commercial_contact, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );

        $stmt->execute([
            $data['name'],
            $data['business_or_lastname'],
            $data['email'],
            $data['phone'],
            $data['inquiry_type'],
            $data['plan_interest'],
            $data['message'],
            !empty($data['privacy_accepted']) ? 1 : 0,
            !empty($data['commercial_contact']) ? 1 : 0,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
