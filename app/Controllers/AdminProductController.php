<?php
/**
 * app/Controllers/AdminProductController.php
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Middleware\RoleMiddleware;
use App\Models\Product;
use App\Models\Category;
use App\Services\ImageUploader;
use Exception;

class AdminProductController extends BaseController
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(Request $request, Response $response): string
    {
        RoleMiddleware::handle('ADMIN');

        $body = $request->getBody();
        $page = max(1, (int)($body['page'] ?? 1));
        $search = trim($body['search'] ?? '');
        $categoryId = (int)($body['category_id'] ?? 0);

        $perPage = 8;
        $products = $this->productModel->getPaginatedProducts($page, $perPage, $search, $categoryId);
        $total = $this->productModel->getTotalCount($search, $categoryId);
        $totalPages = (int)ceil($total / $perPage);

        $categories = $this->categoryModel->getAllCategories();

        return $this->render('admin/products/index', [
            'pageTitle'   => 'Gestión de Productos - HiDalMei',
            'products'    => $products,
            'categories'  => $categories,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'search'      => $search,
            'categoryId'  => $categoryId
        ]);
    }

    public function create(Request $request, Response $response): string
    {
        RoleMiddleware::handle('ADMIN');
        $categories = $this->categoryModel->getAllCategories(true);

        return $this->render('admin/products/create', [
            'pageTitle'  => 'Nuevo Producto - HiDalMei',
            'categories' => $categories
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        RoleMiddleware::handle('ADMIN');
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF no válido.';
            $response->redirect('/admin/products/create');
        }

        $sku           = trim($body['sku'] ?? '');
        $name          = trim($body['name'] ?? '');
        $categoryId    = (int)($body['category_id'] ?? 0);
        $originCountry = $body['origin_country'] ?? '';
        $price         = (float)($body['price'] ?? 0.0);
        $stock         = (int)($body['stock'] ?? 0);
        $status        = $body['status'] ?? 'ACTIVE';
        $description   = trim($body['description'] ?? '');

        if (empty($sku) || empty($name) || $categoryId <= 0 || !in_array($originCountry, ['KR', 'JP', 'CN'])) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos obligatorios.';
            $response->redirect('/admin/products/create');
        }

        if ($price < 0 || $stock < 0) {
            $_SESSION['flash_error'] = 'El precio y las existencias deben ser valores mayor o igual a cero.';
            $response->redirect('/admin/products/create');
        }

        if ($this->productModel->skuExists($sku)) {
            $_SESSION['flash_error'] = 'El código SKU ingresado ya existe en la base de datos.';
            $response->redirect('/admin/products/create');
        }

        $imageName = 'default_product.png';
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploader = new ImageUploader();
                $imageName = $uploader->upload($_FILES['image']);
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error al procesar la imagen: ' . $e->getMessage();
                $response->redirect('/admin/products/create');
            }
        }

        $success = $this->productModel->createProduct([
            'category_id'    => $categoryId,
            'sku'            => $sku,
            'name'           => $name,
            'description'    => $description,
            'origin_country' => $originCountry,
            'price'          => $price,
            'stock'          => $stock,
            'image_url'      => $imageName,
            'status'         => $status
        ]);

        if ($success) {
            $_SESSION['flash_success'] = 'Producto registrado correctamente.';
            $response->redirect('/admin/products');
        } else {
            $_SESSION['flash_error'] = 'Error al registrar el producto.';
            $response->redirect('/admin/products/create');
        }
    }

    public function edit(Request $request, Response $response, array $params): string
    {
        RoleMiddleware::handle('ADMIN');
        $id = (int)($params['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'Producto no encontrado.';
            $response->redirect('/admin/products');
        }

        $categories = $this->categoryModel->getAllCategories();

        return $this->render('admin/products/edit', [
            'pageTitle'  => 'Editar Producto - HiDalMei',
            'product'    => $product,
            'categories' => $categories
        ]);
    }

public function update(Request $request, Response $response, array $params): void
    {
        RoleMiddleware::handle('ADMIN');
        $id = (int)($params['id'] ?? 0);
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF no válido.';
            $response->redirect("/admin/products/edit/{$id}");
        }

        $existingProduct = $this->productModel->findById($id);
        if (!$existingProduct) {
            $_SESSION['flash_error'] = 'Producto no encontrado.';
            $response->redirect('/admin/products');
        }

        $sku           = trim($body['sku'] ?? '');
        $name          = trim($body['name'] ?? '');
        $categoryId    = (int)($body['category_id'] ?? 0);
        $originCountry = $body['origin_country'] ?? '';
        $price         = (float)($body['price'] ?? 0.0);
        $stock         = (int)($body['stock'] ?? 0);
        $status        = $body['status'] ?? 'ACTIVE';
        $description   = trim($body['description'] ?? '');

        // Obtener nombre de imagen previa desde la clave en español o inglés
        $oldImageName = $existingProduct['imagen'] ?? $existingProduct['image_url'] ?? null;

        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploader = new ImageUploader();
                $imageName = $uploader->upload($_FILES['image']);
                $uploader->deleteOldImage($oldImageName);
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error al actualizar imagen: ' . $e->getMessage();
                $response->redirect("/admin/products/edit/{$id}");
            }
        }

        $success = $this->productModel->updateProduct($id, [
            'category_id'    => $categoryId,
            'sku'            => $sku,
            'name'           => $name,
            'description'    => $description,
            'origin_country' => $originCountry,
            'price'          => $price,
            'stock'          => $stock,
            'image_url'      => $imageName,
            'status'         => $status
        ]);

        if ($success) {
            $_SESSION['flash_success'] = 'Producto modificado exitosamente.';
            $response->redirect('/admin/products');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar el producto.';
            $response->redirect("/admin/products/edit/{$id}");
        }
    }
    

    public function delete(Request $request, Response $response, array $params): void
    {
        RoleMiddleware::handle('ADMIN');
        $id = (int)($params['id'] ?? 0);
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF no válido.';
            $response->redirect('/admin/products');
        }

        $product = $this->productModel->findById($id);
        if ($product) {
            $uploader = new ImageUploader();
            $uploader->deleteOldImage($product['image_url']);
            $this->productModel->deleteProduct($id);
            $_SESSION['flash_success'] = 'Producto eliminado del catálogo.';
        } else {
            $_SESSION['flash_error'] = 'Producto no encontrado.';
        }

        $response->redirect('/admin/products');
    }
}
