<?php
/**
 * app/Controllers/AdminCategoryController.php
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Middleware\RoleMiddleware;
use App\Models\Category;

class AdminCategoryController extends BaseController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(Request $request, Response $response): string
    {
        RoleMiddleware::handle('ADMIN');
        $categories = $this->categoryModel->getAllCategories();

        return $this->render('admin/categories/index', [
            'pageTitle'  => 'Gestión de Categorías - HiDalMei',
            'categories' => $categories
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        RoleMiddleware::handle('ADMIN');
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF no válido.';
            $response->redirect('/admin/categories');
        }

        $name = trim($body['name'] ?? '');
        $description = trim($body['description'] ?? '');
        $isActive = isset($body['is_active']) ? 1 : 0;

        if (empty($name)) {
            $_SESSION['flash_error'] = 'El nombre de la categoría es obligatorio.';
            $response->redirect('/admin/categories');
        }

        if ($this->categoryModel->nameExists($name)) {
            $_SESSION['flash_error'] = 'Ya existe una categoría registrada con ese nombre.';
            $response->redirect('/admin/categories');
        }

        if ($this->categoryModel->createCategory($name, $description, $isActive)) {
            $_SESSION['flash_success'] = 'Categoría creada exitosamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al crear la categoría en el sistema.';
        }

        $response->redirect('/admin/categories');
    }

    public function update(Request $request, Response $response, array $params): void
    {
        RoleMiddleware::handle('ADMIN');
        $id = (int)($params['id'] ?? 0);
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF no válido.';
            $response->redirect('/admin/categories');
        }

        $name = trim($body['name'] ?? '');
        $description = trim($body['description'] ?? '');
        $isActive = isset($body['is_active']) ? 1 : 0;

        if ($this->categoryModel->nameExists($name, $id)) {
            $_SESSION['flash_error'] = 'Ya existe otra categoría registrada con ese nombre.';
            $response->redirect('/admin/categories');
        }

        if ($this->categoryModel->updateCategory($id, $name, $description, $isActive)) {
            $_SESSION['flash_success'] = 'Categoría actualizada correctamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar la categoría.';
        }

        $response->redirect('/admin/categories');
    }

    public function delete(Request $request, Response $response, array $params): void
    {
        RoleMiddleware::handle('ADMIN');
        $id = (int)($params['id'] ?? 0);
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF no válido.';
            $response->redirect('/admin/categories');
        }

        if ($this->categoryModel->hasAssociatedProducts($id)) {
            $_SESSION['flash_error'] = 'No se puede eliminar esta categoría porque tiene productos asociados.';
            $response->redirect('/admin/categories');
        }

        if ($this->categoryModel->deleteCategory($id)) {
            $_SESSION['flash_success'] = 'Categoría eliminada del catálogo.';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar la categoría.';
        }

        $response->redirect('/admin/categories');
    }
}
