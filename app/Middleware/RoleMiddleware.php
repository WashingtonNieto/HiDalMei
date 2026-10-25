<?php
/**
 * app/Middleware/RoleMiddleware.php
 * Guard de verificación de roles de usuario
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;
use App\Middleware\AuthMiddleware;

class RoleMiddleware
{
    public static function handle(string $requiredRole): bool
    {
        // Invoca el guard de autenticación primero
        if (!AuthMiddleware::handle()) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? '';

        // Comprueba si el rol coincide con el requerido (ADMIN o CLIENTE)
        if (strtoupper($userRole) !== strtoupper($requiredRole) && $userRole !== 'ADMINISTRADOR') {
            $_SESSION['flash_error'] = 'Acceso denegado. No posee permisos de administrador.';
            $response = new Response();
            
            if ($userRole === 'CLIENTE' || $userRole === 'CUSTOMER') {
                $response->redirect('/');
            } else {
                $response->redirect('/login');
            }
            return false;
        }

        return true;
    }
}