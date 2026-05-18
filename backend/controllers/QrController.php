<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class QrController extends BaseController
{
    public function download(): void
    {
        global $routeParams;

        $bizSlug = preg_replace('/[^a-z0-9-]/', '', $routeParams['biz'] ?? '');
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', $routeParams['tour'] ?? '');
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        require_once BACKEND_PATH . '/models/BusinessModel.php';
        require_once BACKEND_PATH . '/models/TourModel.php';
        require_once BACKEND_PATH . '/models/QrCodeModel.php';
        require_once BACKEND_PATH . '/services/QrCodeService.php';

        $business = (new BusinessModel())->getBySlug($bizSlug, $userId);
        if (!$business) {
            $this->flash('error', 'Negocio no encontrado.');
            $this->redirect('/dashboard/negocios');
        }

        $tour = (new TourModel())->getBySlugAndBusiness($tourSlug, (int) $business['id']);
        if (!$tour) {
            $this->flash('error', 'Tour no encontrado.');
            $this->redirect("/dashboard/negocios/{$bizSlug}");
        }

        if (!(bool) $tour['is_published']) {
            $this->flash('error', 'Publica el tour antes de descargar su QR.');
            $this->redirect("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        $qrCode = (new QrCodeModel())->getOrCreateForTour((int) $tour['id']);
        $token = (string) ($qrCode['token'] ?? '');
        if (!preg_match('/^[A-Za-z0-9]{12}$/', $token)) {
            $this->flash('error', 'No se pudo preparar el QR de este tour.');
            $this->redirect("/dashboard/negocios/{$bizSlug}/tours/{$tourSlug}");
        }

        $qrUrl = rtrim(APP_URL, '/') . '/qr/' . $token;
        $png = (new QrCodeService())->generatePng($qrUrl);
        $filename = $this->buildDownloadFilename($bizSlug, $tourSlug);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: image/png');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($png));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        echo $png;
        exit();
    }

    public function redirectToTour(): void
    {
        global $routeToken;

        $token = (string) ($routeToken ?? '');
        if (!preg_match('/^[A-Za-z0-9]{12}$/', $token)) {
            $this->serve404();
        }

        require_once BACKEND_PATH . '/models/QrCodeModel.php';
        require_once BACKEND_PATH . '/models/QrScanModel.php';

        $target = (new QrCodeModel())->findPublicTargetByToken($token);
        if (!$target || !(bool) $target['is_published']) {
            $this->serve404();
        }

        $businessSlug = preg_replace('/[^a-z0-9-]/', '', (string) $target['business_slug']);
        $tourSlug = preg_replace('/[^a-z0-9-]/', '', (string) $target['tour_slug']);

        $this->recordQrScanIfTrackable((int) $target['qr_id']);

        header('Location: /tour/' . $businessSlug . '/' . $tourSlug . '?src=qr', true, 302);
        exit();
    }

    private function buildDownloadFilename(string $businessSlug, string $tourSlug): string
    {
        $safeBusiness = preg_replace('/[^a-z0-9-]/', '', $businessSlug);
        $safeTour = preg_replace('/[^a-z0-9-]/', '', $tourSlug);

        return 'oxphyre-qr-' . $safeBusiness . '-' . $safeTour . '.png';
    }

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }

    private function serve404(): never
    {
        http_response_code(404);
        if ($this->isHeadRequest()) {
            exit();
        }

        $view404 = VIEWS_PATH . '/errors/404.php';
        if (file_exists($view404)) {
            require_once $view404;
        } else {
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>404 - Oxphyre</title></head><body><h1>QR no encontrado</h1><p><a href="/">Volver al inicio</a></p></body></html>';
        }
        exit();
    }

    private function isHeadRequest(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD';
    }

    private function recordQrScanIfTrackable(int $qrCodeId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            return;
        }

        $userAgent = trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($userAgent === '' || $this->isBotUserAgent($userAgent)) {
            return;
        }

        $clientIp = $this->getClientIp();
        if ($clientIp === '') {
            return;
        }

        try {
            $ipHash = $this->hashClientIp($clientIp);
            $deviceType = $this->detectDeviceType($userAgent);
            (new QrScanModel())->recordScan($qrCodeId, $ipHash, $deviceType);
        } catch (Throwable $e) {
            error_log('QR scan tracking failed: ' . $e->getMessage());
        }
    }

    private function isBotUserAgent(string $userAgent): bool
    {
        $ua = strtolower($userAgent);
        $botPatterns = [
            'bot',
            'crawl',
            'spider',
            'slurp',
            'curl',
            'wget',
            'python-requests',
            'httpclient',
            'headless',
            'scrapy',
            'monitor',
            'uptime',
            'pingdom',
        ];

        foreach ($botPatterns as $pattern) {
            if (str_contains($ua, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function detectDeviceType(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        if ($ua === '') {
            return 'unknown';
        }

        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || (str_contains($ua, 'android') && !str_contains($ua, 'mobile'))) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod')) {
            return 'mobile';
        }

        if (str_contains($ua, 'mozilla') || str_contains($ua, 'chrome') || str_contains($ua, 'safari') || str_contains($ua, 'firefox') || str_contains($ua, 'edge')) {
            return 'desktop';
        }

        return 'unknown';
    }

    private function getClientIp(): string
    {
        $cloudflareIp = (string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? '');
        if (filter_var($cloudflareIp, FILTER_VALIDATE_IP)) {
            return $cloudflareIp;
        }

        $remoteIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        if (filter_var($remoteIp, FILTER_VALIDATE_IP)) {
            return $remoteIp;
        }

        return '';
    }

    private function hashClientIp(string $clientIp): string
    {
        $salt = (string) ($_ENV['QR_HASH_SALT'] ?? '');
        if ($salt === '') {
            $salt = (string) ($_ENV['APP_KEY'] ?? '');
        }
        if ($salt === '') {
            $salt = (string) (defined('APP_URL') ? APP_URL : 'oxphyre');
        }

        return hash_hmac('sha256', $clientIp, $salt);
    }
}
