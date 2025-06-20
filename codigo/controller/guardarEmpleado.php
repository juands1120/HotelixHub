<?php
require_once __DIR__ . '/../models/empleadoRegistro.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../services/ServicioEmail.php';
session_start();

$empleado = new empleadoRegistro($pdo);

if (isset($_POST['guardarEmpleado'])) {
    $data = [
        'usu_rol' => $_POST['usu_rol'],
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'tipoDocumento' => $_POST['tipoDocumento'],
        'numeroDocumento' => $_POST['numeroDocumento'],
        'numeroTelefono' => $_POST['numeroTelefono'],
        'direccion' => $_POST['direccion'],
        'email' => $_POST['email'],
        'estado' => $_POST['estado'],
        'password' => $_POST['password']
    ];

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $registroExitoso = $empleado->registrar(
        $data['usu_rol'],
        $data['nombre'],
        $data['apellido'],
        $data['tipoDocumento'],
        $data['numeroDocumento'],
        $data['numeroTelefono'],
        $data['direccion'],
        $data['email'],
        $data['estado'],
        $hashedPassword,
        null,
        null
    );

    if ($registroExitoso) {
        $servicioEmail = new ServicioEmail();
        $servicioEmail->enviarCorreoBienvenida($data['email'], $data['nombre']);
    }

    header('Location: ../views/login.php');
    exit;
}
