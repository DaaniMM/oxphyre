<?php

class ContactController
{
    private const INQUIRY_TYPES = [
        'trial' => 'Quiero probar Oxphyre',
        'local_business' => 'Soy un negocio local',
        'support' => 'Soporte o problema de acceso',
        'collaboration' => 'Colaboración',
        'other' => 'Otro',
    ];

    private const PLAN_INTERESTS = [
        'free' => 'Free',
        'pro' => 'Pro',
        'business' => 'Business',
        'unknown' => 'No lo sé todavía',
    ];

    public function show(): void
    {
        $this->ensureCsrfToken();

        $formData = $this->emptyFormData();
        $errors = [];
        $successMessage = $_SESSION['contact_success'] ?? '';
        unset($_SESSION['contact_success']);

        require_once VIEWS_PATH . '/contacto.php';
    }

    public function submit(): void
    {
        $this->ensureCsrfToken();

        $formData = $this->sanitizeInput($_POST);
        $errors = $this->validate($formData, $_POST);
        $successMessage = '';

        if (($formData['website'] ?? '') !== '') {
            $_SESSION['contact_success'] = 'Gracias. Hemos recibido tu mensaje y lo revisaremos lo antes posible.';
            $this->redirect('/contacto');
        }

        if ($errors !== []) {
            require_once VIEWS_PATH . '/contacto.php';
            return;
        }

        $messageId = 0;

        try {
            require_once BACKEND_PATH . '/models/ContactMessageModel.php';
            $messageId = (new ContactMessageModel())->create($formData);
        } catch (Throwable $exception) {
            error_log('ContactController::submit save error: ' . $exception->getMessage());
            $errors['general'] = 'No hemos podido guardar tu mensaje ahora mismo. Inténtalo de nuevo en unos minutos.';
            require_once VIEWS_PATH . '/contacto.php';
            return;
        }

        if ($messageId > 0) {
            $emailSent = $this->sendContactEmail($formData, $messageId);
            if (!$emailSent) {
                error_log('ContactController::submit email notification failed for message #' . $messageId);
            }

            $_SESSION['contact_success'] = 'Gracias. Hemos recibido tu mensaje y lo revisaremos lo antes posible.';
            $this->redirect('/contacto');
        }

        $errors['general'] = 'No hemos podido guardar tu mensaje ahora mismo. Inténtalo de nuevo en unos minutos.';
        require_once VIEWS_PATH . '/contacto.php';
    }

    private function sanitizeInput(array $input): array
    {
        return [
            'name' => $this->cleanText($input['name'] ?? '', 100),
            'business_or_lastname' => $this->cleanText($input['business_or_lastname'] ?? '', 120),
            'email' => mb_substr(trim(strip_tags((string) ($input['email'] ?? ''))), 0, 160),
            'phone' => $this->cleanText($input['phone'] ?? '', 40),
            'inquiry_type' => (string) ($input['inquiry_type'] ?? ''),
            'plan_interest' => (string) ($input['plan_interest'] ?? ''),
            'message' => $this->cleanText($input['message'] ?? '', 2000),
            'privacy_accepted' => !empty($input['privacy_accepted']),
            'commercial_contact' => !empty($input['commercial_contact']),
            'website' => mb_substr(trim((string) ($input['website'] ?? '')), 0, 120),
        ];
    }

    private function validate(array $data, array $rawInput): array
    {
        $errors = [];

        if (!$this->isValidCsrf((string) ($rawInput['csrf_token'] ?? ''))) {
            $errors['general'] = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
        }

        if ($data['name'] === '') {
            $errors['name'] = 'Indica tu nombre.';
        }

        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Indica un email válido.';
        }

        if (!array_key_exists($data['inquiry_type'], self::INQUIRY_TYPES)) {
            $errors['inquiry_type'] = 'Elige un tipo de consulta válido.';
        }

        if (!array_key_exists($data['plan_interest'], self::PLAN_INTERESTS)) {
            $errors['plan_interest'] = 'Elige un plan de interés válido.';
        }

        if ($data['message'] === '') {
            $errors['message'] = 'Cuéntanos brevemente tu caso.';
        }

        if (!$data['privacy_accepted']) {
            $errors['privacy_accepted'] = 'Debes aceptar la política de privacidad para enviar el formulario.';
        }

        return $errors;
    }

    private function sendContactEmail(array $data, int $messageId): bool
    {
        $emailServicePath = BACKEND_PATH . '/services/EmailService.php';
        if (!file_exists($emailServicePath)) {
            return false;
        }

        require_once $emailServicePath;
        if (!method_exists(EmailService::class, 'sendContactNotification')) {
            return false;
        }

        return (new EmailService())->sendContactNotification($data, $messageId);
    }

    private function ensureCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function isValidCsrf(string $token): bool
    {
        return $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    private function emptyFormData(): array
    {
        return [
            'name' => '',
            'business_or_lastname' => '',
            'email' => '',
            'phone' => '',
            'inquiry_type' => 'trial',
            'plan_interest' => 'unknown',
            'message' => '',
            'privacy_accepted' => false,
            'commercial_contact' => false,
            'website' => '',
        ];
    }

    private function cleanText(mixed $value, int $maxLength): string
    {
        return mb_substr(trim(strip_tags((string) $value)), 0, $maxLength);
    }

    private function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit();
    }
}
