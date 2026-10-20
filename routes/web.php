<?php
/**
 * routes/web.php
 * Definición centralizada de rutas web
 */

use App\Controllers\HomeController;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);