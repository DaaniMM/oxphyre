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
        // Foto panorámica separada para la sección 360° de la vista
        $photo360   = $photosByDir['360'] ?? null;
        $activeMode = $position['active_mode'] ?? '4photos';

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
        $directions    = ['N', 'S', 'E', 'O'];

        foreach ($directions as $dir) {
            $fieldName = "photo_{$dir}";

            // Saltar si no se subió archivo para esta dirección
            if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file    = $_FILES[$fieldName];
            $tmpPath = $file['tmp_name'];

            // Validar tamaño
            if ($file['size'] > MAX_UPLOAD_SIZE) {
                $failed++;
                continue;
            }

            // Validar tipo MIME real con finfo (nunca confiar en la extensión)
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($tmpPath);

            if (!in_array($mime, ALLOWED_MIME_TYPES, true)) {
                $failed++;
                continue;
            }

            // Generar nombre único para evitar colisiones y ocultar el nombre original
            $ext      = ($mime === 'image/png') ? 'png' : 'jpg';
            $filename = uniqid('', true) . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (!move_uploaded_file($tmpPath, $destPath)) {
                error_log("PositionController: no se pudo mover {$tmpPath} a {$destPath}");
                $failed++;
                continue;
            }

            // Mejorar calidad con CLAHE antes de pasar a MiDaS (fallo silencioso)
            $enhanced = $miDaS->enhance($destPath);
            if ($enhanced !== null) {
                $decoded = base64_decode($enhanced);
                if ($decoded !== false) {
                    file_put_contents($destPath, $decoded);
                }
            }

            // Procesar con MiDaS y guardar el mapa de profundidad
            $depthB64  = $miDaS->process($destPath);
            $depthFile = '';

            if ($depthB64 !== null) {
                $depthFile    = 'depth_' . pathinfo($filename, PATHINFO_FILENAME) . '.png';
                $depthPath    = $uploadDir . '/' . $depthFile;
                $depthDecoded = base64_decode($depthB64);

                if ($depthDecoded === false || file_put_contents($depthPath, $depthDecoded) === false) {
                    error_log("PositionController: no se pudo guardar depth map en {$depthPath}");
                    $depthFile = '';
                }
            }

            // Registrar la foto en BD (processed=true solo si hay depth map)
            $photoModel->create(
                (int) $position['id'],
                $dir,
                $filename,
                htmlspecialchars($file['name']),
                $depthFile,
                $depthFile !== ''
            );

            $processed++;
        }

        // ── Foto panorámica 360° ─────────────────────────────────────────────────
        if (isset($_FILES['photo_360']) && $_FILES['photo_360']['error'] === UPLOAD_ERR_OK) {
            $file    = $_FILES['photo_360'];
            $tmpPath = $file['tmp_name'];

            $validSize = $file['size'] <= MAX_UPLOAD_SIZE;
            $finfo     = new finfo(FILEINFO_MIME_TYPE);
            $mime      = $finfo->file($tmpPath);
            $validMime = in_array($mime, ALLOWED_MIME_TYPES, true);

            if ($validSize && $validMime) {
                $ext      = ($mime === 'image/png') ? 'png' : 'jpg';
                $filename = uniqid('360_', true) . '.' . $ext;
                $destPath = $uploadDir . $filename;

                if (move_uploaded_file($tmpPath, $destPath)) {
                    // Mejorar calidad con CLAHE (fallo silencioso)
                    $enhanced = $miDaS->enhance($destPath);
                    if ($enhanced !== null) {
                        $decoded = base64_decode($enhanced);
                        if ($decoded !== false) {
                            file_put_contents($destPath, $decoded);
                        }
                    }

                    $depthB64  = $miDaS->process($destPath);
                    $depthFile = '';

                    if ($depthB64 !== null) {
                        $depthFile    = 'depth_' . pathinfo($filename, PATHINFO_FILENAME) . '.png';
                        $depthDecoded = base64_decode($depthB64);

                        if ($depthDecoded === false || file_put_contents($uploadDir . $depthFile, $depthDecoded) === false) {
                            error_log("PositionController: no se pudo guardar depth map 360 en {$uploadDir}{$depthFile}");
                            $depthFile = '';
                        }
                    }

                    // direction='360' identifica esta foto como panorámica en BD
                    $photoModel->create(
                        (int) $position['id'],
                        '360',
                        $filename,
                        htmlspecialchars($file['name']),
                        $depthFile,
                        $depthFile !== ''
                    );
                    $processed++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        if ($processed === 0 && $failed === 0) {
            $this->flash('error', 'No se subió ningún archivo.');
        } elseif ($failed > 0) {
            $this->flash('error', "{$processed} foto(s) procesadas, {$failed} rechazadas (tipo MIME inválido o tamaño excedido).");
        } else {
            $this->flash('success', "{$processed} foto(s) procesadas con MiDaS correctamente.");
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

    // ── Helper privado ────────────────────────────────────────────────────────

    private function go(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }
}
