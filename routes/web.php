<?php
/**
 * routes/web.php
 * Registro centralizado de rutas del sistema
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Middleware\RoleMiddleware;

/** @var App\Core\Router $router */

// Rutas Públicas
$router->get('/', [HomeController::class, 'index']);

// Rutas de Autenticación
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->post('/logout', [AuthController::class, 'logout']);

// Ejemplo de Ruta Protegida solo para Administrador
$router->get('/admin/dashboard', function($request, $response) {
    if (RoleMiddleware::handle('ADMIN')) {
        echo "<h1>Panel de Control de Administración - HiDalMei</h1>";
        echo "<p>Acceso concedido únicamente al rol ADMIN.</p>";
        echo "<a href='" . APP_URL . "/'>Volver a la tienda</a>";
    }
});




