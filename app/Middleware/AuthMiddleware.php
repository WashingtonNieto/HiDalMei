<?php
/**
 * app/Middleware/AuthMiddleware.php
 * Guard de autenticación para validar sesiones activas
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;

class AuthMiddleware
{
    public static function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Debe iniciar sesión para acceder a esta área.';
            $response = new Response();
            $response->redirect('/login');
            return false;
        }

        return true;
    }
}