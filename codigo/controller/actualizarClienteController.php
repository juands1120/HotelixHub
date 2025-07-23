<?php
require_once __DIR__ . '/../config/conexionbd.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$idUsuario = $_SESSION['usuario']['id_usuario'];
$email = $data['email'] ?? null;
$telefono = $data['telefono'] ?? null;

if (!$email || !$telefono) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit();
}

try {
    // 1. Actualizar los datos del usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET email = ?, numeroTelefono = ? WHERE id_usuario = ?");
    $stmt->execute([$email, $telefono, $idUsuario]);

    // 2. Registrar la fecha de edición en la tabla fechas
    $insertFecha = $pdo->prepare("INSERT INTO fechas (id_usuario, fecha, tipo) VALUES (?, NOW(), 'edición')");
    $insertFecha->execute([$idUsuario]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
