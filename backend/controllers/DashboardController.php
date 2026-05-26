<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class DashboardController extends BaseController
{
    private static array $planLabels = [
        'business_free'     => 'Free',
        'business_pro'      => 'Pro',
        'business_business' => 'Business',
        'admin'             => 'Admin',
    ];

    // Máximo de negocios por rol. -1 = ilimitado.
    private static array $businessLimits = [
        'business_free'     => 1,
        'business_pro'      => 5,
        'business_business' => -1,
        'admin'             => -1,
    ];

    public function index(): void
    {
        $this->ensureCsrfToken();

        require_once BACKEND_PATH . '/models/DashboardModel.php';
        $model  = new DashboardModel();
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $stats = [
            'tours'      => $model->countTours($userId),
            'businesses' => $model->countBusinesses($userId),
            'qr_scans'   => $model->countQrScansLast30Days($userId),
        ];

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $userRole    = $_SESSION['user_role'] ?? 'business_free';
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        $businessLimit    = self::$businessLimits[$userRole] ?? 1;
        $atBusinessLimit  = $businessLimit !== -1 && $stats['businesses'] >= $businessLimit;

        require_once VIEWS_PATH . '/dashboard/index.php';
    }

    public function showSettings(): void
    {
        $this->ensureCsrfToken();

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $userRole    = $_SESSION['user_role'] ?? 'business_free';
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($userName, 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        require_once VIEWS_PATH . '/dashboard/configuracion.php';
    }
}
