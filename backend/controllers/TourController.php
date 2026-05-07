<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class TourController extends BaseController
{
    private static array $planLabels = [
        'business_free'     => 'Free',
        'business_pro'      => 'Pro',
        'business_business' => 'Business',
        'admin'             => 'Admin',
    ];

    public function showList(): void
    {
        $this->ensureCsrfToken();

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/DashboardModel.php';

        $userId      = (int) ($_SESSION['user_id'] ?? 0);
        $tourModel   = new TourModel();
        $businesses  = (new BusinessModel())->getByUser($userId);

        foreach ($businesses as &$biz) {
            $biz['tours'] = $tourModel->getByBusiness((int) $biz['id']);
        }
        unset($biz);

        $dashModel = new DashboardModel();
        $stats = [
            'businesses' => count($businesses),
            'tours'      => $dashModel->countTours($userId),
            'qr_scans'   => $dashModel->countQrScansLast30Days($userId),
        ];

        $userRole    = $_SESSION['user_role'] ?? 'business_free';
        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        require_once VIEWS_PATH . '/dashboard/tours/index.php';
    }

    public function showCreate(): void
    {
        $bizSlug = preg_replace('/[^a-z0-9-]/', '', $_GET['negocio'] ?? '');

        if ($bizSlug === '') {
            $this->go('/dashboard/negocios');
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/DashboardModel.php';

        $userId   = (int) ($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);

        if (!$business) {
            $this->flash('error', 'Negocio no encontrado.');
            $this->go('/dashboard/negocios');
        }

        // Verificar límite de tours según plan
        if ($userRole === 'business_free') {
            $total = (new DashboardModel())->countTours($userId);
            if ($total >= 1) {
                $this->flash('error', 'El plan Free solo permite 1 tour. Mejora a Pro para crear más.');
                $this->go("/dashboard/negocios/{$bizSlug}");
            }
        } elseif ($userRole === 'business_pro') {
            $inBiz = (new TourModel())->countByBusiness((int) $business['id']);
            if ($inBiz >= 20) {
                $this->flash('error', 'Has alcanzado el límite de 20 tours por negocio en el plan Pro.');
                $this->go("/dashboard/negocios/{$bizSlug}");
            }
        }

        $this->ensureCsrfToken();

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require_once VIEWS_PATH . '/dashboard/tours/create.php';
    }

    public function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
            $this->go('/dashboard/negocios');
        }
        unset($_SESSION['csrf_token']);

        $bizSlug = preg_replace('/[^a-z0-9-]/', '', $_POST['business_slug'] ?? '');
        $userId  = (int) ($_SESSION['user_id'] ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);

        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $title       = strip_tags(trim($_POST['title']       ?? ''));
        $description = strip_tags(trim($_POST['description'] ?? ''));
        $slugInput   = strtolower(preg_replace('/[^a-z0-9-]+/', '', trim($_POST['slug'] ?? '')));
        $slugInput   = trim($slugInput, '-');

        $errors = [];
        if ($title === '' || mb_strlen($title) > 100) {
            $errors[] = 'El título es obligatorio y no puede superar 100 caracteres.';
        }
        if ($description !== '' && mb_strlen($description) > 500) {
            $errors[] = 'La descripción no puede superar 500 caracteres.';
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->go("/dashboard/tours/nuevo?negocio={$bizSlug}");
        }

        // Usar slug del input si tiene al menos 2 chars, si no generar desde título
        $baseSlug = mb_strlen($slugInput) >= 2 ? $slugInput : $this->slugify($title);
        if ($baseSlug === '') $baseSlug = 'tour';

        require_once BACKEND_PATH . '/models/TourModel.php';
        $tourModel = new TourModel();

        $slug = $baseSlug;
        $i    = 2;
        while ($tourModel->slugExistsInBusiness((int) $business['id'], $slug)) {
            $slug = $baseSlug . '-' . $i++;
        }

        $tourModel->create(
            (int) $business['id'],
            $title,
            $description !== '' ? $description : null,
            $slug
        );

        $this->flash('success', "Tour \"{$title}\" creado correctamente.");
        $this->go("/dashboard/negocios/{$bizSlug}");
    }

    public function delete(): void
    {
        global $routeSlug;
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $routeSlug ?? '');

        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->go('/dashboard/negocios');
        }
        unset($_SESSION['csrf_token']);

        $bizSlug = preg_replace('/[^a-z0-9-]/', '', $_POST['biz_slug'] ?? '');
        $userId  = (int) ($_SESSION['user_id'] ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';

        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $tourModel = new TourModel();
        $tour      = $tourModel->getBySlugAndBusiness($tourSlug, (int) $business['id']);

        if (!$tour) {
            $this->flash('error', 'Tour no encontrado.');
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $tourModel->softDelete((int) $tour['id']);

        $this->flash('success', "Tour \"{$tour['title']}\" eliminado correctamente.");
        $this->go("/dashboard/negocios/{$bizSlug}");
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function slugify(string $str): string
    {
        $str = mb_strtolower(trim($str));
        $str = preg_replace('/[àáâãäå]/u', 'a', $str);
        $str = preg_replace('/[èéêë]/u',   'e', $str);
        $str = preg_replace('/[ìíîï]/u',   'i', $str);
        $str = preg_replace('/[òóôõö]/u',  'o', $str);
        $str = preg_replace('/[ùúûü]/u',   'u', $str);
        $str = preg_replace('/[ñ]/u',      'n', $str);
        $str = preg_replace('/[^a-z0-9]+/', '-', $str);
        return trim($str, '-');
    }

    private function go(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }
}
