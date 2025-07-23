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
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id_usuario'])) {
        throw new Exception('Debes iniciar sesión para realizar esta acción', 401);
    }

    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Formato de datos incorrecto', 400);
    }

    // Validar campos requeridos
    $requiredFields = ['claveActual', 'claveNueva', 'claveConfirmar'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Validar que las contraseñas coincidan
    if ($input['claveNueva'] !== $input['claveConfirmar']) {
        throw new Exception('Las contraseñas no coinciden', 400);
    }

    // Validar longitud de contraseña
    if (strlen($input['claveNueva']) < 6) {
        throw new Exception('La nueva contraseña debe tener al menos 6 caracteres', 400);
    }

    // Validar fortaleza de contraseña
    if (!preg_match('/[A-Z]/', $input['claveNueva']) || !preg_match('/[0-9]/', $input['claveNueva'])) {
        throw new Exception('La contraseña debe contener al menos una mayúscula y un número', 400);
    }

    $idUsuario = $_SESSION['usuario']['id_usuario'];

    // Obtener contraseña actual
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$idUsuario]);
    $result = $stmt->fetch();

    if (!$result) {
        throw new Exception('Usuario no encontrado', 404);
    }

    // Verificar contraseña actual
    if (!password_verify($input['claveActual'], $result['password'])) {
        throw new Exception('La contraseña actual es incorrecta', 401);
    }

    // Actualizar contraseña
    $hashedPassword = password_hash($input['claveNueva'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
    $stmt->execute([$hashedPassword, $idUsuario]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('No se realizaron cambios en la contraseña', 400);
    }

    $insertFecha = $pdo->prepare("INSERT INTO fechas (id_usuario, fecha, tipo) VALUES (?, NOW(), 'edición')");
    $insertFecha->execute([$idUsuario]);


    echo json_encode([
        'status' => 'success',
        'message' => 'Contraseña actualizada correctamente'
    ]);

} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}