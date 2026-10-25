<?php
/**
 * routes/web.php
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\CatalogController;
use App\Controllers\AdminController;
use App\Controllers\AdminCategoryController;
use App\Controllers\AdminProductController;

/** @var \App\Core\Router $router */

// Rutas Públicas y Catálogo
$router->get('/', [HomeController::class, 'index']);
$router->get('/catalog', [CatalogController::class, 'index']);
$router->get('/catalog/detail/{id}', [CatalogController::class, 'detail']);

// Autenticación
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// Panel Administrativo
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);

// CRUD Categorías (Admin)
$router->get('/admin/categories', [AdminCategoryController::class, 'index']);
$router->post('/admin/categories/create', [AdminCategoryController::class, 'store']);
$router->post('/admin/categories/update/{id}', [AdminCategoryController::class, 'update']);
$router->post('/admin/categories/delete/{id}', [AdminCategoryController::class, 'delete']);

// CRUD Productos (Admin)
$router->get('/admin/products', [AdminProductController::class, 'index']);
$router->get('/admin/products/create', [AdminProductController::class, 'create']);
$router->post('/admin/products/create', [AdminProductController::class, 'store']);
$router->get('/admin/products/edit/{id}', [AdminProductController::class, 'edit']);
$router->post('/admin/products/edit/{id}', [AdminProductController::class, 'update']);
$router->post('/admin/products/delete/{id}', [AdminProductController::class, 'delete']);