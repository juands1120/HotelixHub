<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/empleadoRegistro.php';

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$empleado = new empleadoRegistro($pdo);

$data = [
    'id' => $_POST['id'],
    'nombre' => $_POST['nombre'],
    'apellido' => $_POST['apellido'],
    'tipoDocumento' => $_POST['tipoDocumento'],
    'numeroDocumento' => $_POST['numeroDocumento'],
    'numeroTelefono' => $_POST['numeroTelefono'],
    'email' => $_POST['email'],
    'direccion' => $_POST['direccion'],
    'usu_idrol' => $_POST['usu_idrol'],
    'estado' => $_POST['estado']
];

try {
    $result = $empleado->actualizarEmpleado($data);
    
    if ($result) {
        header('Location: ../views/formEmpleados.php?success=1');
    } else {
        header('Location: ../views/formEmpleados.php?error=update');
    }
} catch (PDOException $e) {
    header('Location: ../views/formEmpleados.php?error=' . urlencode($e->getMessage()));
}