<?php
/**
 * app/Controllers/HomeController.php
 * Controlador inicial de la tienda
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

class HomeController extends BaseController
{
    public function index(Request $request, Response $response): string
    {
        return $this->render('home/index', [
            'pageTitle' => 'HiDalMei - Tu Conexión con Asia',
            'appName'   => APP_NAME
        ]);
    }
}