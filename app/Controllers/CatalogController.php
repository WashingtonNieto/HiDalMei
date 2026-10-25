<?php
/**
 * app/Controllers/CatalogController.php
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;

class CatalogController extends BaseController
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
        $body = $request->getBody();
        $page = max(1, (int)($body['page'] ?? 1));
        $search = trim($body['search'] ?? '');
        $categoryId = (int)($body['category_id'] ?? 0);

        $perPage = 9;
        $products = $this->productModel->getPaginatedProducts($page, $perPage, $search, $categoryId);
        $total = $this->productModel->getTotalCount($search, $categoryId);
        $totalPages = (int)ceil($total / $perPage);

        $categories = $this->categoryModel->getAllCategories(true);

        return $this->render('catalog/index', [
            'pageTitle'   => 'Catálogo de Productos - HiDalMei',
            'products'    => $products,
            'categories'  => $categories,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'search'      => $search,
            'categoryId'  => $categoryId
        ]);
    }

    public function detail(Request $request, Response $response, array $params): string
    {
        $id = (int)($params['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'El producto solicitado no está disponible.';
            $response->redirect('/catalog');
        }

        return $this->render('catalog/detail', [
            'pageTitle' => htmlspecialchars($product['nombre'] ?? 'Producto') . ' - HiDalMei',
            'product'   => $product
        ]);
    }
}