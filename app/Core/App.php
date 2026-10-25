<?php
/**
 * app/Core/App.php
 * Motor de arranque de la aplicación
 */

declare(strict_types=1);

namespace App\Core;

class App
{
    public static App $app;
    public Router $router;
    public Request $request;
    public Response $response;

    public function __construct()
    {
        self::$app = $this;
        $this->initSession();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', '1');
            }

            session_start();
        }
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve();
        } catch (\Throwable $e) {
            $this->response->setStatusCode(500);
            if (APP_ENV === 'development') {
                echo "<pre style='background:#f8d7da; color:#721c24; padding:15px; border-radius:5px;'>";
                echo "<strong>Excepción no capturada:</strong> " . htmlspecialchars($e->getMessage()) . "\n";
                echo "<strong>Archivo:</strong> " . $e->getFile() . " (Línea " . $e->getLine() . ")\n\n";
                echo "<strong>Traza de pila:</strong>\n" . htmlspecialchars($e->getTraceAsString());
                echo "</pre>";
            } else {
                error_log($e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
                require APP_PATH . '/Views/errors/500.php';
            }
        }
    }
}