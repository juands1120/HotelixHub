<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/empleadoRegistro.php';
require_once __DIR__ . '/../services/sessionManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar cabeceras para JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Verificar sesión activa
    if (!isset($_SESSION['usuario'])) {
        throw new Exception('Debes iniciar sesión para realizar esta acción', 401);
    }

    // Obtener datos del POST
    $input = $_POST;

    // Validar campos requeridos
    $requiredFields = ['id', 'email', 'numeroTelefono'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    $empleadoModel = new empleadoRegistro($pdo);
    $esAdministrador = $_SESSION['usuario']['usu_idrol'] == 1;
    $edicionPropioPerfil = !$esAdministrador && ($_SESSION['usuario']['id_usuario'] == $input['id']);

    // Preparar datos para actualización
    $data = [
        'id' => $input['id'],
        'nombre' => $input['nombre'] ?? '',
        'apellido' => $input['apellido'] ?? '',
        'tipoDocumento' => $input['tipoDocumento'] ?? '',
        'numeroDocumento' => $input['numeroDocumento'] ?? '',
        'numeroTelefono' => $input['numeroTelefono'],
        'email' => $input['email'],
        'direccion' => $input['direccion'] ?? '',
        'usu_idrol' => $esAdministrador ? ($input['usu_idrol'] ?? $_SESSION['usuario']['usu_idrol']) : $_SESSION['usuario']['usu_idrol'],
        'estado' => $esAdministrador ? ($input['estado'] ?? '') : ($_SESSION['usuario']['estado'] ?? '')
    ];

    // Validaciones adicionales
    if ($edicionPropioPerfil) {
        if ($empleadoModel->emailPerteneceAOtroUsuario($data['email'], $data['id'])) {
            throw new Exception('El correo electrónico ya está en uso por otro usuario', 400);
        }
        if ($empleadoModel->telefonoPerteneceAOtroUsuario($data['numeroTelefono'], $data['id'])) {
            throw new Exception('El número de teléfono ya está en uso por otro usuario', 400);
        }
    }

    // Actualizar empleado
    $result = $empleadoModel->actualizarEmpleado($data);

    if ($result) {
        if ($edicionPropioPerfil) {
            $_SESSION['usuario']['numeroTelefono'] = $data['numeroTelefono'];
            $_SESSION['usuario']['email'] = $data['email'];
            $_SESSION['usuario']['direccion'] = $data['direccion'];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Datos actualizados correctamente'
        ]);
    } else {
        throw new Exception('No se realizaron cambios en los datos', 400);
    }
} catch (PDOException $e) {
    error_log("PDOException en actualizarEmpleado: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor de base de datos'
    ]);
} catch (Exception $e) {
    error_log("Exception en actualizarEmpleado: " . $e->getMessage());
    http_response_code($e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
