<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../services/sessionManager.php';

// Configurar cabeceras primero
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Verificar sesión
    if (!isset($_SESSION['usuario'])) {
        throw new Exception('Debes iniciar sesión para realizar esta acción', 401);
    }

    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Formato de datos incorrecto', 400);
    }

    // Validar campos requeridos
    $requiredFields = ['email', 'numeroTelefono', 'direccion'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Validar formato de email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El email proporcionado no es válido', 400);
    }

    // Validar formato de teléfono
    if (!preg_match('/^[\d\s]+$/', $input['numeroTelefono'])) {
        throw new Exception('El teléfono solo puede contener números y espacios', 400);
    }

    $idUsuario = $_SESSION['usuario']['id_usuario'];

    // Verificar si el email ya está en uso por otro usuario
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
    $stmt->execute([$input['email'], $idUsuario]);
    if ($stmt->fetch()) {
        throw new Exception('El correo electrónico ya está en uso por otro usuario', 400);
    }

    // Actualizar datos del empleado
    $stmt = $pdo->prepare("UPDATE usuarios SET 
                          email = ?, 
                          numeroTelefono = ?, 
                          direccion = ? 
                          WHERE id_usuario = ?");
    $stmt->execute([
        $input['email'],
        $input['numeroTelefono'],
        $input['direccion'],
        $idUsuario
    ]);

    // Actualizar datos en sesión
    $_SESSION['usuario']['email'] = $input['email'];
    $_SESSION['usuario']['numeroTelefono'] = $input['numeroTelefono'];
    $_SESSION['usuario']['direccion'] = $input['direccion'];

    echo json_encode([
        'status' => 'success',
        'message' => 'Datos actualizados correctamente'
    ]);

} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor de base de datos'
    ]);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}