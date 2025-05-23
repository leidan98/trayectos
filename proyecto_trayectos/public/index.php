<?php 

require_once '../config/database.php';

// Autoload simple
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../core/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Crear router
$router = new Router();

// Definir rutas
$router->add('/^$/', ['controller' => 'Home', 'action' => 'index']);
$router->add('/^login$/', ['controller' => 'Auth', 'action' => 'login']);
$router->add('/^authenticate$/', ['controller' => 'Auth', 'action' => 'authenticate']);
$router->add('/^logout$/', ['controller' => 'Auth', 'action' => 'logout']);

// Rutas de trayectos
$router->add('/^trayectos$/', ['controller' => 'Trayecto', 'action' => 'index']);
$router->add('/^trayectos\/create$/', ['controller' => 'Trayecto', 'action' => 'create']);
$router->add('/^trayectos\/store$/', ['controller' => 'Trayecto', 'action' => 'store']);
$router->add('/^trayectos\/approve$/', ['controller' => 'Trayecto', 'action' => 'approve']);
$router->add('/^trayectos\/process-approval$/', ['controller' => 'Trayecto', 'action' => 'processApproval']);
$router->add('/^trayectos\/confirm-all$/', ['controller' => 'Trayecto', 'action' => 'confirmAll']);

// Obtener URL
$url = $_GET['url'] ?? '';

// Despachar
try {
    $router->dispatch($url);
} catch (Exception $e) {
    if ($e->getCode() == 404) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Página no encontrada</h1>";
    } else {
        echo "<h1>Error: " . $e->getMessage() . "</h1>";
    }
}

 ?>