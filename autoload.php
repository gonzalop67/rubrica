<?php

/**
 * Autoloader oficial e infalible para SIAE_2025
 * Registra y mapea de forma dinámica los Namespaces a rutas de archivos reales.
 */
spl_autoload_register(function ($className) {
    // 1. Convertimos los Namespaces con barras invertidas a rutas físicas amigables para el S.O.
    // Ejemplo: App\Controllers\LoginController -> App/Controllers/LoginController
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // 2. Usamos la constante del proyecto para armar la ruta absoluta exacta
    // C:\xampp\htdocs\siae_2025/App/Controllers/LoginController.php
    $file = RAIZ_PROYECTO . DIRECTORY_SEPARATOR . $classPath . '.php';

    // 3. Si el archivo existe, lo importamos de inmediato
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Plan B: Si la clase no empieza con "App", buscamos directamente en la raíz (ej: Core\Route)
        $rootFile = RAIZ_PROYECTO . DIRECTORY_SEPARATOR . $classPath . '.php';
        if (file_exists($rootFile)) {
            require_once $rootFile;
        }
    }
});
