<?php
session_start();
require_once __DIR__ . '/../config/conexionbd.php';

$correo = $_POST['email'];
$documento = $_POST['numeroDocumento'];
$telefono = $_POST['numeroTelefono'];

// Verificar si el correo ya existe
$stmt = $pdo->prepare("SELECT 1 FROM usuarios WHERE email = ?");
$stmt->execute([$correo]);
if ($stmt->fetch()) {
    $_SESSION['registro_error'] = "El correo electrónico ya está registrado.";
    header("Location: ../views/registrar.php");
    exit();
}

// Verificar si el número de documento ya existe
$stmt = $pdo->prepare("SELECT 1 FROM usuarios WHERE numeroDocumento = ?");
$stmt->execute([$documento]);
if ($stmt->fetch()) {
    $_SESSION['registro_error'] = "El número de documento ya está registrado.";
    header("Location: ../views/registrar.php");
    exit();
}

// Verificar si el número de teléfono ya existe
$stmt = $pdo->prepare("SELECT 1 FROM usuarios WHERE numeroTelefono = ?");
$stmt->execute([$telefono]);
if ($stmt->fetch()) {
    $_SESSION['registro_error'] = "El número de teléfono ya está registrado.";
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
        // Obtener el ID del usuario recién creado
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->execute([$data['email']]);
        $usuarioInsertado = $stmt->fetch();

        if ($usuarioInsertado) {
            $idUsuario = $usuarioInsertado['id_usuario'];

            // Insertar fecha de registro usando el modelo de fechas
            require_once __DIR__ . '/../models/FechaModelo.php';
            $fechaModel = new FechaModelo($pdo);
            $fechaModel->registrarFecha($idUsuario, 'registro');
        }

        // Enviar correo de bienvenida
        $servicioEmail = new ServicioEmail();
        $servicioEmail->enviarCorreoBienvenida($correo, $nombre);
    }


    header('Location: ../views/login.php');
    exit;
}
?>
