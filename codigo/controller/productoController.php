<?php
require_once '../models/productoModel.php';

header('Content-Type: application/json');
$accion = $_GET['accion'] ?? '';
$model = new ProductoModel();

switch($accion) {
    case 'listar':
        echo json_encode($model->getAll());
        break;

    case 'guardar':
        $nombreArchivo = subirImagen();
        $data = [
            'nombre' => $_POST['nombre'],
            'precio' => $_POST['precio'],
            'descripcion' => $_POST['descripcion'],
            'imagen' => $nombreArchivo,
            'stock' => $_POST['stock'],
            'id_categoria' => $_POST['id_categoria']
        ];
        $res = $model->insert($data);
        echo json_encode(['mensaje' => $res ? 'Producto guardado' : 'Error al guardar']);
        break;

    case 'editar':
        $nombreArchivo = subirImagen();
        $data = [
            'nombre' => $_POST['nombre'],
            'precio' => $_POST['precio'],
            'descripcion' => $_POST['descripcion'],
            'imagen' => $nombreArchivo ?: $_POST['imagen_actual'],
            'stock' => $_POST['stock'],
            'id_categoria' => $_POST['id_categoria'],
            'id' => $_POST['id']
        ];
        $res = $model->update($data);
        echo json_encode(['mensaje' => $res ? 'Producto actualizado' : 'Error al actualizar']);
        break;

    case 'eliminar':
        $data = json_decode(file_get_contents('php://input'), true);
        $res = $model->delete($data['id']);
        echo json_encode(['mensaje' => $res ? 'Producto eliminado' : 'Error al eliminar']);
        break;

    default:
        echo json_encode(['mensaje' => 'Acción no válida']);
}

function subirImagen() {
    if(isset($_FILES['imagen']) && $_FILES['imagen']['name'] != '') {
        $carpeta = __DIR__ . '/../uploads/productos/';
        if(!file_exists($carpeta)) mkdir($carpeta, 0777, true);
        $nombre = uniqid() . '-' . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombre);
        return 'uploads/productos/' . $nombre;
    }
    return '';
}

?>
