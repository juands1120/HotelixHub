<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../controller/ContactoController.php';

$controller = new ContactoController($pdo);
$controller->manejarSolicitud();
