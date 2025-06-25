<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config/conexionbd.php';
require_once __DIR__ . '/models/ReservaModel.php';



$tipo = $_GET['tipo'] ?? '';
$entrada = $_GET['entrada'] ?? '';
$salida = $_GET['salida'] ?? '';

if (!$tipo || !$entrada || !$salida) {
    echo json_encode(['error' => 'Parámetros incompletos']);
    exit;
}

$model = new ReservaModel($pdo);
$id = $model->obtenerHabitacionDisponiblePorTipoYFechas($tipo, $entrada, $salida);

if (!$id) {
    echo json_encode(['disponible' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT nombre FROM habitacion WHERE id_habitacion = :id");
$stmt->execute([':id' => $id]);
$hab = $stmt->fetch();

echo json_encode([
    'disponible' => true,
    'id_habitacion' => $id,
    'nombre_habitacion' => $hab['nombre']
]);
