<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class PositionController extends BaseController
{
    private const WEBP_QUALITY = 92;
    private const MEMORY_SAFETY_BYTES = 33554432; // 32 MB de margen para evitar OOM en GD.
    private const LOW_QUALITY_RECOMMENDATION = 'Recomendación de Oxphyre: evita pasar las fotos por WhatsApp, Instagram u otras apps antes de subirlas, porque pueden reducir la calidad.';

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
        require_once BACKEND_PATH . '/services/MiDaSService.php';

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
        $miDaS         = new MiDaSService();
        $processed     = 0;
        $failed        = 0;
        $warnings      = [];
        $errors        = [];
        $directions    = ['N', 'S', 'E', 'O'];

        foreach ($directions as $dir) {
            $fieldName = "photo_{$dir}";

            // Saltar si no se subió archivo para esta dirección
            if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $result = $this->storeUploadedPhoto(
                $_FILES[$fieldName],
                $uploadDir,
                $dir,
                $dir,
                $miDaS,
                $photoModel,
                (int) $position['id']
            );

            if ($result['success']) {
                $processed++;
                if ($result['warning'] !== '') {
                    $warnings[] = $result['warning'];
                }
            } else {
                $failed++;
                $errors[] = $result['message'];
            }
        }

        // ── Foto panorámica 360° ─────────────────────────────────────────────────
        if (isset($_FILES['photo_360']) && $_FILES['photo_360']['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = $this->storeUploadedPhoto(
                $_FILES['photo_360'],
                $uploadDir,
                '360',
                '360_',
                $miDaS,
                $photoModel,
                (int) $position['id']
            );

            if ($result['success']) {
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
            $this->flash('error', trim($message), !empty($warnings) ? self::LOW_QUALITY_RECOMMENDATION : null);
        } else {
            $message = "{$processed} imagen(es) subidas y optimizadas correctamente.";
            if (!empty($warnings)) {
                $message .= ' ' . implode(' ', array_unique($warnings));
            }
            $this->flash('success', trim($message), !empty($warnings) ? self::LOW_QUALITY_RECOMMENDATION : null);
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

    private function storeUploadedPhoto(
        array $file,
        string $uploadDir,
        string $direction,
        string $filenamePrefix,
        MiDaSService $miDaS,
        PhotoModel $photoModel,
        int $positionId
    ): array {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            error_log("PositionController: error de upload {$file['error']} en {$direction}");
            return $this->uploadResult(false, 'No hemos podido recibir esta imagen. Inténtalo otra vez.');
        }

        if (($file['size'] ?? 0) > MAX_UPLOAD_SIZE) {
            error_log("PositionController: tamaño excedido en {$direction}: {$file['size']}");
            return $this->uploadResult(false, 'Esta imagen es demasiado grande para subirla ahora. Prueba con una versión más ligera.');
        }

        $tmpPath = $file['tmp_name'] ?? '';
        $mime = $this->detectMime($tmpPath);
        if (!in_array($mime, ALLOWED_MIME_TYPES, true)) {
            error_log("PositionController: MIME no permitido en {$direction}: {$mime}");
            return $this->uploadResult(false, 'No hemos podido leer esta imagen. Sube una foto en JPG, PNG o WebP.');
        }

        $dimensions = @getimagesize($tmpPath);
        if (!$dimensions || empty($dimensions[0]) || empty($dimensions[1])) {
            error_log("PositionController: imagen sin dimensiones legibles en {$direction}");
            return $this->uploadResult(false, 'No hemos podido leer esta imagen. Sube una foto en JPG, PNG o WebP.');
        }

        [$width, $height] = [(int) $dimensions[0], (int) $dimensions[1]];
        if (!$this->canProcessWithGd($width, $height)) {
            error_log("PositionController: imagen demasiado grande para GD en {$direction}: {$width}x{$height}");
            return $this->uploadResult(false, 'Esta imagen es demasiado grande para procesarla ahora. Prueba con una versión más ligera.');
        }

        $baseName = uniqid($filenamePrefix, true);
        $filename = $baseName . '.webp';
        $destPath = $uploadDir . $filename;
        $midasTempPath = $uploadDir . $baseName . '_midas.jpg';

        if (!$this->convertToWebp($tmpPath, $mime, $destPath, $midasTempPath)) {
            @unlink($destPath);
            @unlink($midasTempPath);
            error_log("PositionController: conversión WebP fallida en {$direction}");
            return $this->uploadResult(false, 'No hemos podido procesar esta imagen ahora mismo. Inténtalo de nuevo en unos segundos.');
        }

        $depthFile = $this->processDepthMap($miDaS, $midasTempPath, $uploadDir, $baseName);
        @unlink($midasTempPath);

        $photoModel->create(
            $positionId,
            $direction,
            $filename,
            htmlspecialchars($file['name'] ?? ''),
            $depthFile,
            $depthFile !== ''
        );

        return $this->uploadResult(true, '', $this->qualityWarning($direction, $width, $height));
    }

    private function detectMime(string $path): string
    {
        if ($path === '' || !is_file($path)) {
            return '';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($path) ?: '';
    }

    private function convertToWebp(string $sourcePath, string $mime, string $webpPath, string $midasTempPath): bool
    {
        if (!function_exists('imagewebp') || !function_exists('imagejpeg')) {
            error_log('PositionController: GD/WebP no disponible en el servidor');
            return false;
        }

        $image = match ($mime) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($sourcePath) : false,
            'image/png'  => function_exists('imagecreatefrompng') ? @imagecreatefrompng($sourcePath) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default      => false,
        };

        if (!$image) {
            return false;
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $webpOk = imagewebp($image, $webpPath, self::WEBP_QUALITY);
        $jpegOk = imagejpeg($image, $midasTempPath, 92);
        imagedestroy($image);

        return $webpOk && $jpegOk && is_file($webpPath) && filesize($webpPath) > 0;
    }

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

    private function qualityWarning(string $direction, int $width, int $height): string
    {
        $isPanorama = $direction === '360';
        $aspect = $height > 0 ? $width / $height : 0;

        if ($isPanorama && $height < 700) {
            if ($aspect > 3.5) {
                return 'La panorámica se ha subido, pero parece comprimida. Para que el tour se vea mejor, sube la foto original desde la galería del móvil.';
            }
            return 'La panorámica se ha subido, pero parece tener poca resolución. Para que el tour se vea mejor, sube la foto original desde la galería del móvil.';
        }

        if (!$isPanorama && ($width < 1000 || $height < 700)) {
            return 'La imagen se ha subido, pero parece tener poca resolución. Para mejor calidad, sube el archivo original desde la galería del móvil.';
        }

        return '';
    }

    private function canProcessWithGd(int $width, int $height): bool
    {
        $memoryLimit = $this->parseBytes(ini_get('memory_limit'));
        if ($memoryLimit <= 0) {
            return true;
        }

        $estimatedBytes = ($width * $height * 6) + self::MEMORY_SAFETY_BYTES;
        return $estimatedBytes < ($memoryLimit * 0.75);
    }

    private function parseBytes(string|false $value): int
    {
        if ($value === false || $value === '') {
            return 0;
        }

        $value = trim($value);
        if ($value === '-1') {
            return -1;
        }

        $unit = strtolower($value[strlen($value) - 1]);
        $bytes = (int) $value;

        return match ($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }

    private function uploadResult(bool $success, string $message = '', string $warning = ''): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'warning' => $warning,
        ];
    }

    private function go(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }
}
