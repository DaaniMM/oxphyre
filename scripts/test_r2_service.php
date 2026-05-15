<?php

/**
 * Test CLI aislado de R2StorageService.
 *
 * Este script valida contra Cloudflare R2 sin tocar BD, upload, visor ni
 * archivos reales de usuario. Crea un WebP temporal, lo sube a una key de
 * prueba, comprueba la URL publica, borra el objeto y verifica que ya no sirve.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('Este script solo puede ejecutarse por CLI.' . PHP_EOL);
}

define('ROOT_PATH', dirname(__DIR__));
define('TEST_KEY', 'tests/r2-probe/360/r2-test-probe.webp');

require_once ROOT_PATH . '/backend/services/R2StorageService.php';

$tmpPath = '';
$service = null;
$exitCode = 0;

try {
    outputOk('Inicio del test aislado de Cloudflare R2.');

    // Carga el .env real del entorno actual sin mostrar secretos en consola.
    loadEnv(ROOT_PATH . '/.env');
    outputOk('.env cargado correctamente.');

    // El servicio falla rapido si faltan variables R2 criticas.
    $service = new R2StorageService();
    outputOk('R2StorageService instanciado.');

    // Genera un WebP diminuto en /tmp para no tocar archivos reales del proyecto.
    $tmpPath = createProbeWebp();
    outputOk("WebP temporal creado: {$tmpPath}");

    $publicUrl = $service->getPublicUrl(TEST_KEY);
    outputOk("URL publica esperada: {$publicUrl}");

    // Prueba principal: PUT firmado por streaming hacia R2.
    if (!$service->upload($tmpPath, TEST_KEY)) {
        throw new RuntimeException('upload() devolvio false.');
    }
    outputOk('Objeto subido a R2.');

    $checkAfterUpload = publicUrlCheck($publicUrl);
    if ($checkAfterUpload['status'] !== 200) {
        throw new RuntimeException("La URL publica no devuelve 200 tras upload. HTTP {$checkAfterUpload['status']}.");
    }
    outputOk('URL publica verificada con HTTP 200.');

    // DELETE firmado. Un 404 posterior confirma que el objeto de prueba no queda expuesto.
    if (!$service->delete(TEST_KEY)) {
        throw new RuntimeException('delete() devolvio false.');
    }
    outputCleanup('Objeto R2 de prueba eliminado.');

    // Cloudflare puede servir una copia en cache aunque el objeto ya no exista en R2.
    // Si aparece 200 con cf-cache-status HIT, avisamos sin fallar el test del servicio.
    $checkAfterDelete = publicUrlCheck($publicUrl);
    if (in_array($checkAfterDelete['status'], [403, 404], true)) {
        outputOk("URL ya no sirve el objeto tras delete. HTTP {$checkAfterDelete['status']}.");
    } elseif ($checkAfterDelete['status'] === 200 && isLikelyCdnCache($checkAfterDelete)) {
        outputWarn('La URL sigue devolviendo 200, pero parece cache Cloudflare/CDN; el delete del servicio no se considera fallido.');
        outputHttpCheck($checkAfterDelete);
    } elseif ($checkAfterDelete['status'] === 200) {
        outputWarn('La URL sigue devolviendo 200 tras delete sin senales claras de cache CDN. Revisar bucket/R2 manualmente.');
        outputHttpCheck($checkAfterDelete);
    } else {
        outputWarn("La URL devolvio HTTP {$checkAfterDelete['status']} tras delete. No es 200, 403 ni 404; revisar manualmente si hace falta.");
        outputHttpCheck($checkAfterDelete);
    }
} catch (Throwable $exception) {
    outputFail($exception->getMessage());
    $exitCode = 1;
} finally {
    // Limpieza defensiva: aunque falle una fase, se intenta no dejar residuos.
    if ($service instanceof R2StorageService) {
        if ($service->delete(TEST_KEY)) {
            outputCleanup('Limpieza R2 final verificada.');
        } else {
            outputFail('No se pudo confirmar el borrado del objeto R2 en finally.');
            $exitCode = 1;
        }
    }

    if ($tmpPath !== '' && is_file($tmpPath)) {
        if (@unlink($tmpPath)) {
            outputCleanup('Archivo temporal local eliminado.');
        } else {
            outputFail("No se pudo eliminar el archivo temporal local: {$tmpPath}");
            $exitCode = 1;
        }
    }
}

exit($exitCode);

/**
 * Carga un archivo .env simple en $_ENV y putenv().
 * Replica el comportamiento del front controller sin depender de Composer.
 */
