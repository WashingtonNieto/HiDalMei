<?php
/**
 * app/Controllers/BaseController.php
 * Controlador base con soporte para sistema de vistas
 */

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'main'): string
    {
        $content = $this->renderOnlyView($view, $data);
        $layoutContent = $this->renderLayout($layout, $data);
        return str_replace('{{content}}', $content, $layoutContent);
    }

    private function renderOnlyView(string $view, array $data): string
    {
        extract($data);
        ob_start();
        $viewPath = APP_PATH . "/Views/{$view}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<div class='alert alert-danger'>Error: La vista '{$view}' no existe.</div>";
        }
        return ob_get_clean();
    }

    private function renderLayout(string $layout, array $data): string
    {
        extract($data);
        ob_start();
        $layoutPath = APP_PATH . "/Views/layouts/{$layout}.php";
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo "{{content}}";
        }
        return ob_get_clean();
    }
}