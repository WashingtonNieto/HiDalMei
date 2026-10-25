<?php
/**
 * public/index.php
 * Front Controller del proyecto HiDalMei
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

// Autoload básico PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\App;

// 1. Instanciar App
$app = new App();

// 2. Extraer la propiedad $router para pasársela a routes/web.php
$router = $app->router;

// 3. Cargar las rutas
require_once ROOT_PATH . '/routes/web.php';

// 4. Ejecutar la aplicación
$app->run();