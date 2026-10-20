<?php
/**
 * app/Core/Request.php
 * Encapsula la petición HTTP y normaliza la ruta removiendo la carpeta base
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Limpiar query params (?foo=bar)
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        // Obtener el script actual (ej: /hidalmei/public/index.php)
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $baseDir = dirname($scriptName); // Ej: /hidalmei/public

        // Remover /public del baseDir para considerar la raíz del proyecto
        $projectDir = preg_replace('#/public$#i', '', $baseDir);

        // Si la URI empieza con el directorio del proyecto o de public, removerlo
        if (!empty($projectDir) && $projectDir !== '/' && preg_match('#^' . preg_quote($projectDir, '#') . '#i', $path)) {
            $path = substr($path, strlen($projectDir));
        }

        if (preg_match('#^/public#i', $path)) {
            $path = substr($path, 7);
        }

        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'POST') {
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
        return $body;
    }
}