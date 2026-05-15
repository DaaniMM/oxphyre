<?php

/**
 * R2StorageService - cliente aislado para Cloudflare R2.
 *
 * Este servicio concentra todo lo relacionado con R2: subir WebP finales,
 * borrar objetos remotos y construir la URL publica que luego podra guardar
 * PhotoModel. No decide si R2 esta activo: R2_ENABLED lo evaluara el caller
 * cuando se integre en Fase 2.
 *
 * Mantenerlo aislado evita mezclar almacenamiento externo con controllers,
 * modelos o visor. El pipeline de upload y el dashboard se conectaran despues.
 */
class R2StorageService
{
    private const SERVICE = 's3';
    private const ALGORITHM = 'AWS4-HMAC-SHA256';
    private const CONTENT_TYPE_WEBP = 'image/webp';
    private const EMPTY_PAYLOAD_HASH = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
    private const REQUEST_TIMEOUT = 60;
    private const CONNECT_TIMEOUT = 10;
    private const VALID_DIRECTIONS = ['360', 'N', 'S', 'E', 'O'];

    private string $accountId;
    private string $accessKeyId;
    private string $secretAccessKey;
    private string $bucket;
    private string $publicBaseUrl;
    private string $region;

    public function __construct()
    {
        // Fallar al instanciar deja claro que falta configuracion antes de
        // intentar peticiones mal firmadas o dificiles de diagnosticar.
        $this->accountId = $this->requiredEnv('R2_ACCOUNT_ID');
        $this->accessKeyId = $this->requiredEnv('R2_ACCESS_KEY_ID');
        $this->secretAccessKey = $this->requiredEnv('R2_SECRET_ACCESS_KEY');
        $this->bucket = $this->requiredEnv('R2_BUCKET');
        $this->publicBaseUrl = rtrim($this->requiredEnv('R2_PUBLIC_BASE_URL'), '/');
        $this->region = $this->requiredEnv('R2_REGION');
    }

