<?php
session_start();
require_once __DIR__ . '/../config/conexionbd.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['usuario']) || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$id_reserva = (int) $data['id'];

$stmt = $pdo->prepare("DELETE FROM reserva WHERE id_reserva = :id AND id_usuario = :id_usuario");
$success = $stmt->execute(['id' => $id_reserva, 'id_usuario' => $id_usuario]);

echo json_encode(['success' => $success]);
