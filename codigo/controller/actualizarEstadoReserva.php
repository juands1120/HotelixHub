<?php
require_once '../config/conexionbd.php';
require_once '../services/sessionManager.php';

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Verificar sesión y permisos
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

// Validar datos de entrada
if (!isset($_POST['id_reserva']) || !isset($_POST['estado'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

$id_reserva = filter_var($_POST['id_reserva'], FILTER_VALIDATE_INT);
$estado = $_POST['estado'];

// Validar valores permitidos
$estadosPermitidos = ['Pendiente', 'Confirmada', 'Cancelada', 'Sin reserva'];
if (!in_array($estado, $estadosPermitidos)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Estado no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("CALL sp_actualizar_estado_reserva(:id_reserva, :estado)");
    $stmt->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
    $stmt->execute();

    // Verificar si se actualizó alguna fila
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Estado actualizado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró la reserva o no hubo cambios']);
    }
} catch (PDOException $e) {
    error_log('Error al actualizar estado de reserva: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error en el servidor']);
}
?>
