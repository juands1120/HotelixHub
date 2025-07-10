<?php
require_once '../config/conexionbd.php';
require_once '../services/sessionManager.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['mensaje' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$data = json_decode(file_get_contents("php://input"), true);

// Insertar compra
$stmt = $pdo->prepare("INSERT INTO compras (id_usuario, nombre, email, metodo_pago, numero_tarjeta)
    VALUES (?, ?, ?, ?, ?)");
$res = $stmt->execute([
    $id_usuario,
    $data['nombre'],
    $data['email'],
    $data['metodo'],
    $data['metodo'] === 'efectivo' ? null : ($data['tarjeta'] ?? null)
]);

if ($res) {
    $id_compra = $pdo->lastInsertId();

    if (!empty($data['productos'])) {
        foreach ($data['productos'] as $item) {
            $stmtDetalle = $pdo->prepare("INSERT INTO detalle_compras (id_compra, nombre_producto, precio, cantidad)
                VALUES (?, ?, ?, ?)");
            $stmtDetalle->execute([
                $id_compra,
                $item['nombre'],
                $item['precio'],
                $item['cantidad']
            ]);
        }
    }

    echo json_encode(['mensaje' => 'Compra registrada con éxito']);
} else {
    echo json_encode(['mensaje' => 'Error al registrar compra']);
}
?>
