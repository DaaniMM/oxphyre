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

    public function showManage(): void
    {
        global $routeParams;
        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $routeParams['biz']  ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $routeParams['tour'] ?? '');

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';
        require_once BACKEND_PATH . '/models/QrCodeModel.php';
        require_once BACKEND_PATH . '/models/QrScanModel.php';

        $userId   = (int) ($_SESSION['user_id'] ?? 0);
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);

        if (!$business) {
            $this->flash('error', 'Negocio no encontrado.');
            $this->go('/dashboard/negocios');
        }

        $tourModel = new TourModel();
        $tour      = $tourModel->getBySlugAndBusiness($tourSlug, (int) $business['id']);

        if (!$tour) {
            $this->flash('error', 'Tour no encontrado.');
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $positions = (new PositionModel())->getByTour((int) $tour['id']);
        $panoramaPositionIds = (new PhotoModel())->getPanoramaPositionIdsByTour((int) $tour['id']);
        $positionIdsWithPanorama = array_fill_keys($panoramaPositionIds, true);

        // El dashboard necesita saber si una posicion es visitable sin cargar
        // todas sus fotos. La panoramica 360 activa la experiencia Oxphyre Room;
        // las fotos detalle 1-4 siguen siendo opcionales.
        foreach ($positions as &$pos) {
            $pos['has_panorama'] = isset($positionIdsWithPanorama[(int) $pos['id']]);
        }
        unset($pos);

        // Flechas de navegación pendientes de revisión en este tour.
        // Se usa en la vista para mostrar aviso global y badge en la card de cada posición afectada.
        require_once BACKEND_PATH . '/models/HotspotModel.php';
        $arrowsNeedReviewByPosition = (new HotspotModel())->getPositionsWithNeedsReviewByTour((int) $tour['id']);
        // Mapa indexado por positionId para lookup O(1) en el bucle de cards.
        $positionsWithArrowsNeedReview = array_column($arrowsNeedReviewByPosition, null, 'positionId');

        $qrScanCount = 0;
        $qrCode = (new QrCodeModel())->findByTourId((int) $tour['id']);
        if ($qrCode) {
            $qrScanCount = (new QrScanModel())->countByQrCode((int) $qrCode['id']);
        }

        $this->ensureCsrfToken();

        $userRole    = $_SESSION['user_role'] ?? 'business_free';
        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require_once VIEWS_PATH . '/dashboard/tours/manage.php';
    }

    public function update(): void
    {
        global $routeParams;
        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $routeParams['biz']  ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $routeParams['tour'] ?? '');

        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }
        unset($_SESSION['csrf_token']);

        $userId = (int) ($_SESSION['user_id'] ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';

        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $tourModel = new TourModel();
        $tour      = $tourModel->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $title       = strip_tags(trim($_POST['title']       ?? ''));
        $description = strip_tags(trim($_POST['description'] ?? ''));
        $isPublished = isset($_POST['is_published']) && $_POST['is_published'] === '1';

        $errors = [];
        if ($title === '' || mb_strlen($title) > 100) {
            $errors[] = 'El título es obligatorio y no puede superar 100 caracteres.';
        }
        if ($description !== '' && mb_strlen($description) > 500) {
            $errors[] = 'La descripción no puede superar 500 caracteres.';
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        $tourModel->update(
            (int) $tour['id'],
            $title,
            $description !== '' ? $description : null,
            $isPublished
        );

        $this->flash('success', 'Tour actualizado correctamente.');
        $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
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

    // ── Visor público ────────────────────────────────────────────────────────

    public function showPublic(): void
    {
        global $routeParams;
        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $routeParams['biz']  ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $routeParams['tour'] ?? '');

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';
        require_once BACKEND_PATH . '/models/HotspotModel.php';
        require_once BACKEND_PATH . '/services/PhotoUrlResolver.php';

        // Acceso público: busca el negocio sin filtrar por usuario
        $business = (new BusinessModel())->getBySlugPublic($bizSlug);
        if (!$business) {
            $this->serve404();
        }

        // Solo tours publicados son visibles al público
        $tour = (new TourModel())->getBySlugAndBusinessPublic((int) $business['id'], $tourSlug);
        if (!$tour) {
            $this->serve404();
        }

        // Determinar features disponibles según plan del negocio
        $planId       = (int) $business['plan_id'];
        $hasMiDaS     = $planId >= PLAN_PRO;
        $hasWatermark = $planId <= PLAN_FREE;
        $hasMinimapa  = $planId >= PLAN_PRO;

        // Cargar posiciones con sus fotos organizadas por dirección
        $posModel     = new PositionModel();
        $photoModel   = new PhotoModel();
        $hotspotModel = new HotspotModel();
        $positions    = $posModel->getByTour((int) $tour['id']);

        $tourPositions = [];
        $detailDirections = ['N', 'S', 'E', 'O'];
        foreach ($positions as $pos) {
            $photos     = $photoModel->getByPosition((int) $pos['id']);
            $photosByDir = [];
            foreach ($photos as $photo) {
                $photosByDir[$photo['direction']] = [
                    'url'       => PhotoUrlResolver::resolve($photo, (int) $pos['id']),
                    'processed' => (bool) $photo['processed'],
                ];
            }

            // Oxphyre Room solo es visitable con panoramica principal 360.
            // N/S/E/O se mantienen como slots internos por compatibilidad,
            // pero ahora representan fotos detalle opcionales de 1 a 4.
            if (empty($photosByDir['360'])) {
                continue;
            }

            $hasDetails = false;
            foreach ($detailDirections as $dir) {
                if (!empty($photosByDir[$dir])) {
                    $hasDetails = true;
                    break;
                }
            }

            $tourPositions[] = [
                'id'         => (int) $pos['id'],
                'name'       => $pos['name'],
                'order'      => (int) $pos['order_index'],
                'activeMode' => $pos['active_mode'] ?? '4photos',
                'hasRoom'    => $hasDetails,
                'hasDetails' => $hasDetails,
                'photos'     => $photosByDir,
                'hotspots'   => $hotspotModel->getValidForPublic((int) $pos['id'], (int) $tour['id']),
            ];
        }

        $businessLocation = [
            'hasCoords'  => !empty($business['latitude']) && !empty($business['longitude']),
            'lat'        => !empty($business['latitude'])  ? (float) $business['latitude']  : null,
            'lng'        => !empty($business['longitude']) ? (float) $business['longitude'] : null,
            'address'    => $business['address']     ?? null,
            'city'       => $business['city']        ?? null,
            'postalCode' => $business['postal_code'] ?? null,
            'country'    => $business['country']     ?? null,
        ];

        $tourData = [
            'tourId'    => (int) $tour['id'],
            'tourTitle' => $tour['title'],
            'bizName'   => $business['name'],
            'positions' => $tourPositions,
            'features'  => [
                'midas'     => $hasMiDaS,
                'watermark' => $hasWatermark,
                'minimap'   => $hasMinimapa,
            ],
            'location'  => [
                'hasCoords' => $businessLocation['hasCoords'],
                'lat'       => $businessLocation['lat'],
                'lng'       => $businessLocation['lng'],
            ],
        ];

        require_once VIEWS_PATH . '/tour.php';
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function serve404(): never
    {
        http_response_code(404);
        $view404 = VIEWS_PATH . '/errors/404.php';
        if (file_exists($view404)) {
            require_once $view404;
        } else {
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>404</title></head><body><h1>Tour no encontrado</h1><p><a href="/">Volver al inicio</a></p></body></html>';
        }
        exit();
    }

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
