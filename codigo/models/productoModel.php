<?php
class ProductoModel {
    private $pdo;

    public function __construct() {
        $this->pdo = require dirname(__DIR__) . '/config/conexionbd.php';
    }

    public function getAll() {
        $stmt = $this->pdo->query("CALL sp_listar_productos()");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $stmt = $this->pdo->prepare("CALL sp_insertar_producto(?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['descripcion'],
            $data['imagen'],
            $data['stock'],
            $data['id_categoria']
        ]);
    }

    public function update($data) {
        $stmt = $this->pdo->prepare("CALL sp_editar_producto(?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['id'],
            $data['nombre'],
            $data['precio'],
            $data['descripcion'],
            $data['imagen'],
            $data['stock'],
            $data['id_categoria']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_producto(?)");
        return $stmt->execute([$id]);
    }
}
?>
