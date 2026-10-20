<?php
/**
 * app/Middleware/RoleMiddleware.php
 * Guard de verificación de roles
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;

class RoleMiddleware
{
    public static function handle(string $requiredRole): bool
    {
        if (!AuthMiddleware::handle()) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? '';

        if (strtoupper($userRole) !== strtoupper($requiredRole)) {
            $_SESSION['flash_error'] = 'Acceso denegado. No tiene permisos para acceder a esta área.';
            $response = new Response();
            
            if ($userRole === 'CUSTOMER') {
                $response->redirect('/');
            } else {
                $response->redirect('/login');
            }
            return false;
        }

        return true;
    }
}