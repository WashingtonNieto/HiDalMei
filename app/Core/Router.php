<?php
/**
 * app/Core/Router.php
 * Mapeador de rutas con soporte para parámetros dinámicos
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    private Request $request;
    private Response $response;
    private array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback): void
    {
        $this->routes['GET'][$this->formatPath($path)] = $callback;
    }

    public function post(string $path, $callback): void
    {
        $this->routes['POST'][$this->formatPath($path)] = $callback;
    }

    private function formatPath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routePath => $callback) {
            // Convertir rutas con sintaxis tipo {id} o {slug} en expresiones regulares
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_\-]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_array($callback)) {
                    $controllerClass = $callback[0];
                    $methodName = $callback[1];

                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $methodName)) {
                            return call_user_func_array([$controller, $methodName], [$this->request, $this->response, $params]);
                        }
                    }
                }

                if (is_callable($callback)) {
                    return call_user_func($callback, $this->request, $this->response, $params);
                }
            }
        }

        // Ruta no encontrada
        $this->response->setStatusCode(404);
        return $this->renderError(404);
    }

    private function renderError(int $code): string
    {
        ob_start();
        $errorFile = APP_PATH . "/Views/errors/{$code}.php";
        if (file_exists($errorFile)) {
            require $errorFile;
        } else {
            echo "<h1>Error {$code}</h1><p>Página no encontrada.</p>";
        }
        return ob_get_clean();
    }
}