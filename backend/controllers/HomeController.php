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

    public function showPricing(): void
    {
        require_once VIEWS_PATH . '/precios.php';
    }

    public function showVirtualTourForBusinesses(): void
    {
        require_once VIEWS_PATH . '/tour-virtual-para-negocios.php';
    }

    public function showVirtualTourForRestaurants(): void
    {
        require_once VIEWS_PATH . '/tour-virtual-para-restaurantes.php';
    }

    public function showBlog(): void
    {
        require_once VIEWS_PATH . '/blog/index.php';
    }

    public function showBlogPhotosGuide(): void
    {
        require_once VIEWS_PATH . '/blog/como-hacer-fotos-para-tour-virtual.php';
    }

    public function showBlogMobileTour(): void
    {
        require_once VIEWS_PATH . '/blog/tour-virtual-con-movil-sin-camara-360.php';
    }

    public function showBlogQrGuide(): void
    {
        require_once VIEWS_PATH . '/blog/como-usar-qr-para-ensenar-tu-local.php';
    }

    public function showAbout(): void
    {
        require_once VIEWS_PATH . '/sobre-nosotros.php';
    }

    public function showSupport(): void
    {
        require_once VIEWS_PATH . '/soporte.php';
    }

    public function showPrivacy(): void
    {
        require_once VIEWS_PATH . '/legal/privacidad.php';
    }

    public function showTerms(): void
    {
        require_once VIEWS_PATH . '/legal/terminos.php';
    }

    public function showCookies(): void
    {
        require_once VIEWS_PATH . '/legal/cookies.php';
    }
}
