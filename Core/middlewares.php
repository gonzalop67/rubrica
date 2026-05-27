<?php

// middlewares.php
$authMiddleware = function () {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['authenticated'])) {
        header('Location: ' . RUTA_URL . '/');
        exit(); // Corta el flujo si no está autenticado
    }
};

$adminMiddleware = function () {
    if ($_SESSION['role'] !== 'admin') {
        die("Acceso denegado: No eres administrador");
    }
};
