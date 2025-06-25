<?php
session_start();
require_once __DIR__ . '/../config/conexionbd.php';

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

$stmt = $pdo->prepare("
    SELECT r.*, h.nombre AS habitacion_nombre
    FROM reserva r
    JOIN habitacion h ON r.id_habitacion = h.id_habitacion
    WHERE r.id_usuario = :id_usuario
    ORDER BY r.fecha_reserva DESC
");
$stmt->execute(['id_usuario' => $id_usuario]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($reservas);
