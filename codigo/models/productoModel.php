<?php
class ProductoModel {
    private $pdo;

    public function __construct() {
        $this->pdo = require dirname(__DIR__) . '/config/conexionbd.php';
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT p.*, c.nombre_categoria 
            FROM productos p 
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO productos (nombre, precio, descripcion, imagen, stock, id_categoria)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
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
        $stmt = $this->pdo->prepare("
            UPDATE productos SET nombre=?, precio=?, descripcion=?, imagen=?, stock=?, id_categoria=?
            WHERE id=?
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['descripcion'],
            $data['imagen'],
            $data['stock'],
            $data['id_categoria'],
            $data['id']
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
