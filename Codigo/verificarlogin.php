<?php

session_start();
include("con_db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $correo = trim($_POST['correo']);
    $contraseña = trim($_POST['contraseña']);

    if (empty($correo) || empty($contraseña)) {
        echo '<script>alert("Por favor, completa todos los campos.");</script>';
        exit();
    }

    $stmt = $conexion->prepare("SELECT nombre, contraseña FROM usuario WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario;

            // Redirigir a la página de inicio o dashboard
            header("Location: index.php");
            exit();
        } else {
            echo '<script>alert("Contraseña incorrecta.");</script>';
        }
    } else {
        echo '<script>alert("El correo no está registrado.");</script>';
    }

    $stmt->close();
}



?>
