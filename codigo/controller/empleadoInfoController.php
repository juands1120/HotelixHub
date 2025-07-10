<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/empleadoRegistro.php';
require_once __DIR__ . '/../services/sessionManager.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Verificar sesión
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id_usuario'])) {
        throw new Exception('Acceso no autorizado', 401);
    }

    $empleadoModel = new empleadoRegistro($pdo);
    $idUsuario = $_SESSION['usuario']['id_usuario'];
    
    // Obtener datos del empleado
    $empleado = $empleadoModel->obtenerEmpleadoPorId($idUsuario);
    
    if (!$empleado) {
        throw new Exception('Empleado no encontrado', 404);
    }

    // Preparar respuesta
    echo json_encode([
        'status' => 'success',
        'data' => [
            'id_usuario' => $empleado['id_usuario'],
            'nombre' => $empleado['nombre'],
            'apellido' => $empleado['apellido'] ?? '',
            'tipoDocumento' => $empleado['tipoDocumento'] ?? '',
            'numeroDocumento' => $empleado['numeroDocumento'],
            'numeroTelefono' => $empleado['numeroTelefono'] ?? '',
            'paisProcedencia' => $empleado['paisProcedencia'] ?? '',
            'email' => $empleado['email'],
            'estado' => $empleado['estado'] ?? '',
            'direccion' => $empleado['direccion'] ?? '',
            'rol_nombre' => $empleado['rol_nombre'] ?? ''
        ]
    ]);

} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de base de datos'
    ]);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}