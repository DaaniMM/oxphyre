<?php

class AuthController
{
    private UserModel $userModel;
    private LoginAttemptModel $attemptModel;

    public function __construct()
    {
        require_once BACKEND_PATH . '/models/UserModel.php';
        require_once BACKEND_PATH . '/models/LoginAttemptModel.php';
        $this->userModel    = new UserModel();
        $this->attemptModel = new LoginAttemptModel();
    }

    // ── Vistas ────────────────────────────────────────────────────────────────

    public function showLogin(): void
    {
        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/auth/login.php';
    }

    public function showRegister(): void
    {
        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/auth/register.php';
    }

    // ── Acciones POST ─────────────────────────────────────────────────────────

    public function login(): void
    {
        $this->validateCsrf('/login');

        $email    = strtolower(trim($_POST['email']   ?? ''));
        $password = $_POST['password'] ?? '';  // NUNCA trim() en password
        $ip       = $this->getClientIp();

        // Rate limiting: 5 intentos por email+IP en 15 minutos
        if ($this->attemptModel->countRecent($email, $ip, 15) >= 5) {
            $this->flash('error', 'Demasiados intentos fallidos. Espera 15 minutos e inténtalo de nuevo.');
            $this->redirect('/login');
        }

        // Siempre ejecutar password_verify aunque el usuario no exista (anti timing attack)
        $dummyHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $user      = $this->userModel->findByEmail($email);
        $hash      = $user ? $user['password'] : $dummyHash;
        $valid     = password_verify($password, $hash);

        if (!$user || !$valid) {
            $this->attemptModel->record($email, $ip);
            $this->flash('error', 'Email o contraseña incorrectos.');
            $this->redirect('/login');
        }

        // Login exitoso
        session_regenerate_id(true);
        $_SESSION['user_id']    = (int) $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        $destination = $_SESSION['redirect_after_login'] ?? '/dashboard';
        unset($_SESSION['redirect_after_login']);

        $this->redirect($destination);
    }

    public function register(): void
    {
        $this->validateCsrf('/registro');

        $name            = trim($_POST['name']             ?? '');
        $email           = strtolower(trim($_POST['email'] ?? ''));
        $password        = $_POST['password']              ?? '';
        $confirmPassword = $_POST['confirm_password']      ?? '';
        $ip              = $this->getClientIp();

        // Rate limiting: 3 registros por IP en 1 hora
        if ($this->attemptModel->countRecentByIp($ip, 60) >= 3) {
            $this->flash('error', 'Has alcanzado el límite de registros. Inténtalo más tarde.');
            $this->redirect('/registro');
        }

        // Validaciones server-side
        $errors = $this->validateRegister($name, $email, $password, $confirmPassword);
        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/registro');
        }

        if ($this->userModel->emailExists($email)) {
            $this->flash('error', 'Ese email ya está en uso. ¿Quieres iniciar sesión?');
            $this->redirect('/registro');
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->userModel->create($name, $email, $hashedPassword);

        // Registrar intento de registro para rate limiting (email vacío = identificador de registro)
        $this->attemptModel->record('', $ip);

        $this->flash('success', '¡Cuenta creada! Ya puedes iniciar sesión.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->validateCsrf('/dashboard');

        // Destruir sesión de forma segura
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        $this->redirect('/login');
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function ensureCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function validateCsrf(string $fallback): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
            $this->redirect($fallback);
        }
        // Regenerar tras consumir para prevenir replay attacks
        unset($_SESSION['csrf_token']);
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    private function getClientIp(): string
    {
        // Comprueba primero headers de proxy reverso (Nginx en producción)
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                // X-Forwarded-For puede contener lista separada por comas; tomar el primero
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    private function validateRegister(
        string $name, string $email, string $password, string $confirm
    ): array {
        $errors = [];

        if (mb_strlen($name) < 2 || !preg_match('/^[\p{L}\s]+$/u', $name)) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres y solo puede contener letras.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Introduce un email válido.';
        }
        if (mb_strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una mayúscula.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un número.';
        } elseif (!preg_match('/[\W_]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un carácter especial.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        return $errors;
    }
}
