<?php

// 1. Carga PRIMERO el archivo de configuración para que existan las constantes
// (Asegúrate de que la ruta hacia tu archivo de configuración sea la correcta)
require_once __DIR__ . '/App/config/config.php'; 

// 2. Ahora que las constantes existen, cargas el archivo de la clase
require_once __DIR__ . '/Core/MiniBlade.php'; 

use Core\MiniBlade;

$cachePath = __DIR__ . '/cache';
$blade = new MiniBlade(__DIR__ . '/views', $cachePath);

$cantidad = $blade->clearCache();

echo "Caché optimizada\n";
echo "Se han eliminado $cantidad archivos temporales.";