    public function upload(string $localPath, string $key): bool
    {
        // Flujo: validar key, comprobar archivo, calcular hash, firmar PUT y
        // subir por streaming para no cargar panoramicas grandes en la RAM del EC2.
        try {
            $this->validateKey($key);
        } catch (InvalidArgumentException $exception) {
            error_log('R2StorageService::upload: key invalida - ' . $exception->getMessage());
            return false;
        }

        if (!is_file($localPath) || !is_readable($localPath)) {
            error_log("R2StorageService::upload: archivo no legible {$localPath}");
            return false;
        }

        $payloadHash = hash_file('sha256', $localPath);
        if ($payloadHash === false) {
            error_log("R2StorageService::upload: no se pudo calcular hash de {$localPath}");
            return false;
        }

        $fileSize = filesize($localPath);
        if ($fileSize === false) {
            error_log("R2StorageService::upload: no se pudo leer tamano de {$localPath}");
            return false;
        }

        $handle = fopen($localPath, 'rb');
        if ($handle === false) {
            error_log("R2StorageService::upload: no se pudo abrir {$localPath}");
            return false;
        }

        $signedRequest = $this->signedRequest('PUT', $key, $payloadHash, [
            'content-type' => self::CONTENT_TYPE_WEBP,
        ]);

        $ch = curl_init($signedRequest['url']);
        curl_setopt_array($ch, [
            CURLOPT_UPLOAD => true,
            CURLOPT_INFILE => $handle,
            CURLOPT_INFILESIZE => $fileSize,
            CURLOPT_HTTPHEADER => $signedRequest['headers'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        fclose($handle);

        if ($response === false) {
            error_log("R2StorageService::upload: cURL error para {$key} - {$curlError}");
            return false;
        }

        if (!in_array($httpCode, [200, 201], true)) {
            error_log("R2StorageService::upload: HTTP {$httpCode} para {$key}");
            return false;
        }

        return true;
    }

    public function getPublicUrl(string $key): string
    {
        // No llama a R2: la URL publica es el custom domain mas la storage_key
        // codificada. Si cambia el dominio CDN, se puede regenerar desde la key.
        $this->validateKey($key);

        return $this->publicBaseUrl . '/' . $this->encodedKey($key);
    }

    public function delete(string $key): bool
    {
        // DELETE firmado contra R2. Un 404 se considera correcto porque el
        // resultado deseado ya se cumple: el objeto no existe.
        try {
            $this->validateKey($key);
        } catch (InvalidArgumentException $exception) {
            error_log('R2StorageService::delete: key invalida - ' . $exception->getMessage());
            return false;
        }

        $signedRequest = $this->signedRequest('DELETE', $key, self::EMPTY_PAYLOAD_HASH);

        $ch = curl_init($signedRequest['url']);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => $signedRequest['headers'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("R2StorageService::delete: cURL error para {$key} - {$curlError}");
            return false;
        }

        if (!in_array($httpCode, [200, 202, 204, 404], true)) {
            error_log("R2StorageService::delete: HTTP {$httpCode} para {$key}");
            return false;
        }

        return true;
    }

    private function requiredEnv(string $name): string
    {
        $value = trim((string) ($_ENV[$name] ?? ''));
        if ($value === '') {
            throw new RuntimeException("R2StorageService: falta {$name}");
        }

        return $value;
    }

    private function validateKey(string $key): void
    {
        // Las keys deben generarlas procesos internos de Oxphyre, nunca texto
        // libre del usuario. Esta validacion evita traversal, espacios, slash
        // inicial y caracteres raros antes de firmar o construir URLs.
        if ($key === '') {
            throw new InvalidArgumentException('key vacia');
        }

        if (preg_match('/\s/', $key) === 1) {
            throw new InvalidArgumentException('key con espacios');
        }

        if (str_contains($key, '..')) {
            throw new InvalidArgumentException('key con ..');
        }

        if (str_starts_with($key, '/')) {
            throw new InvalidArgumentException('key con slash inicial');
        }

        if (preg_match('/^[A-Za-z0-9._\/-]+$/', $key) !== 1) {
            throw new InvalidArgumentException('key con caracteres no permitidos');
        }

        if (!str_ends_with($key, '.webp')) {
            throw new InvalidArgumentException('key sin extension .webp');
        }

        $segments = explode('/', $key);
        if (count(array_intersect($segments, self::VALID_DIRECTIONS)) === 0) {
            throw new InvalidArgumentException('key sin direction valida');
        }
    }

    private function signedRequest(string $method, string $key, string $payloadHash, array $extraHeaders = []): array
    {
        // AWS Signature V4 firma una representacion canonica de la peticion:
        // canonical request -> string to sign -> signing key -> Authorization.
        // El host firmado debe ser exactamente el mismo host que usara cURL.
        $encodedKey = $this->encodedKey($key);
        $host = $this->host();
        $amzDate = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');

        $headers = array_merge($extraHeaders, [
            'host' => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date' => $amzDate,
        ]);
        ksort($headers);

        $canonicalHeaders = '';
        foreach ($headers as $name => $value) {
            $canonicalHeaders .= strtolower($name) . ':' . trim((string) $value) . "\n";
        }

        $signedHeaders = implode(';', array_keys($headers));
        $canonicalRequest = implode("\n", [
            strtoupper($method),
            '/' . $encodedKey,
            '',
            $canonicalHeaders,
            $signedHeaders,
            $payloadHash,
        ]);

        $credentialScope = "{$dateStamp}/{$this->region}/" . self::SERVICE . '/aws4_request';
        $stringToSign = implode("\n", [
            self::ALGORITHM,
            $amzDate,
            $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        $signature = hash_hmac('sha256', $stringToSign, $this->signingKey($dateStamp));
        $authorization = self::ALGORITHM
            . " Credential={$this->accessKeyId}/{$credentialScope},"
            . " SignedHeaders={$signedHeaders},"
            . " Signature={$signature}";

        $headerLines = $this->headerLines($headers);
        $headerLines[] = "Authorization: {$authorization}";

        return [
            'url' => "https://{$host}/{$encodedKey}",
            'headers' => $headerLines,
        ];
    }

    private function signingKey(string $dateStamp): string
    {
        $dateKey = hash_hmac('sha256', $dateStamp, 'AWS4' . $this->secretAccessKey, true);
        $regionKey = hash_hmac('sha256', $this->region, $dateKey, true);
        $serviceKey = hash_hmac('sha256', self::SERVICE, $regionKey, true);

        return hash_hmac('sha256', 'aws4_request', $serviceKey, true);
    }

    private function encodedKey(string $key): string
    {
        // Codificar por segmento mantiene los "/" como separadores de ruta.
        // urlencode($key) completo romperia la key al convertir "/" en %2F.
        return implode('/', array_map('rawurlencode', explode('/', $key)));
    }

    private function host(): string
    {
        // R2 se firma en virtual-host style: bucket.accountId.r2...
        // No usar path-style porque cambiaría el host y rompería la firma.
        return "{$this->bucket}.{$this->accountId}.r2.cloudflarestorage.com";
    }

    private function headerLines(array $headers): array
    {
        $lines = [];
        foreach ($headers as $name => $value) {
            $lines[] = $this->headerName($name) . ': ' . $value;
        }

        return $lines;
    }

    private function headerName(string $name): string
    {
        return match ($name) {
            'content-type' => 'Content-Type',
            'host' => 'Host',
            'x-amz-content-sha256' => 'x-amz-content-sha256',
            'x-amz-date' => 'x-amz-date',
            default => $name,
        };
    }
}
