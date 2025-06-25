<?php
header('Content-Type: application/json');

require_once "config/conexionbd.php";
require_once "controller/HabitacionController.php";

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

