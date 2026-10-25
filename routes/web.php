<?php
/**
 * routes/web.php
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\CatalogController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\AdminController;
use App\Controllers\AdminCategoryController;
use App\Controllers\AdminProductController;

/** @var \App\Core\Router $router */

// Rutas Públicas y Catálogo
$router->get('/', [HomeController::class, 'index']);
$router->get('/catalog', [CatalogController::class, 'index']);
$router->get('/catalog/detail/{id}', [CatalogController::class, 'detail']);

// Carrito de Compras
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->get('/cart/remove/{id}', [CartController::class, 'remove']);

// Checkout y Pedidos (NUEVAS RUTAS)
$router->get('/orders/checkout', [OrderController::class, 'showCheckout']);
$router->post('/orders/checkout', [OrderController::class, 'processCheckout']);
$router->get('/orders/my-orders', [OrderController::class, 'myOrders']);

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

// Pedidos Admin
$router->get('/admin/orders', [OrderController::class, 'adminOrders']);
$router->post('/admin/orders/update-status/{id}', [OrderController::class, 'updateStatus']);