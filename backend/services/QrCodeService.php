<?php

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class QrCodeService
{
    public function __construct()
    {
        $autoload = ROOT_PATH . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new RuntimeException('Dependencias Composer no disponibles. Ejecuta composer install.');
        }

        require_once $autoload;

        if (!extension_loaded('iconv')) {
            throw new RuntimeException('La extension iconv es necesaria para generar codigos QR.');
        }

        if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
            throw new RuntimeException('La extension GD es necesaria para generar QR PNG.');
        }
    }

    public function generatePng(string $url, int $size = 640): string
    {
        $url = trim($url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('La URL del QR no es valida.');
        }

        $safeSize = max(240, min(1200, $size));
        $renderer = new GDLibRenderer($safeSize);
        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }
}
