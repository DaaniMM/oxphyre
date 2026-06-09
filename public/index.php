<?php

/**
 * Front Controller — Punto de entrada único de la aplicación.
 *
 * Nginx redirige TODAS las peticiones HTTP a este archivo (try_files $uri /index.php).
 * Esto nos permite centralizar en un solo lugar: carga del entorno, configuración
 * de sesión segura, emisión de headers de seguridad y despacho al router.
 * Sin este patrón, cada archivo PHP tendría que repetir esta inicialización.
 */

// Definimos la ruta raíz absoluta del proyecto.
// dirname(__DIR__) sube un nivel desde /public hasta la raíz /oxphyre.
// Usamos ROOT_PATH en todos los require_once para evitar rutas relativas
// que dependen del directorio de trabajo actual del servidor.
define('ROOT_PATH', dirname(__DIR__));


// ─── Carga del archivo .env ────────────────────────────────────────────────────
// Las credenciales y configuración sensible viven en .env, que está en .gitignore.
// parse_ini_file() lo lee como un archivo INI y devuelve un array clave=valor.
// putenv() lo pone disponible también para getenv(), además de $_ENV.
// El .env NUNCA se sube a GitHub ni al repositorio.
$envPath = ROOT_PATH . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || $trimmed[0] === '#') continue;
        if (strpos($trimmed, '=') === false) continue;

        [$name, $value] = explode('=', $trimmed, 2);
        $name  = trim($name);
        $value = trim($value);

        // Handle inline comments: KEY=value # comment
        if (!in_array($value[0] ?? '', ['"', "'"])) {
            $hashPos = strpos($value, ' #');
            if ($hashPos !== false) {
                $value = trim(substr($value, 0, $hashPos));
            }
        }

        // Clean surrounding quotes if value is quoted
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[-1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
} else {
    error_log('CRÍTICO: archivo .env no encontrado en ' . ROOT_PATH);
    http_response_code(500);
    exit('Error de configuración del servidor.');
}


// ─── Configuración de sesión segura ───────────────────────────────────────────
// IMPORTANTE: estos ini_set deben ejecutarse ANTES de session_start().
// Una vez iniciada la sesión, estos parámetros no tienen efecto.

// HttpOnly: impide que JavaScript (y scripts XSS) lean la cookie de sesión.
// Si un atacante inyecta JS, no podrá robar el session_id.
ini_set('session.cookie_httponly', 1);

// Secure: la cookie de sesión solo se envía por conexiones HTTPS.
// Previene que se transmita en texto claro por HTTP.
ini_set('session.cookie_secure', 1);

// SameSite=Strict: el navegador no enviará la cookie en peticiones cross-site.
// Protección fundamental contra ataques CSRF basados en cookies.
ini_set('session.cookie_samesite', 'Strict');

// use_strict_mode: PHP rechaza IDs de sesión que no haya creado él mismo.
// Previene ataques de Session Fixation donde el atacante fija un ID conocido.
ini_set('session.use_strict_mode', 1);

// Tiempo de vida del GC: sesiones inactivas expiran tras 1 hora.
ini_set('session.gc_maxlifetime', 3600);

// cookie_lifetime = 0: la cookie desaparece al cerrar el navegador.
// El usuario debe volver a hacer login en cada sesión de navegador.
ini_set('session.cookie_lifetime', 0);

session_start();


// ─── Headers de seguridad HTTP ─────────────────────────────────────────────────
// Estos headers viajan en CADA respuesta del servidor al navegador.
// Instruyen al navegador sobre qué está permitido y qué no, independientemente del contenido HTML.

// X-Frame-Options: prohíbe embeber esta web en un <iframe>.
// Protege contra ataques de Clickjacking donde el atacante superpone capas invisibles.
header('X-Frame-Options: DENY');

// X-Content-Type-Options: impide que el navegador "adivine" el tipo MIME de un archivo.
// Sin esto, un archivo .txt con contenido JS podría ejecutarse como script (MIME sniffing).
header('X-Content-Type-Options: nosniff');

// Referrer-Policy: controla qué URL se envía en el header Referer al navegar.
// strict-origin-when-cross-origin: solo el origen (sin path) en peticiones cross-origin.
// Evita filtrar URLs internas (ej: /dashboard?token=xxx) a sitios externos.
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content-Security-Policy: declara desde qué orígenes puede cargar recursos el navegador.
// Es la defensa más potente contra XSS: aunque un atacante inyecte JS, el navegador lo bloqueará
// si no cumple esta política. Ajustar cuando se añadan CDNs externos (Three.js, Fonts).
// 'unsafe-inline' en script-src/style-src es necesario temporalmente; eliminarlo cuando
// se migren los estilos e inline scripts a archivos externos con nonces.
// unpkg.com está en script-src para cargar Three.js desde CDN con defer.
// 'unsafe-inline' en script-src es temporal — se eliminará cuando se añadan nonces por petición.
// cdn.jsdelivr.net queda permitido para Three.js del visor público.
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: blob: https://pub-b9106d772d3349409c0b98f07f931aa0.r2.dev https://media.oxphyre.com https://cdn.jsdelivr.net https://*.tile.openstreetmap.org; connect-src 'self' https://cdn.jsdelivr.net; frame-src 'self' https://vid2scene.com https://superspl.at;");

// HSTS: fuerza al navegador a usar HTTPS durante 1 año (31536000 segundos).
// Una vez recibido, el navegador rechaza cualquier conexión HTTP al dominio.
// Solo activamos en producción — en desarrollo local no hay HTTPS.
if (($_ENV['APP_ENV'] ?? 'production') === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}


// ─── Carga de archivos base del framework MVC ─────────────────────────────────
// El orden importa:
// 1. config.php primero: define constantes que usan los siguientes archivos.
// 2. database.php: clase disponible para cualquier controller que la necesite.
// 3. AuthMiddleware.php: el router lo llama antes de despachar rutas protegidas.
// 4. web.php al final: despacha la petición, que puede usar todo lo anterior.
require_once ROOT_PATH . '/backend/config/config.php';
require_once ROOT_PATH . '/backend/config/database.php';
require_once ROOT_PATH . '/backend/middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/backend/routes/web.php';
