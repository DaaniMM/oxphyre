<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

/**
 * AdminController — panel de supervisión solo lectura para el rol admin.
 *
 * Acceso: requiere sesión activa (guard 'auth' en web.php) Y rol 'admin'.
 * Si el usuario tiene sesión pero no es admin, redirige al dashboard con error.
 * No expone ninguna acción de escritura ni de borrado en esta versión inicial.
 */
class AdminController extends BaseController
{
    public function index(): void
    {
        // Segunda capa de acceso: el guard 'auth' solo verifica sesión activa.
        // Este check garantiza que solo usuarios con rol 'admin' pueden continuar.
        // Un usuario business_free/pro/business con sesión válida sería bloqueado aquí.
        $userRole = $_SESSION['user_role'] ?? '';
        if ($userRole !== 'admin') {
            $this->flash('error', 'Acceso restringido. Esta sección es solo para administradores.');
            header('Location: /dashboard');
            exit();
        }

        $this->ensureCsrfToken();

        require_once BACKEND_PATH . '/models/AdminModel.php';
        $adminModel = new AdminModel();

        // Contadores globales de la plataforma
        $stats = [
            'users'      => $adminModel->countUsers(),
            'businesses' => $adminModel->countBusinesses(),
            'tours'      => $adminModel->countTours(),
            'positions'  => $adminModel->countPositions(),
            'photos'     => $adminModel->countPhotos(),
            'qr_codes'   => $adminModel->countQrCodes(),
            'qr_scans'   => $adminModel->countQrScans(),
        ];

        // Listados recientes para supervisión (solo lectura)
        $latestUsers      = $adminModel->getLatestUsers(10);
        $latestBusinesses = $adminModel->getLatestBusinesses(10);
        $latestTours      = $adminModel->getLatestTours(10);

        // Variables de layout sidebar
        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = 'Admin';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'A', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        require_once VIEWS_PATH . '/dashboard/admin/index.php';
    }
}
