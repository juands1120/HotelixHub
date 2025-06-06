<?php
require_once __DIR__ . '/../conexion/conexionbd.php';
require_once __DIR__ . '/../modelo/usuarioRegistro.php';

$userModel = new UsuarioRegistro($pdo);

$token = $_GET['token'] ?? null;
$nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
$confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

if (!$token) {
    die("Token inválido.");
}

if (empty($nuevaContrasena) || empty($confirmarContrasena)) {
    die("Debe llenar ambos campos de contraseña.");
}

if ($nuevaContrasena !== $confirmarContrasena) {
    die("Las contraseñas no coinciden.");
}

if (strlen($nuevaContrasena) < 6) {
    die("La contraseña debe tener al menos 6 caracteres.");
}

// Buscar usuario por token válido
$user = $userModel->findByToken($token);

if (!$user) {
    die("Token inválido o expirado.");
}

// Actualizar la contraseña
if ($userModel->updatePassword($user['id_usuario'], $nuevaContrasena)) {
    // Limpiar el token para que no pueda reutilizarse
    $userModel->clearResetToken($user['id_usuario']);

    // Redirigir a login con mensaje de éxito
    header("Location: ../vista/dash/login.php?reset=success");
    exit;
} else {
    die("Error al actualizar la contraseña.");
}


