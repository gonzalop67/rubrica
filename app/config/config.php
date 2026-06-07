<?php

// 1. Configuración de acceso a la base de datos
if (!defined('DB_HOST'))     define('DB_HOST', 'localhost');
if (!defined('DB_USER'))     define('DB_USER', 'colegion_1');
if (!defined('DB_PASS'))     define('DB_PASS', 'AQSWDE123');
if (!defined('DB_NAME'))     define('DB_NAME', 'colegion_1');
if (!defined('APP_NAME'))    define('APP_NAME', 'SIAE_2025');

// 2. Cálculo infalible de rutas absolutas basadas en la posición real del archivo
// Si este archivo está en App/config/config.php:
// __DIR__ es App/config. El dirname(__DIR__) retrocede un nivel y obtiene la carpeta App.
if (!defined('RUTA_APP')) {
    define('RUTA_APP', dirname(__DIR__)); 
}

// RAIZ_PROYECTO retrocede un nivel más arriba desde RUTA_APP, llegando a siae_2025
if (!defined('RAIZ_PROYECTO')) {
    define('RAIZ_PROYECTO', dirname(RUTA_APP));
}

// 3. Configuración dinámica de la URL amigable (Compatible con Consola y Servidor Web)
if (php_sapi_name() === 'cli') {
    // Si estás en consola, definimos valores fijos por defecto para evitar romper el script
    if (!defined('RUTA_URL')) {
        define('RUTA_URL', 'http://localhost/rubrica');
    }
} else {
    // Si estás en el navegador web, calculamos dinámicamente
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    if (!defined('RUTA_URL')) {
        define('RUTA_URL', $protocol . $host . '/rubrica');
    }
}

date_default_timezone_set("America/Guayaquil");
