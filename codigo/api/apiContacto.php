<?php
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../controller/contactoController.php';

$controller = new ContactoController($pdo);
$controller->manejarSolicitud();
