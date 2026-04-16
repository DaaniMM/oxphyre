<?php

/**
 * HomeController — Gestiona la ruta GET /.
 *
 * Siguiendo el patrón MVC, el controller no genera HTML directamente.
 * Su única responsabilidad es coordinar: preparar los datos que la vista
 * necesita y delegarle el renderizado. Toda la lógica de negocio va en modelos.
 */
class HomeController
{
    /**
     * Muestra la página principal (landing page).
     *
     * Por ahora carga una vista de placeholder. Cuando se desarrolle la landing
     * real (Paso 5 del DEVLOG), este método pasará datos a la vista:
     * planes de precios, testimonios, configuración SEO, etc.
     */
    public function index(): void
    {
        // Cargamos la vista usando la constante VIEWS_PATH definida en config.php.
        // Así la ruta es absoluta y no depende del directorio de trabajo del servidor.
        require_once VIEWS_PATH . '/home.php';
    }
}
