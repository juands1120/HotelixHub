<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../models/productoModel.php';

header('Content-Type: application/json');

try {
    $productoModel = new ProductoModel();
    $productos = $productoModel->getAll();

    // Aseguramos ruta correcta para imagen
    foreach ($productos as &$prod) {
        if (!str_starts_with($prod['imagen'], 'uploads/')) {
            $prod['imagen'] = 'uploads/productos/' . $prod['imagen'];
        }
    }

    echo json_encode($productos);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
