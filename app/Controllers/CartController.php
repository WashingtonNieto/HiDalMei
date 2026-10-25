<?php
/**
 * app/Controllers/CartController.php
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Models\Product;

class CartController extends BaseController
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index(Request $request, Response $response): string
    {
        return $this->render('cart/index', [
            'pageTitle' => 'Carrito de Compras - HiDalMei',
            'cart'      => $_SESSION['cart']
        ]);
    }

    public function add(Request $request, Response $response): void
    {
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad no válido.';
            $response->redirect('/catalog');
        }

        $productId = (int)($body['product_id'] ?? 0);
        $quantity  = max(1, (int)($body['quantity'] ?? 1));

        $product = $this->productModel->findById($productId);

        if (!$product) {
            $_SESSION['flash_error'] = 'El producto seleccionado no está disponible.';
            $response->redirect('/catalog');
        }

        $stock = (int)($product['stock'] ?? 0);
        if ($stock < $quantity) {
            $_SESSION['flash_error'] = 'La cantidad solicitada supera las existencias disponibles (' . $stock . ' uds).';
            $response->redirect('/catalog/detail/' . $productId);
        }

        $nombre = $product['nombre'] ?? $product['name'] ?? 'Producto';
        $imagen = !empty($product['imagen']) ? $product['imagen'] : (!empty($product['image_url']) ? $product['image_url'] : 'default_product.png');
        $precio = (float)($product['precio'] ?? $product['price'] ?? 0);

        if (isset($_SESSION['cart'][$productId])) {
            $newQty = $_SESSION['cart'][$productId]['quantity'] + $quantity;
            if ($newQty > $stock) {
                $_SESSION['flash_error'] = 'No puedes agregar más unidades de las disponibles en inventario.';
                $response->redirect('/cart');
            }
            $_SESSION['cart'][$productId]['quantity'] = $newQty;
        } else {
            $_SESSION['cart'][$productId] = [
                'id'        => $product['id'],
                'name'      => $nombre,
                'price'     => $precio,
                'image_url' => $imagen,
                'quantity'  => $quantity
            ];
        }

        $_SESSION['flash_success'] = '¡Producto agregado al carrito!';
        $response->redirect('/cart');
    }

    public function update(Request $request, Response $response): void
    {
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad no válido.';
            $response->redirect('/cart');
        }

        $productId = (int)($body['product_id'] ?? 0);
        $quantity  = (int)($body['quantity'] ?? 1);

        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
                $_SESSION['flash_success'] = 'Producto removido del carrito.';
            } else {
                $product = $this->productModel->findById($productId);
                $stock = (int)($product['stock'] ?? 0);
                if ($product && $quantity <= $stock) {
                    $_SESSION['cart'][$productId]['quantity'] = $quantity;
                    $_SESSION['flash_success'] = 'Cantidad actualizada correctamente.';
                } else {
                    $_SESSION['flash_error'] = 'La cantidad no está disponible en inventario.';
                }
            }
        }

        $response->redirect('/cart');
    }

    public function remove(Request $request, Response $response, array $params): void
    {
        $productId = (int)($params['id'] ?? 0);
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['flash_success'] = 'Producto eliminado del carrito.';
        }
        $response->redirect('/cart');
    }
}