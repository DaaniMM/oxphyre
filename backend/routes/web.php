<?php

/**
 * Router principal de la aplicación.
 *
 * Mapea cada combinación (método HTTP + ruta URL) a un controller y método concreto.
 * Este enfoque centraliza toda la lógica de enrutamiento: de un vistazo se ve qué 
 * rutas existen, qué controller las maneja y qué middleware protege cada una.
 *
 * Flujo de una petición:
 *   Nginx → index.php → web.php → [middleware] → Controller::method() → Vista
 */


// ─── Tabla de rutas ────────────────────────────────────────────────────────────
// Estructura: $routes[MÉTODO_HTTP]['/ruta'] = [Controller, método, guard?]
//
// Guard opcional (tercer elemento):
//   'auth'  → ruta protegida: solo usuarios con sesión activa pueden acceder.
//   'guest' → ruta de invitado: redirige al dashboard si ya hay sesión.
//   (sin guard) → ruta pública accesible por cualquiera.
//
// Añadir nuevas rutas aquí es suficiente — el router las resuelve automáticamente.
$routes = [
    'GET' => [
        '/'          => ['HomeController',      'index'],
        '/login'     => ['AuthController',      'showLogin',    'guest'],
        '/register'  => ['AuthController',      'showRegister', 'guest'],
        '/registro'  => ['AuthController',      'showRegister', 'guest'],
        '/recover'   => ['AuthController',      'showRecover',  'guest'],
        '/reset'     => ['AuthController',      'showReset',    'guest'],
        '/verify'    => ['AuthController',      'verifyEmail'],
        '/dashboard'                  => ['DashboardController', 'index',       'auth'],
        '/dashboard/negocios'         => ['BusinessController', 'showList',    'auth'],
        '/dashboard/negocios/nuevo'   => ['BusinessController', 'showCreate',  'auth'],
        '/dashboard/tours'            => ['TourController',     'showList',    'auth'],
        '/dashboard/tours/nuevo'      => ['TourController',     'showCreate',  'auth'],
        '/dashboard/business/created' => ['BusinessController', 'showSuccess', 'auth'],
    ],
    'POST' => [
        '/login'    => ['AuthController', 'login',    'guest'],
        '/register' => ['AuthController', 'register', 'guest'],
        '/registro' => ['AuthController', 'register', 'guest'],
        '/recover'  => ['AuthController', 'recover',  'guest'],
        '/reset'    => ['AuthController', 'reset',    'guest'],
        '/logout'                   => ['AuthController',    'logout', 'auth'],
        '/dashboard/business/store' => ['BusinessController', 'store',  'auth'],
        '/dashboard/tours/store'    => ['TourController',     'store',  'auth'],
    ],
];


// ─── Resolución de la ruta actual ──────────────────────────────────────────────
// Obtenemos el método HTTP y la URI limpia sin query string.
// parse_url extrae solo el componente path de la URL completa,
// descartando ?parametros=valor que no forman parte de la ruta.
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Normalizamos quitando el slash final, excepto en '/' (la raíz).
// Así /dashboard y /dashboard/ son equivalentes y no necesitamos duplicar rutas.
if ($uri !== '/') {
    $uri = rtrim($uri, '/');
}


// ─── Despacho de la petición ───────────────────────────────────────────────────
if (isset($routes[$method][$uri])) {
    $route          = $routes[$method][$uri];
    $controllerName = $route[0];
    $actionName     = $route[1];
    $guard          = $route[2] ?? null; // null = ruta pública

    // Aplicamos el middleware ANTES de instanciar el controller.
    // Si el guard falla (redirección), la ejecución para en exit() dentro del middleware
    // y el controller nunca llega a cargarse. Esto es intencional: el guard actúa
    // como portero antes de que el controller haga cualquier trabajo.
    if ($guard === 'auth') {
        // Solo usuarios autenticados pueden continuar.
        AuthMiddleware::check();
    } elseif ($guard === 'guest') {
        // Solo usuarios no autenticados pueden ver esta ruta (login, registro).
        AuthMiddleware::guest();
    }

    // Construimos la ruta al archivo del controller.
    // Convención: cada controller tiene su propio archivo con el mismo nombre de clase.
    // Ejemplo: DashboardController → backend/controllers/DashboardController.php
    $controllerFile = BACKEND_PATH . '/controllers/' . $controllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;

        // Instanciamos el controller y llamamos al método correspondiente a la ruta.
        // Los controllers son clases PHP normales, sin herencia forzada aún.
        $controller = new $controllerName();
        $controller->$actionName();
    } else {
        // El controller está registrado en la tabla de rutas pero el archivo no existe.
        // Esto es un error de desarrollo, no del usuario — lo logueamos y paramos.
        error_log("Controller no encontrado: {$controllerFile}");
        http_response_code(500);
        exit('Error interno: componente no disponible.');
    }

} elseif ($method === 'GET' && preg_match('#^/dashboard/negocios/([a-z0-9-]+)$#', $uri, $m)) {
    AuthMiddleware::check();
    $routeSlug = $m[1];
    require_once BACKEND_PATH . '/controllers/BusinessController.php';
    (new BusinessController())->showManage();

} elseif ($method === 'POST' && preg_match('#^/dashboard/negocios/([a-z0-9-]+)/edit$#', $uri, $m)) {
    AuthMiddleware::check();
    $routeSlug = $m[1];
    require_once BACKEND_PATH . '/controllers/BusinessController.php';
    (new BusinessController())->update();

} elseif ($method === 'POST' && preg_match('#^/dashboard/negocios/([a-z0-9-]+)/delete$#', $uri, $m)) {
    AuthMiddleware::check();
    $routeSlug = $m[1];
    require_once BACKEND_PATH . '/controllers/BusinessController.php';
    (new BusinessController())->delete();

} elseif ($method === 'POST' && preg_match('#^/dashboard/tours/([a-z0-9-]+)/delete$#', $uri, $m)) {
    AuthMiddleware::check();
    $routeSlug = $m[1];
    require_once BACKEND_PATH . '/controllers/TourController.php';
    (new TourController())->delete();

} elseif ($method === 'GET' && preg_match('#^/dashboard/negocios/([a-z0-9-]+)/tours/([a-z0-9-]+)$#', $uri, $m)) {
    AuthMiddleware::check();
    $routeParams = ['biz' => $m[1], 'tour' => $m[2]];
    require_once BACKEND_PATH . '/controllers/TourController.php';
    (new TourController())->showManage();

} elseif ($method === 'POST' && preg_match('#^/dashboard/negocios/([a-z0-9-]+)/tours/([a-z0-9-]+)/edit$#', $uri, $m)) {
    AuthMiddleware::check();
    $routeParams = ['biz' => $m[1], 'tour' => $m[2]];
    require_once BACKEND_PATH . '/controllers/TourController.php';
    (new TourController())->update();

} else {
    // La combinación método + URI no existe en la tabla de rutas.
    // Respondemos con 404 en lugar de dejar que PHP muestre un error genérico.
    http_response_code(404);

    // Intentamos cargar una vista de error personalizada para mejor UX.
    // Si no existe aún (fase temprana del desarrollo), mostramos respuesta mínima.
    $view404 = VIEWS_PATH . '/errors/404.php';
    if (file_exists($view404)) {
        require_once $view404;
    } else {
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>404 - Oxphyre</title></head><body><h1>404 - Página no encontrada</h1><p><a href="/">Volver al inicio</a></p></body></html>';
    }
}
