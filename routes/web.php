<?php

// Definir rutas

use App\Controllers\Admin\AdminDashboardController;
use Core\Route;

// Ahora sí encontrará perfectamente la carpeta Core en la raíz del proyecto
require_once RAIZ_PROYECTO . '/Core/middlewares.php';

use App\Controllers\LoginController;

Route::get('/', [LoginController::class, 'showLoginForm']);

Route::post('/auth/login', [LoginController::class, 'login']);
Route::get('/auth/logout', [LoginController::class, 'logout']);

Route::get('admin/dashboard', [AdminDashboardController::class, 'index'], [$authMiddleware]);

Route::dispatch();