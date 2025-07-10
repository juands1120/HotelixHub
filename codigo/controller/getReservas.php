<?php
require_once __DIR__ . '/../config/conexionbd.php';

header('Content-Type: application/json');

try {
    // Obtener las fechas desde el formulario (AJAX)
    $fechaInicio = $_GET['fechaInicio'] ?? null;
    $fechaFin = $_GET['fechaFin'] ?? null;

    if (!$fechaInicio || !$fechaFin) {
        echo json_encode(["error" => "Fechas no válidas"]);
        exit;
    }

    // Preparar llamada al procedimiento almacenado
    $stmt = $pdo->prepare("CALL sp_reservas_completadas_por_fecha(:fecha_inicio, :fecha_fin)");
    $stmt->bindParam(':fecha_inicio', $fechaInicio);
    $stmt->bindParam(':fecha_fin', $fechaFin);
    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
