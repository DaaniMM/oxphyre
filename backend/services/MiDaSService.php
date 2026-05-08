<?php

/**
 * MiDaSService — cliente PHP del microservicio Flask de profundidad IA.
 *
 * Envía una imagen al microservicio local (127.0.0.1:5000) vía cURL,
 * recibe el mapa de profundidad como PNG en base64 y lo devuelve.
 *
 * El servicio Flask solo acepta requests desde localhost y requiere
 * el token PYTHON_SERVICE_TOKEN en el header X-Service-Token.
 */
class MiDaSService
{
    private const ENDPOINT = 'http://127.0.0.1:5000/process';
    private const TIMEOUT  = 120; // segundos — MiDaS puede tardar ~60s en CPU

    /**
     * Procesa una imagen con MiDaS y devuelve el mapa de profundidad en base64.
     *
     * @param string $imagePath Ruta absoluta al archivo de imagen en disco.
     * @return string|null Base64 del PNG generado, o null si el procesado falla.
     */
    public function process(string $imagePath): ?string
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

        // CURLFile envía el archivo como multipart/form-data
        $cfile = new CURLFile($imagePath);

        $ch = curl_init(self::ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['image' => $cfile],
            CURLOPT_HTTPHEADER     => ["X-Service-Token: {$token}"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            // Solo conexiones localhost — no necesitamos verificar certificado SSL
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("MiDaSService: cURL error — {$curlErr}");
            return null;
        }

        if ($httpCode !== 200) {
            error_log("MiDaSService: HTTP {$httpCode} para {$imagePath}");
            return null;
        }

        $data = json_decode($response, true);

        if (!is_array($data) || empty($data['success'])) {
            error_log('MiDaSService: ' . ($data['error'] ?? 'respuesta inválida del servicio'));
            return null;
        }

        return $data['depth_map'] ?? null;
    }
}
