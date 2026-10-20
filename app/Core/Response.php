<?php
/**
 * app/Core/Response.php
 * Manejo de la respuesta HTTP
 */

declare(strict_types=1);

namespace App\Core;

class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $url): void
    {
        if (strpos($url, 'http') !== 0) {
            $url = APP_URL . '/' . ltrim($url, '/');
        }
        header("Location: " . $url);
        exit;
    }

    public function json(array $data, int $statusCode = 200): void
    {
        $this->setStatusCode($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}