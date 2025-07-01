<?php
require_once __DIR__ . '/config/conexionbd.php';
require_once __DIR__ . '/controllers/ContactoController.php';

$controller = new ContactoController($pdo);
$controller->manejarSolicitud();
