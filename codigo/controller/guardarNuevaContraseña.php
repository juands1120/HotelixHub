<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/usuarioRegistro.php';

$userModel = new UsuarioRegistro($pdo);

$token = $_GET['token'] ?? null;
$nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
$confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

if (!$token) {
    $message = "Token inválido.";
    $mostrar_modal = true;
    include __DIR__ . '/../views/nuevaContraseña.php';
    exit;
}

if (empty($nuevaContrasena) || empty($confirmarContrasena)) {
    $message = "Debe llenar ambos campos de contraseña.";
    $mostrar_modal = true;
    include __DIR__ . '/../views/nuevaContraseña.php';
    exit;
}

if ($nuevaContrasena !== $confirmarContrasena) {
    $message = "Las contraseñas no coinciden.";
    $mostrar_modal = true;
    include __DIR__ . '/../views/nuevaContraseña.php';
    exit;
}

if (strlen($nuevaContrasena) < 6) {
    $message = "La contraseña debe tener al menos 6 caracteres.";
    $mostrar_modal = true;
    include __DIR__ . '/../views/nuevaContraseña.php';
    exit;
}

// Buscar usuario por token válido
$user = $userModel->findByToken($token);

if (!$user) {
    $message = "Token inválido o expirado.";
    $mostrar_modal = true;
    include __DIR__ . '/../views/nuevaContraseña.php';
    exit;
}

// Actualizar la contraseña
if ($userModel->updatePassword($user['id_usuario'], $nuevaContrasena)) {
    $userModel->clearResetToken($user['id_usuario']);
    header("Location: ../views/login.php?reset=success");
    exit;
} else {
    $message = "Error al actualizar la contraseña.";
    $mostrar_modal = true;
    include __DIR__ . '/../views/nuevaContraseña.php';
    exit;
}
?>



