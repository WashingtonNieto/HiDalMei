<?php
/**
 * app/Controllers/AdminController.php
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Middleware\RoleMiddleware;
use App\Models\Product;
use App\Models\Category;

class AdminController extends BaseController
{
    public function dashboard(Request $request, Response $response): string
    {
        RoleMiddleware::handle('ADMIN');

        $productModel = new Product();
        $categoryModel = new Category();

        $totalProducts = $productModel->getTotalCount();
        $categories = $categoryModel->getAllCategories();

        return $this->render('admin/dashboard', [
            'pageTitle'       => 'Panel de Control - HiDalMei',
            'totalProducts'   => $totalProducts,
            'totalCategories' => count($categories)
        ]);
    }
}
