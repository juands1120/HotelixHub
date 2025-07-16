<?php
$correo = $_POST['email'];
$documento = $_POST['numeroDocumento'];
$telefono = $_POST['numeroTelefono'];

// Verificar duplicados
include '../conexion.php';
$sqlVerificar = "SELECT * FROM usuarios WHERE email = ? OR numeroDocumento = ? OR numeroTelefono = ?";
$stmt = $conn->prepare($sqlVerificar);
$stmt->bind_param("sss", $correo, $documento, $telefono);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            let modal = document.getElementById('modalDuplicado');
            modal.style.display = 'block';
        });
    </script>";
    exit();
}

require_once __DIR__ . '/../models/usuarioRegistro.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../services/ServicioEmail.php'; 
session_start();

$usuario = new UsuarioRegistro($pdo);

// Registro
if (isset($_POST['registrarse'])) {
    $data = [
        'usu_idrol' => 2, // Rol por defecto (cliente)
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],|
        'tipodocumento' => $_POST['tipodocumento'],
        'numeroDocumento' => $_POST['numeroDocumento'],
        'numeroTelefono' => $_POST['numeroTelefono'],
        'paisProcedencia' => $_POST['paisProcedencia'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
    ];

    // Llamada al método con valores individuales
    $registroExitoso = $usuario->registrar(
        $data['usu_idrol'],
        $data['nombre'],
        $data['apellido'],
        $data['tipodocumento'],
        $data['numeroDocumento'],
        $data['numeroTelefono'],
        $data['paisProcedencia'],
        $data['email'],
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT),
        null, // reset_token
        null  // token_expires
    );

    if ($registroExitoso) {
        // Enviar correo de bienvenida
        $correo = $data['email'];
        $nombre = $data['nombre'];

        $servicioEmail = new ServicioEmail();
        $servicioEmail->enviarCorreoBienvenida($correo, $nombre);
    }

    header('Location: ../views/login.php');
    exit;
}
