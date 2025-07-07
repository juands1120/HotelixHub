<?php
require_once __DIR__ . '/../models/empleadoRegistro.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../services/ServicioEmail.php';
session_start();

$empleado = new empleadoRegistro($pdo);

if (isset($_POST['guardarEmpleado'])) {
    $data = [
        'usu_idrol' => $_POST['usu_idrol'],
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'tipoDocumento' => $_POST['tipoDocumento'],
        'numeroDocumento' => $_POST['numeroDocumento'],
        'numeroTelefono' => $_POST['numeroTelefono'],
        'email' => strtolower(trim($_POST['email'])),
        'password' => $_POST['password'],
        'estado' => $_POST['estado'],
        'direccion' => $_POST['direccion']
    ];

    // Validaciones: documento, teléfono y correo deben ser únicos
    if ($empleado->findByEmail($data['email'])) {
        header("Location: ../views/formEmpleados.php?error=correo");
        exit;
    }

    if ($empleado->findByDocumento($data['numeroDocumento'])) {
        header("Location: ../views/formEmpleados.php?error=documento");
        exit;
    }

    if ($empleado->findByTelefono($data['numeroTelefono'])) {
        header("Location: ../views/formEmpleados.php?error=telefono");
        exit;
    }

    // Hashear la contraseña
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Registrar empleado
    $registroExitoso = $empleado->registrar(
        $data['usu_idrol'],
        $data['nombre'],
        $data['apellido'],
        $data['tipoDocumento'],
        $data['numeroDocumento'],
        $data['numeroTelefono'],
        null, // paisProcedencia como NULL
        $data['email'],
        $hashedPassword,
        null,
        null,
        $data['estado'],
        $data['direccion']
    );

    if ($registroExitoso) {
        $servicioEmail = new ServicioEmail();
        $servicioEmail->enviarCorreoBienvenida($data['email'], $data['nombre']);
    }

    header('Location: ../views/formEmpleados.php?registro=exitoso');
    exit;
}

