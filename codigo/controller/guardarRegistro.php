<?php
require_once __DIR__ . '/../modelo/usuarioRegistro.php';
require_once __DIR__ . '/../conexion/conexionbd.php';
require_once __DIR__ . '/ServicioEmail.php'; 
session_start();

$usuario = new UsuarioRegistro($pdo);

// Registro
if (isset($_POST['registrarse'])) {
    $data = [
        'usu_idrol' => 2, // Rol por defecto
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'tipodocumento' => $_POST['tipodocumento'],
        'numeroDocumento' => $_POST['numeroDocumento'],
        'numeroTelefono' => $_POST['numeroTelefono'],
        'paisProcedencia' => $_POST['paisProcedencia'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
    ];

    $registroExitoso = $usuario->registrar($data);

    if ($registroExitoso) {
        // Enviar correo de bienvenida
        $correo = $data['email'];
        $nombre = $data['nombre'];

        $servicioEmail = new ServicioEmail();
        $servicioEmail->enviarCorreoBienvenida($correo, $nombre);
    }

    header('Location: ../vista/dash/login.php');
    exit;
}
