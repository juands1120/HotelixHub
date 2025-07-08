<?php
// 1. Conexión a base de datos
require_once __DIR__ . '/../config/conexionbd.php';

// 2. Importar el modelo
require_once __DIR__ . '/../models/clienteModels.php';

// 3. Iniciar sesión y validar login
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// 4. Indicar que la respuesta será JSON
header('Content-Type: application/json');

// 5. Crear instancia del modelo y traer clientes
$clienteModelo = new ClienteModelo($pdo);

try {
    $clientes = $clienteModelo->obtenerClientes();
    
    // 6. Devolver JSON con los datos de los clientes
    echo json_encode([
        "status" => "success",
        "data" => $clientes
    ]);
} catch (PDOException $e) {
    // Si ocurre un error, devolver mensaje
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
