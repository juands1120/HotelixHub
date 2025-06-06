<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Tiempo máximo de inactividad (en segundos)
$tiempoInactividad = 900; // 1 minutos

if (isset($_SESSION['usuario'])) {
    if (isset($_SESSION['ultimo_acceso'])) {
        $inactividad = time() - $_SESSION['ultimo_acceso'];
        if ($inactividad > $tiempoInactividad) {
            session_unset();
            session_destroy();
            header("Location: ../dash/login.php");
            exit();
        }
    }
    $_SESSION['ultimo_acceso'] = time();
}
?>
