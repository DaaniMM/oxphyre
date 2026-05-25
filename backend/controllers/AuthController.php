<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class AuthController extends BaseController
{
    private UserModel $userModel;
    private LoginAttemptModel $attemptModel;

    private static array $planLabels = [
        'free'     => 'Free',
        'pro'      => 'Pro',
        'business' => 'Business',
    ];

    private static array $planRoles = [
        'free'     => 'business_free',
        'pro'      => 'business_pro',
        'business' => 'business_business',
    ];

    public function __construct()
    {
        require_once BACKEND_PATH . '/models/UserModel.php';
        require_once BACKEND_PATH . '/models/LoginAttemptModel.php';
        $this->userModel    = new UserModel();
        $this->attemptModel = new LoginAttemptModel();
    }

    // ── Vistas GET ────────────────────────────────────────────────────────────

    public function showLogin(): void
    {
        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/auth/login.php';
    }

    public function showRegister(): void
    {
        $selectedPlan      = $this->normalizeSelectedPlan($_GET['plan'] ?? '');
        $selectedPlanLabel = self::$planLabels[$selectedPlan];

        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/auth/register.php';
    }

    public function showRecover(): void
    {
        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/auth/recover.php';
    }

    public function showReset(): void
    {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $this->flash('error', 'Enlace de restablecimiento inválido.');
            $this->redirect('/recover');
        }
        if (!$this->userModel->findByResetToken($token)) {
            $this->flash('error', 'El enlace ha expirado o no es válido. Solicita uno nuevo.');
            $this->redirect('/recover');
        }
        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/auth/reset.php';
    }

    public function verifyEmail(): void
    {
        $token    = trim($_GET['token'] ?? '');
        $verified = !empty($token) && $this->userModel->verifyEmail($token);
        require_once VIEWS_PATH . '/auth/verify.php';
    }

    // ── Acciones POST ─────────────────────────────────────────────────────────

    public function login(): void
    {
        $this->validateCsrf('/login');

        $email    = strtolower(trim($_POST['email']   ?? ''));
        $password = $_POST['password'] ?? '';
        $ip       = $this->getClientIp();

        // Rate limiting: 5 intentos por email+IP en 15 minutos
        if ($this->attemptModel->countRecent($email, $ip, 15) >= 5) {
            $this->flash('error', 'Demasiados intentos fallidos. Espera 15 minutos e inténtalo de nuevo.');
            $this->redirect('/login');
        }

        // Anti timing attack: siempre ejecutar password_verify aunque el email no exista
        $dummyHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $user      = $this->userModel->findByEmail($email);
        $hash      = $user ? $user['password'] : $dummyHash;
        $valid     = password_verify($password, $hash);

        if (!$user || !$valid) {
            $this->attemptModel->record($email, $ip);
            $this->flash('error', 'Email o contraseña incorrectos.');
            $this->redirect('/login');
        }

        // Verificación de email obligatoria antes de permitir login
        if (!(bool) $user['email_verified']) {
            $this->flash('error', 'Debes verificar tu email antes de iniciar sesión. Revisa tu bandeja de entrada.');
            $this->redirect('/login');
        }

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
        $selectedPlan = $this->normalizeSelectedPlan($_POST['plan'] ?? '');
        $registerPath = '/registro?plan=' . urlencode($selectedPlan);

        $this->validateCsrf($registerPath);

        $name            = strip_tags(trim($_POST['name']             ?? ''));
        $email           = strtolower(trim($_POST['email']            ?? ''));
        $password        = $_POST['password']                         ?? '';
        $confirmPassword = $_POST['confirm_password']                 ?? '';
        $ip              = $this->getClientIp();
        $role            = self::$planRoles[$selectedPlan] ?? self::$planRoles['free'];

        // Rate limiting: 3 registros por IP en 1 hora
        if ($this->attemptModel->countRecentByIp($ip, 60) >= 3) {
            $this->flash('error', 'Has alcanzado el límite de registros. Inténtalo más tarde.');
            $this->redirect($registerPath);
        }

        $errors = $this->validateRegister($name, $email, $password, $confirmPassword);
        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect($registerPath);
        }

        if ($this->userModel->emailExists($email)) {
            $this->flash('error', 'Ese email ya está en uso. ¿Quieres iniciar sesión?');
            $this->redirect($registerPath);
        }

        $hashedPassword    = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $verificationToken = bin2hex(random_bytes(32));

        // Seleccion publica de plan para demo/pre-lanzamiento.
        // En producto real, Pro y Business deberian pasar por checkout antes de activar el rol.
        $this->userModel->create($name, $email, $hashedPassword, $verificationToken, $role);
        $this->attemptModel->record('', $ip);

        // Enviar email de verificación (fallo silencioso: cuenta creada igualmente)
        try {
            $this->getEmailService()->sendVerification($email, $name, $verificationToken);
        } catch (\Throwable $e) {
            error_log('register() email error: ' . $e->getMessage());
        }

        $this->flash('success', '¡Cuenta creada! Revisa tu email para verificar tu dirección antes de iniciar sesión.');
        $this->redirect('/login');
    }

    public function recover(): void
    {
        $this->validateCsrf('/recover');

        $email = strtolower(trim($_POST['email'] ?? ''));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);
                $this->userModel->saveResetToken($email, $token, $expires);
                try {
                    $this->getEmailService()->sendPasswordReset($email, $user['name'], $token);
                } catch (\Throwable $e) {
                    error_log('recover() email error: ' . $e->getMessage());
                }
            }
        }

        // Mismo mensaje siempre — previene enumeración de emails
        $this->flash('success', 'Si ese email está registrado, recibirás las instrucciones en breve.');
        $this->redirect('/recover');
    }

    public function reset(): void
    {
        $token = trim($_POST['token'] ?? '');
        $this->validateCsrf('/recover');

        if (empty($token)) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/recover');
        }

        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            $this->flash('error', 'El enlace ha expirado o no es válido. Solicita uno nuevo.');
            $this->redirect('/recover');
        }

        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $errors   = $this->validateNewPassword($password, $confirm);

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/reset?token=' . urlencode($token));
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->userModel->updatePassword((int) $user['id'], $hashed);

        $this->flash('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        // Fallback '/' en lugar de '/dashboard' para evitar redirect loop
        // si la sesión está en estado inconsistente durante el fallo CSRF
        $this->validateCsrf('/');

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

    private function getEmailService(): EmailService
    {
        require_once BACKEND_PATH . '/services/EmailService.php';
        return new EmailService();
    }

    private function normalizeSelectedPlan(mixed $plan): string
    {
        if (!is_string($plan)) {
            return 'free';
        }

        $plan = strtolower(trim($plan));
        return array_key_exists($plan, self::$planLabels) ? $plan : 'free';
    }

    private function validateCsrf(string $fallback): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
            $this->redirect($fallback);
        }
        unset($_SESSION['csrf_token']);
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    private function getClientIp(): string
    {
        // En producción los headers X-Forwarded-For y CF-Connecting-IP están
        // limpiados por Nginx (fastcgi_param ... ""), por lo que solo REMOTE_ADDR
        // llega aquí. Esta cadena de fallbacks es para entornos de desarrollo.
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    private function validateRegister(string $name, string $email, string $password, string $confirm): array
    {
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

    private function validateNewPassword(string $password, string $confirm): array
    {
        $errors = [];
        if (mb_strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Necesita al menos una mayúscula.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Necesita al menos un número.';
        } elseif (!preg_match('/[\W_]/', $password)) {
            $errors[] = 'Necesita al menos un carácter especial.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }
        return $errors;
    }
}
