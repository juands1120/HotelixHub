<?php
require_once '../models/categoriaModel.php';
require_once '../models/FechaModelo.php';
require_once '../config/conexionbd.php';
session_start();

header('Content-Type: application/json');

$model = new CategoriaModel($pdo);
$fechaModel = new FechaModelo($pdo);

$accion = $_GET['accion'] ?? '';

switch($accion) {
    case 'listar':
        echo json_encode($model->getAll());
        break;

    case 'guardar':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;

        if ($id) {
            $res = $model->update($id, $nombre);

            if ($res) {
                $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
                if ($idUsuario) {
                    $fechaModel->registrarFecha($idUsuario, 'edición');
                }
            }

            echo json_encode(['mensaje' => $res ? 'Categoría actualizada' : 'Error al actualizar']);
        } else {
            $res = $model->insert($nombre);

            if ($res) {
                $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
                if ($idUsuario) {
                    $fechaModel->registrarFecha($idUsuario, 'registro');
                }
            }

            echo json_encode(['mensaje' => $res ? 'Categoría creada' : 'Error al crear']);
        }
        break;

    case 'eliminar':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $res = $model->delete($id);

        if ($res) {
            $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
            if ($idUsuario) {
                $fechaModel->registrarFecha($idUsuario, 'eliminación');
            }
        }

        echo json_encode(['mensaje' => $res ? 'Categoría eliminada' : 'Error al eliminar']);
        break;

    default:
        echo json_encode(['mensaje' => 'Acción no válida']);
}
?>
