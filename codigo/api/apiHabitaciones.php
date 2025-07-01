<?php
error_reporting(0); // Oculta warnings y notices
ini_set('display_errors', 0);
require_once('../config/conexionbd.php');
require_once('../controller/HabitacionController.php');
header('Content-Type: application/json');

try {
    $controller = new HabitacionController($pdo);
    $controller->manejarSolicitud();
} catch (Throwable $e) {
    echo json_encode([
        'exito' => false,
        'error' => 'Error interno del servidor',
        'detalles' => $e->getMessage()
    ]);
}

