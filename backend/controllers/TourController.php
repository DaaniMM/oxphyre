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
}
