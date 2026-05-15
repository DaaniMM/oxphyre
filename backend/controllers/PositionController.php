<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class PositionController extends BaseController
{
    // Etiquetas de plan para mostrar en el sidebar
    private static array $planLabels = [
        'business_free'     => 'Free',
        'business_pro'      => 'Pro',
        'business_business' => 'Business',
        'admin'             => 'Admin',
    ];

    // Máximo de posiciones por tour según plan (-1 = ilimitado)
    private static array $positionLimits = [
        'business_free'     => 5,
        'business_pro'      => 20,
        'business_business' => -1,
        'admin'             => -1,
    ];

    // ── Mostrar formulario de nueva posición ─────────────────────────────────

    public function showCreate(): void
    {
        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $_GET['negocio'] ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $_GET['tour']    ?? '');

        if ($bizSlug === '' || $tourSlug === '') {
            $this->go('/dashboard/negocios');
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';

        $userId   = (int) ($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';

        // Verificar que el negocio y el tour pertenecen al usuario
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->flash('error', 'Negocio no encontrado.');
            $this->go('/dashboard/negocios');
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->flash('error', 'Tour no encontrado.');
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $this->ensureCsrfToken();

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        require_once VIEWS_PATH . '/dashboard/position/create.php';
    }

    // ── Procesar creación de posición ────────────────────────────────────────

    public function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->go('/dashboard/negocios');
        }
        unset($_SESSION['csrf_token']);

        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $_POST['biz_slug']  ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $_POST['tour_slug'] ?? '');
        $name     = strip_tags(trim($_POST['name'] ?? ''));
        $userId   = (int) ($_SESSION['user_id']   ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';

        if ($name === '' || mb_strlen($name) > 100) {
            $this->flash('error', 'El nombre es obligatorio y no puede superar 100 caracteres.');
            $this->go("/dashboard/posicion/nueva?negocio={$bizSlug}&tour={$tourSlug}");
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';

        // Verificar cadena de propiedad: usuario → negocio → tour
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $tourModel = new TourModel();
        $tour      = $tourModel->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        // Aplicar límite de posiciones según plan
        $posModel   = new PositionModel();
        $count      = $posModel->countByTour((int) $tour['id']);
        $limit      = self::$positionLimits[$userRole] ?? 5;

        if ($limit !== -1 && $count >= $limit) {
            $this->flash('error', "Has alcanzado el límite de {$limit} posiciones por tour en tu plan.");
            $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        // Insertar con order_index = total_actual + 1
        $posModel->create((int) $tour['id'], $name, $count + 1);

        $this->flash('success', "Posición \"{$name}\" creada. Ahora sube las fotos 360°.");
        $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
    }

    // ── Mostrar formulario de subida de fotos ────────────────────────────────

    public function showUpload(): void
    {
        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $_GET['negocio']  ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $_GET['tour']     ?? '');
        $posId    = (int) ($_GET['position'] ?? 0);

        if ($bizSlug === '' || $tourSlug === '' || $posId <= 0) {
            $this->go('/dashboard/negocios');
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';

        $userId   = (int) ($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';

        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->flash('error', 'Negocio no encontrado.');
            $this->go('/dashboard/negocios');
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->flash('error', 'Tour no encontrado.');
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $position = (new PositionModel())->getByIdAndTour($posId, (int) $tour['id']);
        if (!$position) {
            $this->flash('error', 'Posición no encontrada.');
            $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        // Fotos ya subidas para pre-mostrar en el formulario
        $existingPhotos = (new PhotoModel())->getByPosition((int) $position['id']);
        $photosByDir    = [];
        foreach ($existingPhotos as $p) {
            $photosByDir[$p['direction']] = $p;
        }
        // Estado Sprint 1: la panorámica es la vista principal y las 4 fotos
        // activan Oxphyre Room como detalle opcional. active_mode queda heredado.
        $photo360       = $photosByDir['360'] ?? null;
        $roomDirections = ['N', 'S', 'E', 'O'];
        $roomPhotoCount = 0;
        foreach ($roomDirections as $dir) {
            if (!empty($photosByDir[$dir])) {
                $roomPhotoCount++;
            }
        }
        $hasPanorama    = $photo360 !== null;
        $hasOxphyreRoom = $roomPhotoCount === count($roomDirections);
        $activeMode     = $position['active_mode'] ?? '4photos';

        $this->ensureCsrfToken();

        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require_once VIEWS_PATH . '/dashboard/position/upload.php';
    }

    // ── Procesar subida de fotos y llamada a MiDaS ───────────────────────────

    public function upload(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->go('/dashboard/negocios');
        }
        unset($_SESSION['csrf_token']);

        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $_POST['biz_slug']   ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $_POST['tour_slug']  ?? '');
        $posId    = (int) ($_POST['position_id'] ?? 0);
        $userId   = (int) ($_SESSION['user_id']  ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';
        require_once BACKEND_PATH . '/services/ImageProcessingService.php';
        require_once BACKEND_PATH . '/services/MiDaSService.php';
        require_once BACKEND_PATH . '/services/R2StorageService.php';

        // Verificar cadena de propiedad: usuario → negocio → tour → posición
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $posModel = new PositionModel();
        $position = $posModel->getByIdAndTour($posId, (int) $tour['id']);
        if (!$position) {
            $this->flash('error', 'Posición no encontrada.');
            $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        // Directorio de uploads específico de esta posición
        $positionId = (int) $position['id'];
        $uploadDir  = UPLOADS_PATH . '/' . $positionId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $photoModel    = new PhotoModel();
        $imageService  = new ImageProcessingService();
        $miDaS         = new MiDaSService();
        $processed     = 0;
        $failed        = 0;
        $warnings      = [];
        $errors        = [];
        $directions    = ['N', 'S', 'E', 'O'];

        // Procesar primero la panorámica principal: es la vista obligatoria y la
        // petición puede alargarse si se suben también las 4 fotos de Room.
        if (isset($_FILES['photo_360']) && $_FILES['photo_360']['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = $imageService->processUpload(
                $_FILES['photo_360'],
                $uploadDir,
                '360',
                '360_'
            );

            if ($result['success']) {
                $depthFile = $this->processDepthMap($miDaS, $result['midasTempPath'], $uploadDir, pathinfo($result['filename'], PATHINFO_FILENAME));
                @unlink($result['midasTempPath']);
                $storage = $this->resolveStorage(
                    $result['finalPath'],
                    (int) $tour['id'],
                    (int) $position['id'],
                    '360'
                );
                $photoModel->create(
                    (int) $position['id'],
                    '360',
                    $result['filename'],
                    htmlspecialchars($result['originalName']),
                    $depthFile,
                    $depthFile !== '',
                    $storage['storage_provider'],
                    $storage['storage_key'],
                    $storage['public_url']
                );
                $processed++;
                if ($result['warning'] !== '') {
                    $warnings[] = $result['warning'];
                }
            } else {
                $failed++;
                $errors[] = $result['message'];
            }
        }

        foreach ($directions as $dir) {
            $fieldName = "photo_{$dir}";

            // Saltar si no se subió archivo para esta dirección
            if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $result = $imageService->processUpload(
                $_FILES[$fieldName],
                $uploadDir,
                $dir,
                $dir
            );

            if ($result['success']) {
                $depthFile = $this->processDepthMap($miDaS, $result['midasTempPath'], $uploadDir, pathinfo($result['filename'], PATHINFO_FILENAME));
                @unlink($result['midasTempPath']);
                $storage = $this->resolveStorage(
                    $result['finalPath'],
                    (int) $tour['id'],
                    (int) $position['id'],
                    $dir
                );
                $photoModel->create(
                    (int) $position['id'],
                    $dir,
                    $result['filename'],
                    htmlspecialchars($result['originalName']),
                    $depthFile,
                    $depthFile !== '',
                    $storage['storage_provider'],
                    $storage['storage_key'],
                    $storage['public_url']
                );
                $processed++;
                if ($result['warning'] !== '') {
                    $warnings[] = $result['warning'];
                }
            } else {
                $failed++;
                $errors[] = $result['message'];
            }
        }

        if ($processed === 0 && $failed === 0) {
            $this->flash('error', 'No se seleccionó ninguna imagen.');
        } elseif ($failed > 0) {
            $message = "{$processed} imagen(es) subidas, {$failed} no se pudieron procesar. " . implode(' ', array_unique($errors));
            if (!empty($warnings)) {
                $message .= ' ' . implode(' ', array_unique($warnings));
            }
            $this->flash('error', trim($message), !empty($warnings) ? ImageProcessingService::LOW_QUALITY_RECOMMENDATION : null);
        } else {
            $message = "{$processed} imagen(es) subidas y optimizadas correctamente.";
            if (!empty($warnings)) {
                $message .= ' ' . implode(' ', array_unique($warnings));
            }
            $this->flash('success', trim($message), !empty($warnings) ? ImageProcessingService::LOW_QUALITY_RECOMMENDATION : null);
        }

        $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
    }

    // ── Cambiar modo activo del visor (AJAX) ─────────────────────────────────

    public function setActiveMode(): void
    {
        // Respuesta siempre JSON — es un endpoint AJAX
        header('Content-Type: application/json');

        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            echo json_encode(['success' => false, 'error' => 'Token de seguridad inválido.']);
            exit();
        }
        // No consumir el token CSRF aquí: el usuario puede cambiar de modo varias veces
        // sin recargar la página, y no necesitamos protección de doble envío de formulario

        $bizSlug  = preg_replace('/[^a-z0-9-]/', '', $_POST['biz_slug']    ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $_POST['tour_slug']   ?? '');
        $posId    = (int) ($_POST['position_id'] ?? 0);
        $mode     = $_POST['mode'] ?? '';
        $userId   = (int) ($_SESSION['user_id']  ?? 0);

        if (!in_array($mode, ['4photos', 'panoramic'], true)) {
            echo json_encode(['success' => false, 'error' => 'Modo no válido.']);
            exit();
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';

        // Verificar ownership completa: usuario → negocio → tour → posición
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            echo json_encode(['success' => false, 'error' => 'Negocio no encontrado.']);
            exit();
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            echo json_encode(['success' => false, 'error' => 'Tour no encontrado.']);
            exit();
        }

        $posModel = new PositionModel();
        $position = $posModel->getByIdAndTour($posId, (int) $tour['id']);
        if (!$position) {
            echo json_encode(['success' => false, 'error' => 'Posición no encontrada.']);
            exit();
        }

        $posModel->updateActiveMode($posId, $mode);
        echo json_encode(['success' => true]);
        exit();
    }

    // ── Eliminar foto individual ──────────────────────────────────────────────

    public function deletePhoto(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->go('/dashboard/negocios');
        }
        unset($_SESSION['csrf_token']);

        $bizSlug   = preg_replace('/[^a-z0-9-]/', '', $_POST['biz_slug']   ?? '');
        $tourSlug  = preg_replace('/[^a-z0-9-]/', '', $_POST['tour_slug']  ?? '');
        $posId     = (int) ($_POST['position_id'] ?? 0);
        $direction = $_POST['direction'] ?? '';
        $userId    = (int) ($_SESSION['user_id']  ?? 0);

        if ($bizSlug === '' || $tourSlug === '' || $posId <= 0 || !in_array($direction, ['N', 'S', 'E', 'O', '360'], true)) {
            $this->flash('error', 'Solicitud de eliminación inválida.');
            $this->go('/dashboard/negocios');
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';

        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->go("/dashboard/negocios/{$bizSlug}");
        }

        $position = (new PositionModel())->getByIdAndTour($posId, (int) $tour['id']);
        if (!$position) {
            $this->flash('error', 'Posición no encontrada.');
            $this->go("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        $photoModel = new PhotoModel();
        $photo = $photoModel->getByPositionAndDirection((int) $position['id'], $direction);
        if (!$photo) {
            $this->flash('error', 'La foto ya no existe o ya fue eliminada.');
            $this->go("/dashboard/posicion/upload?position={$posId}&negocio={$bizSlug}&tour={$tourSlug}");
        }

        $photoModel->softDeleteByPositionAndDirection((int) $position['id'], $direction);
        $this->flash('success', 'Foto eliminada correctamente.');
        $this->go("/dashboard/posicion/upload?position={$posId}&negocio={$bizSlug}&tour={$tourSlug}");
    }

    // ── Helper privado ────────────────────────────────────────────────────────

    private function processDepthMap(MiDaSService $miDaS, string $imagePath, string $uploadDir, string $baseName): string
    {
        if (!is_file($imagePath)) {
            return '';
        }

        $depthB64 = $miDaS->process($imagePath);
        if ($depthB64 === null) {
            return '';
        }

        $depthFile = 'depth_' . $baseName . '.png';
        $depthDecoded = base64_decode($depthB64);

        if ($depthDecoded === false || file_put_contents($uploadDir . $depthFile, $depthDecoded) === false) {
            error_log("PositionController: no se pudo guardar depth map en {$uploadDir}{$depthFile}");
            return '';
        }

        return $depthFile;
    }

    // Fase 2A: cada WebP visible se mantiene en local y, si R2 esta activo,
    // se intenta duplicar en Cloudflare R2. Esta funcion nunca debe romper la
    // subida: ante cualquier fallo vuelve a local y deja el visor actual intacto.
    private function resolveStorage(string $localWebpPath, int $tourId, int $positionId, string $direction): array
    {
        $localStorage = [
            'storage_provider' => 'local',
            'storage_key' => null,
            'public_url' => null,
        ];

        if (($_ENV['R2_ENABLED'] ?? '') !== 'true') {
            return $localStorage;
        }

        if (!is_file($localWebpPath)) {
            error_log("[R2] PositionController: omitido porque no existe el WebP local {$localWebpPath}");
            return $localStorage;
        }

        try {
            $r2 = new R2StorageService();
            $key = $this->buildR2Key($tourId, $positionId, $direction);

            if (!$r2->upload($localWebpPath, $key)) {
                error_log("[R2] PositionController: upload fallo para {$key}; se mantiene almacenamiento local");
                return $localStorage;
            }

            return [
                'storage_provider' => 'r2',
                'storage_key' => $key,
                'public_url' => $r2->getPublicUrl($key),
            ];
        } catch (Throwable $e) {
            error_log('[R2] PositionController: no disponible, fallback local. ' . $e->getMessage());
            return $localStorage;
        }
    }

    // Las keys no se reutilizan: el sufijo aleatorio evita colisiones y problemas
    // de cache en Cloudflare si una foto se sustituye. El WebP local se conserva
    // como fallback hasta una futura Fase 3 de limpieza.
    private function buildR2Key(int $tourId, int $positionId, string $direction): string
    {
        $random = bin2hex(random_bytes(8));

        return "tours/{$tourId}/positions/{$positionId}/{$direction}/{$direction}_{$random}.webp";
    }

    private function go(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }
}
