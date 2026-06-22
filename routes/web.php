<?php

// Definir rutas

use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\PermisoController;
use App\Controllers\Admin\RoleController;
use Core\Route;

// Ahora sí encontrará perfectamente la carpeta Core en la raíz del proyecto
require_once RAIZ_PROYECTO . '/Core/middlewares.php';

use App\Controllers\LoginController;

Route::get('/', [LoginController::class, 'showLoginForm']);

Route::post('/auth/login', [LoginController::class, 'login']);
Route::get('/auth/logout', [LoginController::class, 'logout']);

Route::get('admin/dashboard', [AdminDashboardController::class, 'index'], [$authMiddleware]);

/** Rutas para Permisos (Quita el / del inicio si tu enrutador usa trim($uri, '/')) */
Route::get('permissions', [PermisoController::class, 'index'], [$authMiddleware]);
Route::get('permissions/create', [PermisoController::class, 'create'], [$authMiddleware]);
Route::post('permissions', [PermisoController::class, 'store'], [$authMiddleware]);
// Ver el listado de la papelera (GET)
Route::get('permissions/wastebasket', [PermisoController::class, 'wastebasket'], [$authMiddleware]);
Route::post('permissions/:id/restore', [PermisoController::class, 'restore'], [$authMiddleware]);
Route::post('permissions/:id/destroy', [PermisoController::class, 'destroy'], [$authMiddleware]);
// Rutas comunes
Route::get('permissions/:id/edit', [PermisoController::class, 'edit'], [$authMiddleware]);
Route::post('permissions/:id/update', [PermisoController::class, 'update'], [$authMiddleware]);
Route::post('permissions/:id/delete', [PermisoController::class, 'delete'], [$authMiddleware]);

/** Rutas para Roles */
Route::get('/roles', [RoleController::class, 'index'], [$authMiddleware]);
Route::get('/roles/create', [RoleController::class, 'create'], [$authMiddleware]);
Route::post('/roles', [RoleController::class, 'store'], [$authMiddleware]);
// Ver el listado de la papelera (GET)
Route::get('/roles/wastebasket', [RoleController::class, 'wastebasket'], [$authMiddleware]);
Route::post('/roles/:id/restore', [RoleController::class, 'restore'], [$authMiddleware]);
Route::post('/roles/:id/destroy', [RoleController::class, 'destroy'], [$authMiddleware]);
// Rutas comunes
Route::get('/roles/:id/edit', [RoleController::class, 'edit'], [$authMiddleware]);
Route::post('/roles/:id/update', [RoleController::class, 'update'], [$authMiddleware]);
// Ruta para la eliminación "suave"
Route::post('/roles/:id/delete', [RoleController::class, 'delete'], [$authMiddleware]);

Route::dispatch();