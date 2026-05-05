<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class BusinessController extends BaseController
{
    private static array $planLabels = [
        'business_free'     => 'Free',
        'business_pro'      => 'Pro',
        'business_business' => 'Business',
        'admin'             => 'Admin',
    ];

    private static array $businessLimits = [
        'business_free'     => 1,
        'business_pro'      => 5,
        'business_business' => -1,
        'admin'             => -1,
    ];

    public function showList(): void
    {
        $this->ensureCsrfToken();

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $model  = new BusinessModel();
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $businesses = $model->getByUser($userId);

        $userRole        = $_SESSION['user_role'] ?? 'business_free';
        $businessLimit   = self::$businessLimits[$userRole] ?? 1;
        $atBusinessLimit = $businessLimit !== -1 && count($businesses) >= $businessLimit;

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        require_once VIEWS_PATH . '/dashboard/negocios/index.php';
    }

    public function showCreate(): void
    {
        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $userId   = (int) ($_SESSION['user_id']   ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';

        if ($userRole === 'business_free' && (new BusinessModel())->countByUser($userId) >= 1) {
            $this->flash('error', 'El plan Free solo permite 1 negocio. Actualiza a Pro para crear más.');
            $this->go('/dashboard');
        }

        $this->ensureCsrfToken();

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require_once VIEWS_PATH . '/dashboard/business/create.php';
    }

    public function store(): void
    {
        $this->verifyCsrf('/dashboard/tours/nuevo');

        $userId   = (int) ($_SESSION['user_id']   ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';

        $name        = strip_tags(trim($_POST['name']        ?? ''));
        $slug        = strtolower(preg_replace('/[^a-z0-9-]+/', '', trim($_POST['slug'] ?? '')));
        $slug        = trim($slug, '-');
        $description = strip_tags(trim($_POST['description'] ?? ''));
        $phone       = strip_tags(trim($_POST['phone']       ?? ''));
        $address     = strip_tags(trim($_POST['address']     ?? ''));

        $errors = [];
        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'El nombre es obligatorio y no puede superar 100 caracteres.';
        }
        if (mb_strlen($slug) < 2) {
            $errors[] = 'El slug debe tener al menos 2 caracteres.';
        }
        if ($description !== '' && mb_strlen($description) > 300) {
            $errors[] = 'La descripción no puede superar 300 caracteres.';
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->go('/dashboard/tours/nuevo');
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $model = new BusinessModel();

        if ($userRole === 'business_free' && $model->countByUser($userId) >= 1) {
            $this->flash('error', 'El plan Free solo permite 1 negocio.');
            $this->go('/dashboard');
        }

        if ($model->slugExists($slug)) {
            $this->flash('error', 'Ese slug ya está en uso. Elige otro nombre para la URL del negocio.');
            $this->go('/dashboard/tours/nuevo');
        }

        $businessId = $model->create(
            $userId, $name, $slug,
            $description !== '' ? $description : null,
            $phone       !== '' ? $phone       : null,
            $address     !== '' ? $address     : null
        );

        $_SESSION['created_business'] = ['id' => $businessId, 'name' => $name, 'slug' => $slug];
        $this->go('/dashboard/business/created');
    }

    public function showSuccess(): void
    {
        $business = $_SESSION['created_business'] ?? null;
        if (!$business) {
            $this->go('/dashboard');
        }
        unset($_SESSION['created_business']);

        $this->ensureCsrfToken();

        $userRole    = $_SESSION['user_role'] ?? 'business_free';
        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        require_once VIEWS_PATH . '/dashboard/business/success.php';
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function go(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }

    private function verifyCsrf(string $fallback): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
            $this->go($fallback);
        }
        unset($_SESSION['csrf_token']);
    }
}
