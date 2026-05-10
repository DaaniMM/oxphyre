<?php

/**
 * MiDaSService — cliente PHP del microservicio Flask de profundidad IA.
 *
 * Expone dos operaciones:
 *   - process(): genera mapa de profundidad MiDaS (PNG base64)
 *   - enhance(): mejora contraste/iluminación con CLAHE (JPEG base64)
 *
 * Ambas operaciones son opacas para el controller: si fallan devuelven null
 * y el flujo continúa sin interrumpirse (fallo silencioso).
 */
class MiDaSService
{
    private const ENDPOINT_PROCESS = 'http://127.0.0.1:5000/process';
    private const ENDPOINT_ENHANCE = 'http://127.0.0.1:5000/enhance';
    private const TIMEOUT_PROCESS  = 120; // segundos — MiDaS puede tardar ~60s en CPU
    private const TIMEOUT_ENHANCE  = 30;  // CLAHE es rápido, 30s más que suficiente

    // ── Profundidad MiDaS ─────────────────────────────────────────────────────

    public function process(string $imagePath): ?string
    {
        $response = $this->callService(self::ENDPOINT_PROCESS, $imagePath, self::TIMEOUT_PROCESS);
        if ($response === null) return null;

        $data = json_decode($response, true);

        if (!is_array($data) || empty($data['success'])) {
            error_log('MiDaSService::process: ' . ($data['error'] ?? 'respuesta inválida'));
            return null;
        }

        return $data['depth_map'] ?? null;
    }

    // ── CLAHE — mejora automática de contraste e iluminación ─────────────────

    public function enhance(string $imagePath): ?string
    {
        $response = $this->callService(self::ENDPOINT_ENHANCE, $imagePath, self::TIMEOUT_ENHANCE);
        if ($response === null) return null;

        $data = json_decode($response, true);

        if (!is_array($data) || empty($data['success'])) {
            // Fallo silencioso: el controller usará la foto original sin mejorar
            return null;
        }

        return $data['image'] ?? null;
    }

    // ── Helper compartido ─────────────────────────────────────────────────────

    private function callService(string $endpoint, string $imagePath, int $timeout): ?string
    {
        $token = $_ENV['PYTHON_SERVICE_TOKEN'] ?? '';

        if ($token === '') {
            error_log('MiDaSService: PYTHON_SERVICE_TOKEN no configurado');
            return null;
        }

        if (!file_exists($imagePath)) {
            error_log("MiDaSService: archivo no encontrado: {$imagePath}");
            return null;
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['image' => new CURLFile($imagePath)],
            CURLOPT_HTTPHEADER     => ["X-Service-Token: {$token}"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_SSL_VERIFYPEER => false, // conexión localhost, sin TLS
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        // curl_close() eliminado: deprecated en PHP 8.4, el GC libera el recurso automáticamente

        if ($response === false) {
            error_log("MiDaSService: cURL error en {$endpoint} — {$curlErr}");
            return null;
        }

        if ($httpCode !== 200) {
            error_log("MiDaSService: HTTP {$httpCode} para {$endpoint}");
            return null;
        }

        return $response;
    }
}
