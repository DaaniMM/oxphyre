<?php

/**
 * Constantes globales de la aplicación.
 *
 * Centralizamos aquí todos los valores de configuración que se usan en múltiples
 * partes del sistema. Usar define() en lugar de variables globales evita que sean
 * sobreescritas accidentalmente en cualquier punto del código.
 *
 * Este archivo se carga en el Front Controller ANTES que cualquier otro,
 * por lo que las constantes están disponibles en controllers, modelos y vistas.
 */


// ─── Aplicación ────────────────────────────────────────────────────────────────

// Nombre visible de la aplicación. Se usa en emails, títulos de página y marca de agua.
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Oxphyre');

// Versión semántica. Útil para cache-busting de assets (CSS, JS).
// Incrementar en cada deploy que cambie assets públicos.
define('APP_VERSION', '1.0.0');

// URL base completa (con protocolo y dominio, sin slash final).
// Se usa para construir enlaces absolutos en emails y redirects.
// Ejemplo: 'https://oxphyre.com' o 'http://localhost' en desarrollo.
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

// Entorno de ejecución: 'production' | 'development'.
// Controla el nivel de detalle de errores, HSTS y otras configuraciones.
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');


// ─── Modo de errores según entorno ────────────────────────────────────────────
// En desarrollo mostramos todos los errores para depurar más rápido.
// En producción ocultamos los errores al usuario (podrían revelar información sensible)
// y los redirigimos al log del servidor para análisis interno.
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}


// ─── Rutas del sistema de archivos ────────────────────────────────────────────
// Definimos rutas absolutas para usar en require_once y operaciones de archivos.
// Las rutas relativas dependen del directorio de trabajo actual del proceso PHP,
// que puede variar según cómo Nginx invoca PHP-FPM. Las absolutas son siempre fiables.

// Raíz del proyecto (mismo valor que ROOT_PATH definido en index.php).
// Lo redefinimos aquí por si alguien incluye config.php desde otro punto de entrada.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}

// Ruta al directorio backend con controllers, modelos, vistas, etc.
define('BACKEND_PATH', ROOT_PATH . '/backend');

// Ruta a las vistas PHP. Los controllers usan esta constante para cargar templates.
define('VIEWS_PATH', ROOT_PATH . '/backend/views');

// Ruta al directorio de uploads públicos. Solo archivos procesados y validados llegan aquí.
define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');


// ─── Configuración de uploads ──────────────────────────────────────────────────
// Tamaño máximo permitido para imágenes subidas: 10 MB en bytes.
// MiDaS necesita imágenes de calidad para generar buenos mapas de profundidad,
// pero limitamos el tamaño para proteger el servidor y evitar abusos.
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);

// Tipos MIME reales permitidos para fotos de posiciones del tour.
// Validamos el MIME real con finfo_file(), no la extensión del archivo,
// porque un atacante puede renombrar un .php a .jpg fácilmente.
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);


// ─── Sesión ────────────────────────────────────────────────────────────────────
// Tiempo máximo de inactividad antes de que la sesión expire: 1 hora.
// Coincide con session.gc_maxlifetime configurado en el Front Controller.
define('SESSION_LIFETIME', 3600);


// ─── Planes SaaS ──────────────────────────────────────────────────────────────
// IDs de los planes tal como están en la tabla `plans` de MySQL.
// Centralizamos aquí para no hardcodear números mágicos en el código.
// Si cambian los IDs en BD, solo hay que actualizar aquí.
define('PLAN_FREE',     1);
define('PLAN_PRO',      2);
define('PLAN_BUSINESS', 3);
