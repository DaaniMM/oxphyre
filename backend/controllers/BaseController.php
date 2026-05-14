<?php

class BaseController
{
    protected function ensureCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function flash(string $type, string $message, ?string $secondary = null): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        if ($secondary !== null && $secondary !== '') {
            $_SESSION['flash']['secondary'] = $secondary;
        }
    }
}
