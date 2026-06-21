<?php

spl_autoload_register(function ($className) {
    $raizProyecto = __DIR__; 
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $file = $raizProyecto . DIRECTORY_SEPARATOR . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
