<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/empleadoRegistro.php';

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

header('Content-Type: application/json');

$empleado = new empleadoRegistro($pdo);

$idEmpleado = $_GET['id']; // ID del empleado a eliminar
$idEliminador = $_SESSION['usuario']['id_usuario']; // ID del usuario logueado

try {
    $result = $empleado->eliminarEmpleado($idEmpleado, $idEliminador);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el empleado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
