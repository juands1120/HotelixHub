<?php
require_once __DIR__ . '/../models/usuarioLogin.php';
require_once __DIR__ . '/../config/conexionbd.php';
session_start();

$usuario = new Usuario($pdo);

// Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $usuario->login($email, $password);
    if ($user) {
        $_SESSION['usuario'] = $user;

        // Redireccionar según rol
        switch ($user['usu_idrol']) {
            case 1:
                header('Location: ../views/dashAdmin.php');
                break;
            case 2:
                header('Location: ../views/dashCliente.php');
                break;
            case 3:
            case 4:
            case 5:
                header('Location: ../views/dashAdmin.php');
                break;
            default:
                session_destroy();
                header('Location: ../views/login.php?error=Rol de usuario no válido');
                exit;
        }
        exit;
    } else {
        // Verificar si el correo existe
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->fetch()) {
            header('Location: ../views/login.php?error=Contraseña incorrecta');
        } else {
            header('Location: ../views/login.php?error=El correo no está registrado');
        }
        exit;
    }
}