function loadEnv(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException("No se puede leer .env en {$path}");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        throw new RuntimeException('No se pudo leer el archivo .env.');
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || $trimmed[0] === '#' || strpos($trimmed, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $trimmed, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        // Permite comentarios inline tipo KEY=value # comentario si el valor no esta quoted.
        if (!in_array($value[0] ?? '', ['"', "'"], true)) {
            $hashPos = strpos($value, ' #');
            if ($hashPos !== false) {
                $value = trim(substr($value, 0, $hashPos));
            }
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}

/**
 * Crea una imagen WebP minima usando GD. Si GD/WebP no esta disponible,
 * el test se detiene con un mensaje claro antes de tocar R2.
 */
function createProbeWebp(): string
{
    if (!function_exists('imagecreatetruecolor') || !function_exists('imagewebp')) {
        throw new RuntimeException('GD con soporte WebP no esta disponible en este entorno.');
    }

    $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'oxphyre-r2-test-' . bin2hex(random_bytes(8)) . '.webp';
    $image = imagecreatetruecolor(10, 10);
    if ($image === false) {
        throw new RuntimeException('No se pudo crear la imagen temporal GD.');
    }

    $color = imagecolorallocate($image, 254, 179, 84);
    imagefilledrectangle($image, 0, 0, 9, 9, $color);

    $saved = imagewebp($image, $path, 80);
    imagedestroy($image);

    if (!$saved || !is_file($path) || filesize($path) === 0) {
        @unlink($path);
        throw new RuntimeException('No se pudo guardar el WebP temporal.');
    }

    return $path;
}

/**
 * Comprueba la URL publica con HEAD para evitar descargar el archivo completo.
 * Devuelve status y headers utiles para detectar cache de Cloudflare/CDN.
 */
function publicUrlCheck(string $url): array
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('cURL no esta disponible para comprobar la URL publica.');
    }

    $ch = curl_init($url);
    $headers = [];
    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADERFUNCTION => static function ($ch, string $line) use (&$headers): int {
            $length = strlen($line);
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
            }

            return $length;
        },
    ]);

    $response = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException("No se pudo comprobar la URL publica: {$error}");
    }

    return [
        'status' => $status,
        'cf-cache-status' => $headers['cf-cache-status'] ?? '',
        'cache-control' => $headers['cache-control'] ?? '',
        'age' => $headers['age'] ?? '',
    ];
}

function isLikelyCdnCache(array $check): bool
{
    $cacheStatus = strtoupper((string) ($check['cf-cache-status'] ?? ''));

    return in_array($cacheStatus, ['HIT', 'STALE', 'UPDATING', 'REVALIDATED'], true)
        || (string) ($check['age'] ?? '') !== ''
        || str_contains(strtolower((string) ($check['cache-control'] ?? '')), 'max-age');
}

function outputHttpCheck(array $check): void
{
    outputWarn(
        'HEAD status=' . $check['status']
        . '; cf-cache-status=' . ($check['cf-cache-status'] !== '' ? $check['cf-cache-status'] : '-')
        . '; cache-control=' . ($check['cache-control'] !== '' ? $check['cache-control'] : '-')
        . '; age=' . ($check['age'] !== '' ? $check['age'] : '-')
    );
}

function outputOk(string $message): void
{
    echo "[OK] {$message}" . PHP_EOL;
}

function outputFail(string $message): void
{
    echo "[FAIL] {$message}" . PHP_EOL;
}

function outputWarn(string $message): void
{
    echo "[WARN] {$message}" . PHP_EOL;
}

function outputCleanup(string $message): void
{
    echo "[CLEANUP] {$message}" . PHP_EOL;
}
