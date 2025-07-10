<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Configuración de tiempo de inactividad (15 minutos)
    $tiempoInactividad = 900;
    
    if (isset($_SESSION['usuario'])) {
        if (isset($_SESSION['ultimo_acceso'])) {
            $inactividad = time() - $_SESSION['ultimo_acceso'];
            if ($inactividad > $tiempoInactividad) {
                session_unset();
                session_destroy();
                if (php_sapi_name() !== 'cli') {
                    header("Location: ../views/login.php");
                    exit();
                }
            }
        }
        $_SESSION['ultimo_acceso'] = time();
    }
}

// Función para verificar autenticación
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario'])) {
        if (php_sapi_name() !== 'cli') {
            header("Location: ../views/login.php");
            exit();
        }
    }
}

// Función para verificar rol
function verificarRol($rolesPermitidos) {
    verificarAutenticacion();
    if (!in_array($_SESSION['usuario']['usu_idrol'], $rolesPermitidos)) {
        if (php_sapi_name() !== 'cli') {
            header("Location: ../views/acceso-denegado.php");
            exit();
        }
    }
}