<?php

require_once BACKEND_PATH . '/controllers/BaseController.php';

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->ensureCsrfToken();
        require_once VIEWS_PATH . '/dashboard/index.php';
    }
}
