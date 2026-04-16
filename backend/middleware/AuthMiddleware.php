<?php

/**
 * Middleware de autenticación.
 *
 * Centraliza la verificación de sesión para no repetir la misma lógica
 * en cada controller que necesite proteger sus rutas.
 *
 * El router llama a estos métodos ANTES de instanciar el controller correspondiente.
 * Si la verificación falla, la ejecución se detiene con exit() y el controller
 * nunca llega a ejecutarse. El usuario recibe una redirección HTTP limpia.
 *
 * Métodos disponibles:
 *   AuthMiddleware::check() → solo usuarios autenticados pueden pasar.
 *   AuthMiddleware::guest() → solo usuarios NO autenticados pueden pasar.
 */
class AuthMiddleware
{
    /**
     * Protege rutas que requieren sesión activa (dashboard, perfil, etc.).
     *
     * Verifica que $_SESSION['user_id'] existe, es un entero y es mayor que cero.
     * No basta con comprobar que existe: un atacante podría establecer session_id = 'admin'
     * si no validamos el tipo. Forzamos int > 0 para que coincida con los IDs reales de la BD.
     *
     * Si el usuario no está autenticado:
     *   1. Guardamos la URL a la que intentaba ir (para redirigir tras login exitoso).
     *   2. Redirigimos a /login con header HTTP 302.
     *   3. exit() detiene la ejecución — sin exit, PHP seguiría procesando el controller.
     */
    public static function check(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (empty($userId) || !is_int($userId) || $userId <= 0) {
            // Guardamos la URL destino para redirigir al usuario después del login.
            // Solo guardamos si la URI actual no es /login para evitar loops.
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            if ($requestUri !== '/login') {
                $_SESSION['redirect_after_login'] = $requestUri;
            }

            header('Location: /login');
            exit();
        }
    }

    /**
     * Protege rutas de invitado (/login, /registro).
     *
     * Si el usuario ya tiene una sesión activa, no tiene sentido mostrarle
     * el formulario de login. Lo mandamos directamente al dashboard.
     *
     * Sin este guard, un usuario logueado podría acceder a /login,
     * lo que es confuso desde el punto de vista de UX y podría
     * causar comportamientos inesperados con las sesiones.
     */
    public static function guest(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!empty($userId) && is_int($userId) && $userId > 0) {
            header('Location: /dashboard');
            exit();
        }
    }
}
