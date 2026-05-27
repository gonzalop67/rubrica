<?php

// 1. Forzar visualización de errores absoluta al inicio
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Ruta exacta al archivo de configuración usando __DIR__
$configFile = __DIR__ . '/../App/config/config.php';

// 3. Validar instalación de forma segura
if (!file_exists($configFile)) {
    // Si no estás en la carpeta install, redirige
    if (strpos($_SERVER['REQUEST_URI'], 'install') === false) {
        header("Location: ./install/index.php");
        exit;
    }
}

// 4. Inicializar sesión del framework
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Carga secuencial del Core utilizando rutas unificadas
require_once $configFile;
require_once __DIR__ . '/../Core/helpers.php';
require_once __DIR__ . '/../autoload.php';

// Ejecutamos las rutas dentro de un bloque try-catch para atrapar cualquier fallo del Router
try {
    require_once __DIR__ . '/../routes/web.php';
} catch (\Exception $e) {
    echo "<div style='padding:20px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;'>";
    echo "<strong>Error en el Enrutador:</strong> " . $e->getMessage();
    echo "</div>";
}
