<?php
require_once __DIR__ . '/../modelo/usuarioLogin.php';
require_once __DIR__ . '/../conexion/conexionbd.php';
session_start();

$usuario = new Usuario($pdo);

// Login
if (isset($_POST['login'])) {
    $user = $usuario->login($_POST['email'], $_POST['password']);
    if ($user) {
        $_SESSION['usuario'] = $user;

        // Redireccionar según rol
        switch ($user['usu_idrol']) {
            case 1:
                header('Location: ../vista/dash/dashAdmin.php');
                break;
            case 2:
                header('Location: ../vista/dash/dashCliente.php');
                break;
            case 3:
                header('Location: ../vista/dash/dashOtro.php');
                break;
            default:
                // Si no tiene rol válido, cerrar sesión y mostrar error
                session_destroy();
                echo "Rol de usuario no válido.";
                exit;
        }
        exit; // Siempre después de header() para evitar que se siga ejecutando el código
    } else {
        echo "Contraseña incorrecta.";
    }
}
