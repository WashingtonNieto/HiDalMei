<?php
/**
 * public/index.php
 * Front Controller del proyecto HiDalMei
 */

declare(strict_types=1);

// Carga del archivo de configuración global
require_once dirname(__DIR__) . '/config/config.php';

// Autoload básico de clases cumpliendo el estándar PSR-4 sin Composer
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

// 1. Iniciar la aplicación
$app = new App();

// 2. Extraer el Router para que esté disponible en el scope del require
$router = $app->router;

// 3. Cargar el archivo de rutas (ahora $router sí existirá dentro de web.php)
require_once ROOT_PATH . '/routes/web.php';

// 4. Ejecutar la aplicación
$app->run();