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

    public function showManage(): void
    {
        global $routeSlug;
        $slug = preg_replace('/[^a-z0-9-]/', '', $routeSlug ?? '');

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';

        $userId   = (int) ($_SESSION['user_id'] ?? 0);
        $model    = new BusinessModel();
        $business = $model->getBySlug($slug, $userId);

        if (!$business) {
            $this->flash('error', 'Negocio no encontrado.');
            $this->go('/dashboard/negocios');
        }

        $tours = (new TourModel())->getByBusiness((int) $business['id']);

        $this->ensureCsrfToken();

        $userRole    = $_SESSION['user_role'] ?? 'business_free';
        $userName    = htmlspecialchars($_SESSION['user_name']  ?? '');
        $userEmail   = htmlspecialchars($_SESSION['user_email'] ?? '');
        $planLabel   = self::$planLabels[$userRole] ?? 'Free';
        $userInitial = mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
        $csrfToken   = htmlspecialchars($_SESSION['csrf_token'] ?? '');

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require_once VIEWS_PATH . '/dashboard/negocios/manage.php';
    }

    public function update(): void
    {
        global $routeSlug;
        $slug = preg_replace('/[^a-z0-9-]/', '', $routeSlug ?? '');

        $this->verifyCsrf("/dashboard/negocios/{$slug}");

        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $name        = strip_tags(trim($_POST['name']        ?? ''));
        $description = strip_tags(trim($_POST['description'] ?? ''));
        $phone       = strip_tags(trim($_POST['phone']       ?? ''));
        $address     = strip_tags(trim($_POST['address']     ?? ''));
        $city        = strip_tags(trim($_POST['city']        ?? ''));
        $postalCode  = strip_tags(trim($_POST['postal_code'] ?? ''));
        $country     = strip_tags(trim($_POST['country']     ?? ''));

        $errors = [];
        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'El nombre es obligatorio y no puede superar 100 caracteres.';
        }
        if ($description !== '' && mb_strlen($description) > 300) {
            $errors[] = 'La descripción no puede superar 300 caracteres.';
        }
        if ($city !== '' && mb_strlen($city) > 100) {
            $errors[] = 'La ciudad no puede superar 100 caracteres.';
        }
        if ($postalCode !== '' && mb_strlen($postalCode) > 20) {
            $errors[] = 'El código postal no puede superar 20 caracteres.';
        }
        if ($country !== '' && mb_strlen($country) > 100) {
            $errors[] = 'El país no puede superar 100 caracteres.';
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->go("/dashboard/negocios/{$slug}");
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $model    = new BusinessModel();
        $business = $model->getBySlug($slug, $userId);

        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        $model->update(
            (int) $business['id'],
            $name,
            $description !== '' ? $description : null,
            $phone       !== '' ? $phone       : null,
            $address     !== '' ? $address     : null,
            $city        !== '' ? $city        : null,
            $postalCode  !== '' ? $postalCode  : null,
            $country     !== '' ? $country     : null
        );

        $this->flash('success', 'Negocio actualizado correctamente.');
        $this->go("/dashboard/negocios/{$slug}");
    }

    public function delete(): void
    {
        global $routeSlug;
        $slug = preg_replace('/[^a-z0-9-]/', '', $routeSlug ?? '');

        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->go('/dashboard/negocios');
        }
        unset($_SESSION['csrf_token']);

        $userId = (int) ($_SESSION['user_id'] ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';

        $model    = new BusinessModel();
        $business = $model->getBySlug($slug, $userId);

        if (!$business) {
            $this->go('/dashboard/negocios');
        }

        (new TourModel())->softDeleteByBusiness((int) $business['id']);
        $model->softDelete((int) $business['id']);

        $this->flash('success', 'Negocio eliminado correctamente.');
        $this->go('/dashboard/negocios');
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
        $this->verifyCsrf('/dashboard/negocios/nuevo');

        $userId   = (int) ($_SESSION['user_id']   ?? 0);
        $userRole = $_SESSION['user_role'] ?? 'business_free';

        $name        = strip_tags(trim($_POST['name']        ?? ''));
        $slug        = strtolower(preg_replace('/[^a-z0-9-]+/', '', trim($_POST['slug'] ?? '')));
        $slug        = trim($slug, '-');
        $description = strip_tags(trim($_POST['description'] ?? ''));
        $phone       = strip_tags(trim($_POST['phone']       ?? ''));
        $address     = strip_tags(trim($_POST['address']     ?? ''));
        $city        = strip_tags(trim($_POST['city']        ?? ''));
        $postalCode  = strip_tags(trim($_POST['postal_code'] ?? ''));
        $country     = strip_tags(trim($_POST['country']     ?? ''));

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
        if ($city !== '' && mb_strlen($city) > 100) {
            $errors[] = 'La ciudad no puede superar 100 caracteres.';
        }
        if ($postalCode !== '' && mb_strlen($postalCode) > 20) {
            $errors[] = 'El código postal no puede superar 20 caracteres.';
        }
        if ($country !== '' && mb_strlen($country) > 100) {
            $errors[] = 'El país no puede superar 100 caracteres.';
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->go('/dashboard/negocios/nuevo');
        }

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $model = new BusinessModel();

        if ($userRole === 'business_free' && $model->countByUser($userId) >= 1) {
            $this->flash('error', 'El plan Free solo permite 1 negocio.');
            $this->go('/dashboard');
        }

        if ($model->slugExists($slug)) {
            $this->flash('error', 'Ese slug ya está en uso. Elige otro nombre para la URL del negocio.');
            $this->go('/dashboard/negocios/nuevo');
        }

        $businessId = $model->create(
            $userId, $name, $slug,
            $description !== '' ? $description : null,
            $phone       !== '' ? $phone       : null,
            $address     !== '' ? $address     : null,
            $city        !== '' ? $city        : null,
            $postalCode  !== '' ? $postalCode  : null,
            $country     !== '' ? $country     : null
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

    // ── Geocodificación con Nominatim/OpenStreetMap ───────────────────────────

    public function geocode(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        global $routeSlug;
        $slug = preg_replace('/[^a-z0-9-]/', '', $routeSlug ?? '');

        $input = $this->parseJsonBody();
        $token = (string) ($input['csrf_token'] ?? '');

        // Valida CSRF sin consumirlo: el form de edición también lo necesita.
        if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->geocodeJson(false, 'Token de seguridad inválido.');
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        $model    = new BusinessModel();
        $business = $model->getBySlug($slug, $userId);

        if (!$business) {
            $this->geocodeJson(false, 'Negocio no encontrado.');
        }

        // Usar los valores enviados desde el formulario, no los de BD,
        // para geocodificar lo que el usuario ve aunque no haya guardado aún.
        $address    = mb_substr(strip_tags(trim((string) ($input['address']     ?? ''))), 0, 200);
        $city       = mb_substr(strip_tags(trim((string) ($input['city']        ?? ''))), 0, 100);
        $postalCode = mb_substr(strip_tags(trim((string) ($input['postal_code'] ?? ''))), 0, 20);
        $country    = mb_substr(strip_tags(trim((string) ($input['country']     ?? ''))), 0, 100);

        if ($address === '' && $city === '') {
            $this->geocodeJson(false, 'Añade al menos la dirección o la ciudad antes de buscar en el mapa.');
        }

        $parts = array_values(array_filter([$address, $postalCode, $city, $country], fn($v) => $v !== ''));

        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q'              => implode(', ', $parts),
            'format'         => 'json',
            'limit'          => 1,
            'addressdetails' => 0,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            // User-Agent obligatorio por los términos de uso de Nominatim.
            CURLOPT_USERAGENT      => 'Oxphyre/1.0 (TFG tour virtual; https://oxphyre.com)',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw      = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $curlErr !== '' || $httpCode !== 200) {
            $this->geocodeJson(false, 'No hemos podido conectar con el servicio de mapas. Inténtalo de nuevo en unos segundos.');
        }

        $results = json_decode((string) $raw, true);

        if (!is_array($results) || empty($results[0])) {
            $this->geocodeJson(false, 'No hemos encontrado esa dirección. Prueba a escribirla de otra forma o añade la ciudad al final.');
        }

        $lat = (float) ($results[0]['lat'] ?? 0);
        $lng = (float) ($results[0]['lon'] ?? 0);

        if (!is_finite($lat) || !is_finite($lng)
            || ($lat === 0.0 && $lng === 0.0)
            || $lat < -90 || $lat > 90
            || $lng < -180 || $lng > 180) {
            $this->geocodeJson(false, 'No hemos encontrado esa dirección. Prueba a escribirla de otra forma o añade la ciudad al final.');
        }

        $model->saveGeocoding(
            (int) $business['id'],
            $address    !== '' ? $address    : null,
            $city       !== '' ? $city       : null,
            $postalCode !== '' ? $postalCode : null,
            $country    !== '' ? $country    : null,
            $lat,
            $lng,
            'nominatim'
        );

        $this->geocodeJson(true, 'Ubicación encontrada. Ya podremos mostrarla en tu tour.');
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function parseJsonBody(): array
    {
        $raw  = file_get_contents('php://input');
        $json = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        return is_array($json) ? array_merge($_POST, $json) : $_POST;
    }

    private function geocodeJson(bool $success, string $message): never
    {
        echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
        exit();
    }

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
