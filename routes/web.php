<?php

// Definir rutas

use Core\Route;

// Ahora sí encontrará perfectamente la carpeta Core en la raíz del proyecto
require_once RAIZ_PROYECTO . '/Core/middlewares.php';

use App\Controllers\LoginController;

Route::get('/', [LoginController::class, 'showLoginForm']);

Route::dispatch();