<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../controller/reservaController.php';


$controller = new ReservaController($pdo);
$controller->manejarSolicitud();
