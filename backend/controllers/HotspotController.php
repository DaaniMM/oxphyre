<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class HotspotController extends BaseController
{
    private const GENERIC_SAVE_ERROR = 'No hemos podido guardar la flecha. Inténtalo de nuevo.';

    private array $body = [];

    public function showList(): void
    {
        $this->jsonHeader();

        $context = $this->resolveOwnedContext($_GET);
        if (!$context['success']) {
            $this->json($context);
        }

        require_once BACKEND_PATH . '/models/HotspotModel.php';

        $hotspotModel = new HotspotModel();
        $positionId = (int) $context['position']['id'];
        $tourId = (int) $context['tour']['id'];

        $this->json([
            'success' => true,
            'message' => 'Flechas de navegación cargadas.',
            'data' => [
                'arrows' => $hotspotModel->listDashboardByPosition($positionId, $tourId),
                'targets' => $this->getAvailableTargets($tourId, $positionId),
            ],
        ]);
    }

    public function create(): void
    {
        $this->jsonHeader();
        $input = $this->postInput();

        if (!$this->isValidCsrf($input)) {
            $this->json(['success' => false, 'message' => 'Token de seguridad inválido.']);
        }

        $context = $this->resolveOwnedContext($input);
        if (!$context['success']) {
            $this->json($context);
        }

        $texture = $this->resolveTexture($input);
        if (!$texture['success']) {
            $this->json($texture);
        }

        $target = $this->resolveTargetPosition($context, (int) ($input['target_position_id'] ?? 0));
        if (!$target['success']) {
            $this->json($target);
        }

        require_once BACKEND_PATH . '/models/HotspotModel.php';

        $hotspotId = (new HotspotModel())->createNavigationFromTexture([
            'position_id' => (int) $context['position']['id'],
            'target_position_id' => (int) $target['position']['id'],
            'panorama_photo_id' => (int) $context['panorama']['id'],
            'texture_x' => $texture['x'],
            'texture_y' => $texture['y'],
            'label' => $input['label'] ?? $target['position']['name'],
            'is_active' => 1,
        ]);

        if ($hotspotId <= 0) {
            $this->json(['success' => false, 'message' => self::GENERIC_SAVE_ERROR]);
        }

        $this->json([
            'success' => true,
            'message' => 'Flecha guardada correctamente.',
            'data' => ['id' => $hotspotId],
        ]);
    }

    public function move(): void
    {
        $this->jsonHeader();
        $input = $this->postInput();

        if (!$this->isValidCsrf($input)) {
            $this->json(['success' => false, 'message' => 'Token de seguridad inválido.']);
        }

        $context = $this->resolveOwnedContext($input);
        if (!$context['success']) {
            $this->json($context);
        }

        $arrow = $this->resolveOwnedArrow($context, (int) ($input['hotspot_id'] ?? 0));
        if (!$arrow['success']) {
            $this->json($arrow);
        }

        $target = $this->resolveTargetPosition($context, (int) $arrow['hotspot']['target_position_id']);
        if (!$target['success']) {
            $this->json($target);
        }

        $texture = $this->resolveTexture($input);
        if (!$texture['success']) {
            $this->json($texture);
        }

        require_once BACKEND_PATH . '/models/HotspotModel.php';

        $ok = (new HotspotModel())->updateTextureScoped(
            (int) $arrow['hotspot']['id'],
            (int) $context['position']['id'],
            (int) $context['tour']['id'],
            $texture['x'],
            $texture['y']
        );

        $this->json([
            'success' => $ok,
            'message' => $ok ? 'Flecha actualizada correctamente.' : self::GENERIC_SAVE_ERROR,
        ]);
    }

    public function toggle(): void
    {
        $this->jsonHeader();
        $input = $this->postInput();

        if (!$this->isValidCsrf($input)) {
            $this->json(['success' => false, 'message' => 'Token de seguridad inválido.']);
        }

        $context = $this->resolveOwnedContext($input);
        if (!$context['success']) {
            $this->json($context);
        }

        $arrow = $this->resolveOwnedArrow($context, (int) ($input['hotspot_id'] ?? 0));
        if (!$arrow['success']) {
            $this->json($arrow);
        }

        $target = $this->resolveTargetPosition($context, (int) $arrow['hotspot']['target_position_id']);
        if (!$target['success']) {
            $this->json($target);
        }

        require_once BACKEND_PATH . '/models/HotspotModel.php';

        $active = $this->boolFromInput($input['is_active'] ?? null);
        $ok = (new HotspotModel())->setActiveScoped(
            (int) $arrow['hotspot']['id'],
            (int) $context['position']['id'],
            (int) $context['tour']['id'],
            $active
        );

        $this->json([
            'success' => $ok,
            'message' => $ok ? 'Flecha actualizada correctamente.' : self::GENERIC_SAVE_ERROR,
        ]);
    }

    public function delete(): void
    {
        $this->jsonHeader();
        $input = $this->postInput();

        if (!$this->isValidCsrf($input)) {
            $this->json(['success' => false, 'message' => 'Token de seguridad inválido.']);
        }

        $context = $this->resolveOwnedContext($input);
        if (!$context['success']) {
            $this->json($context);
        }

        $arrow = $this->resolveOwnedArrow($context, (int) ($input['hotspot_id'] ?? 0));
        if (!$arrow['success']) {
            $this->json($arrow);
        }

        $target = $this->resolveTargetPosition($context, (int) $arrow['hotspot']['target_position_id']);
        if (!$target['success']) {
            $this->json($target);
        }

        require_once BACKEND_PATH . '/models/HotspotModel.php';

        $ok = (new HotspotModel())->softDeleteScoped(
            (int) $arrow['hotspot']['id'],
            (int) $context['position']['id'],
            (int) $context['tour']['id']
        );

        $this->json([
            'success' => $ok,
            'message' => $ok ? 'Flecha eliminada correctamente.' : self::GENERIC_SAVE_ERROR,
        ]);
    }

    private function resolveOwnedContext(array $input): array
    {
        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $bizSlug = preg_replace('/[^a-z0-9-]/', '', (string) ($input['biz_slug'] ?? $input['negocio'] ?? ''));
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', (string) ($input['tour_slug'] ?? $input['tour'] ?? ''));
        $positionId = (int) ($input['position_id'] ?? $input['position'] ?? 0);

        if ($userId <= 0 || $bizSlug === '' || $tourSlug === '' || $positionId <= 0) {
            return ['success' => false, 'message' => 'No hemos podido encontrar esta zona.'];
        }

        // La cadena usuario -> negocio -> tour -> posicion evita que una peticion
        // manipulada pueda leer o cambiar flechas de otro propietario.
        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            return ['success' => false, 'message' => 'No tienes permiso para editar este negocio.'];
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            return ['success' => false, 'message' => 'No hemos podido encontrar este tour.'];
        }

        $position = (new PositionModel())->getByIdAndTour($positionId, (int) $tour['id']);
        if (!$position) {
            return ['success' => false, 'message' => 'No hemos podido encontrar esta zona.'];
        }

        $panorama = (new PhotoModel())->getByPositionAndDirection((int) $position['id'], '360');
        if (!$panorama) {
            return [
                'success' => false,
                'message' => 'Para añadir flechas de navegación, primero sube la foto panorámica de esta zona.',
            ];
        }

        return [
            'success' => true,
            'business' => $business,
            'tour' => $tour,
            'position' => $position,
            'panorama' => $panorama,
        ];
    }

    private function resolveTargetPosition(array $context, int $targetPositionId): array
    {
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';

        $originId = (int) $context['position']['id'];
        $tourId = (int) $context['tour']['id'];

        if ($targetPositionId <= 0 || $targetPositionId === $originId) {
            return ['success' => false, 'message' => 'Elige otra zona del tour como destino.'];
        }

        $target = (new PositionModel())->getByIdAndTour($targetPositionId, $tourId);
        if (!$target) {
            return ['success' => false, 'message' => 'La zona de destino no está disponible.'];
        }

        $panorama = (new PhotoModel())->getByPositionAndDirection((int) $target['id'], '360');
        if (!$panorama) {
            return ['success' => false, 'message' => 'La zona de destino necesita una foto panorámica antes de poder enlazarla.'];
        }

        return ['success' => true, 'position' => $target, 'panorama' => $panorama];
    }

    private function resolveOwnedArrow(array $context, int $hotspotId): array
    {
        require_once BACKEND_PATH . '/models/HotspotModel.php';

        $hotspot = (new HotspotModel())->getByIdInPositionTour(
            $hotspotId,
            (int) $context['position']['id'],
            (int) $context['tour']['id']
        );

        if (!$hotspot) {
            return ['success' => false, 'message' => 'No hemos podido encontrar esta flecha.'];
        }

        return ['success' => true, 'hotspot' => $hotspot];
    }

    private function getAvailableTargets(int $tourId, int $originPositionId): array
    {
        require_once BACKEND_PATH . '/models/PositionModel.php';
        require_once BACKEND_PATH . '/models/PhotoModel.php';

        $positions = (new PositionModel())->getByTour($tourId);
        $completeIds = array_fill_keys((new PhotoModel())->getPanoramaPositionIdsByTour($tourId), true);
        $targets = [];

        foreach ($positions as $position) {
            $positionId = (int) $position['id'];
            if ($positionId === $originPositionId || !isset($completeIds[$positionId])) {
                continue;
            }

            $targets[] = [
                'id' => $positionId,
                'name' => (string) $position['name'],
            ];
        }

        return $targets;
    }

    private function resolveTexture(array $input): array
    {
        $textureX = $input['texture_x'] ?? null;
        $textureY = $input['texture_y'] ?? null;

        if (!is_numeric($textureX) || !is_numeric($textureY)) {
            return ['success' => false, 'message' => self::GENERIC_SAVE_ERROR];
        }

        $textureX = (float) $textureX;
        $textureY = (float) $textureY;

        if ($textureX < 0 || $textureX > 1 || $textureY < 0 || $textureY > 1) {
            return ['success' => false, 'message' => self::GENERIC_SAVE_ERROR];
        }

        return ['success' => true, 'x' => $textureX, 'y' => $textureY];
    }

    private function postInput(): array
    {
        if (!empty($this->body)) {
            return $this->body;
        }

        $raw = file_get_contents('php://input');
        $json = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        $this->body = is_array($json) ? array_merge($_POST, $json) : $_POST;

        return $this->body;
    }

    private function isValidCsrf(array $input): bool
    {
        $token = (string) ($input['csrf_token'] ?? '');

        return $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    private function boolFromInput(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    private function jsonHeader(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    private function json(array $payload): never
    {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
        exit();
    }
}
