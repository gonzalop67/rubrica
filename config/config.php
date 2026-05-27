<?php

//Configuración de acceso a la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'colegion_1');
define('DB_PASS', 'AQSWDE123');
define('DB_NAME', 'colegion_1');

// define global constants

//Ruta de la aplicación
define('RUTA_APP', dirname(dirname(__FILE__)) . '/App');

define('APP_NAME', 'SIAE_2025');

// Reemplaza tu define('RUTA_URL', ...) viejo por este:
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

// Si HTTP_HOST no existe (consola), asignamos tu URL local por defecto
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

define('RUTA_URL', $protocol . $host . '/rubrica');