<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$usuario = $_SESSION['usuario'];

echo json_encode([
    'id_usuario' => $usuario['id_usuario'],
    'nombre' => $usuario['nombre'],
    'apellido' => $usuario['apellido'],
    'tipoDocumento' => $usuario['tipoDocumento'],
    'numeroDocumento' => $usuario['numeroDocumento'],
    'numeroTelefono' => $usuario['numeroTelefono'],
    'email' => $usuario['email']
]);
