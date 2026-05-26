<?php

class ContactMessageModel
{
    private PDO $db;

    public function __construct()
    {
        require_once BACKEND_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }

    /**
     * Genera un subject legible a partir del inquiry_type.
     * Necesario por compatibilidad con la columna legacy `subject NOT NULL` en producción.
     */
    private function buildSubject(string $inquiryType): string
    {
        $labels = [
            'trial'          => 'Quiero probar Oxphyre',
            'local_business' => 'Soy un negocio local',
            'support'        => 'Soporte o problema de acceso',
            'collaboration'  => 'Colaboración',
            'other'          => 'Otro',
        ];

        $label = $labels[$inquiryType] ?? 'Consulta general';

        return mb_substr('Contacto Oxphyre - ' . $label, 0, 200);
    }

    public function create(array $data): int
    {
        $subject = $this->buildSubject($data['inquiry_type'] ?? '');

        $stmt = $this->db->prepare(
            'INSERT INTO contact_messages
                (name, business_or_lastname, email, phone, inquiry_type, plan_interest,
                 message, privacy_accepted, commercial_contact, subject, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
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
            $subject,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
