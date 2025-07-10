<?php
class CategoriaModel {
    private $pdo;

    public function __construct() {
        $this->pdo = require dirname(__DIR__) . '/config/conexionbd.php';
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categorias");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($nombre) {
        $stmt = $this->pdo->prepare("INSERT INTO categorias (nombre_categoria) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function update($id, $nombre) {
        $stmt = $this->pdo->prepare("UPDATE categorias SET nombre_categoria=? WHERE id_categoria=?");
        return $stmt->execute([$nombre, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id_categoria=?");
        return $stmt->execute([$id]);
    }
}
?>
