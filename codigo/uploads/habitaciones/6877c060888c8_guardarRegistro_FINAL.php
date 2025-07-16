<?php
session_start();

$correo = $_POST['email'];
$documento = $_POST['numeroDocumento'];
$telefono = $_POST['numeroTelefono'];

require_once __DIR__ . '/../config/conexionbd.php';

// Verificar duplicados
$sqlVerificar = "SELECT * FROM usuarios WHERE email = ? OR numeroDocumento = ? OR numeroTelefono = ?";
$stmt = $pdo->prepare($sqlVerificar);
$stmt->execute([$correo, $documento, $telefono]);
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $_SESSION['registro_error'] = "El correo, número de documento o teléfono ya están registrados.";
    header("Location: ../views/registrar.php");
    exit();
}

require_once __DIR__ . '/../models/usuarioRegistro.php';
require_once __DIR__ . '/../services/ServicioEmail.php';

$usuario = new UsuarioRegistro($pdo);

// Registro
if (isset($_POST['registrarse'])) {
    $data = [
        'usu_idrol' => 2, // Rol por defecto (cliente)
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'tipodocumento' => $_POST['tipodocumento'],
        'numeroDocumento' => $_POST['numeroDocumento'],
        'numeroTelefono' => $_POST['numeroTelefono'],
        'paisProcedencia' => $_POST['paisProcedencia'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
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
        $data['password'],
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
?>
