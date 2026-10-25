<?php
/**
 * app/Controllers/OrderController.php
 * Controlador para la gestión de Checkout, Mis Pedidos y Administración de Órdenes
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Models\Order;
use Exception;

class OrderController extends BaseController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Muestra la vista de checkout para confirmar dirección y pedido
     */
    public function showCheckout(Request $request, Response $response): string
    {
        AuthMiddleware::handle();

        if (empty($_SESSION['cart'])) {
            $_SESSION['flash_error'] = 'Tu carrito de compras está vacío.';
            $response->redirect('/catalog');
        }

        return $this->render('orders/checkout', [
            'pageTitle' => 'Confirmar Pedido - HiDalMei',
            'cart'      => $_SESSION['cart']
        ]);
    }

    /**
     * Procesa la compra de forma transaccional
     */
    public function processCheckout(Request $request, Response $response): void
    {
        AuthMiddleware::handle();
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad no válido. Por favor intente de nuevo.';
            $response->redirect('/orders/checkout');
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['flash_error'] = 'El carrito de compras se encuentra vacío.';
            $response->redirect('/catalog');
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Sesión de usuario no válida. Inicie sesión nuevamente.';
            $response->redirect('/login');
        }

        $address = trim($body['shipping_address'] ?? '');
        $notes   = trim($body['notes'] ?? '');

        if (empty($address)) {
            $_SESSION['flash_error'] = 'La dirección completa de envío es obligatoria.';
            $response->redirect('/orders/checkout');
        }

        try {
            $shippingCost = 12000.00; // Tarifa fija de envío
            $orderNumber = $this->orderModel->createOrder(
                $userId,
                $_SESSION['cart'],
                $shippingCost,
                $address,
                $notes
            );

            // Vaciar el carrito de compras al completar la transacción
            $_SESSION['cart'] = [];
            $_SESSION['flash_success'] = '¡Pedido ' . $orderNumber . ' generado con éxito! Puedes consultar su estado en el panel.';
            $response->redirect('/orders/my-orders');

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al procesar la compra: ' . $e->getMessage();
            $response->redirect('/orders/checkout');
        }
    }

    /**
     * Muestra el historial de pedidos del cliente autenticado
     */
    public function myOrders(Request $request, Response $response): string
    {
        AuthMiddleware::handle();
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $orders = $this->orderModel->getOrdersByUserId($userId);

        return $this->render('orders/my_orders', [
            'pageTitle' => 'Mis Pedidos - HiDalMei',
            'orders'    => $orders
        ]);
    }

    /**
     * Muestra el listado de todos los pedidos para el Administrador
     */
    public function adminOrders(Request $request, Response $response): string
    {
        RoleMiddleware::handle('ADMIN');
        
        $orders = $this->orderModel->getAllOrders();

        return $this->render('admin/orders/index', [
            'pageTitle' => 'Gestión de Pedidos - HiDalMei',
            'orders'    => $orders
        ]);
    }

    /**
     * Actualiza el estado de un pedido desde el Panel Administrativo
     */
    public function updateStatus(Request $request, Response $response, array $params): void
    {
        RoleMiddleware::handle('ADMIN');
        $id = (int)($params['id'] ?? 0);
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad no válido.';
            $response->redirect('/admin/orders');
        }

        $status = trim($body['status'] ?? '');
        if (empty($status)) {
            $_SESSION['flash_error'] = 'Debe seleccionar un estado válido.';
            $response->redirect('/admin/orders');
        }

        if ($this->orderModel->updateOrderStatus($id, $status)) {
            $_SESSION['flash_success'] = 'Estado del pedido actualizado correctamente.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar el estado del pedido.';
        }

        $response->redirect('/admin/orders');
    }
}