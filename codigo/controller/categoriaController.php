<?php
require_once '../models/categoriaModel.php';
header('Content-Type: application/json');

$model = new CategoriaModel();
$accion = $_GET['accion'] ?? '';

switch($accion) {
    case 'listar':
        echo json_encode($model->getAll());
        break;

    case 'guardar':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? '';
        $nombre = $data['nombre'];

        if($id) {
            $res = $model->update($id, $nombre);
            echo json_encode(['mensaje' => $res ? 'Categoría actualizada' : 'Error al actualizar']);
        } else {
            $res = $model->insert($nombre);
            echo json_encode(['mensaje' => $res ? 'Categoría creada' : 'Error al crear']);
        }
        break;

    case 'eliminar':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $res = $model->delete($id);
        echo json_encode(['mensaje' => $res ? 'Categoría eliminada' : 'Error al eliminar']);
        break;

    default:
        echo json_encode(['mensaje' => 'Acción no válida']);
}
?>