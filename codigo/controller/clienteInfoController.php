<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/clienteModels.php';

session_start();

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "No autenticado"]);
    exit();
}

$idUsuario = $_SESSION['usuario']['id_usuario'];
$clienteModelo = new ClienteModelo($pdo);

try {
    $historial = $clienteModelo->obtenerHistorialReservasCliente($idUsuario);

    echo json_encode(["status" => "success", "data" => $historial]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}




?>
