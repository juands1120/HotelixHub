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
        try {
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
        } catch (Exception $e) {
            echo json_encode(['mensaje' => $e->getMessage()]);
        }
        break;

    case 'editar':
        try {
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
        } catch (Exception $e) {
            echo json_encode(['mensaje' => $e->getMessage()]);
        }
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
    if (isset($_FILES['imagen']) && $_FILES['imagen']['name'] != '') {
        $carpeta = __DIR__ . '/../uploads/productos/';
        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

        $nombreOriginal = $_FILES['imagen']['name'];
        $nombreTmp = $_FILES['imagen']['tmp_name'];
        $tipoMime = mime_content_type($nombreTmp);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        $tiposPermitidos = ['image/jpeg', 'image/png'];

        if (!in_array($extension, $extensionesPermitidas) || !in_array($tipoMime, $tiposPermitidos)) {
            throw new Exception("Solo se permiten imágenes JPG o PNG.");
        }

        $nombreFinal = uniqid() . '-' . basename($nombreOriginal);
        move_uploaded_file($nombreTmp, $carpeta . $nombreFinal);
        return 'uploads/productos/' . $nombreFinal;
    }
    return '';
}


?>
