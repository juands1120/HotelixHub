<?php
class CategoriaModel {
    private $pdo;

    public function __construct() {
        $this->pdo = require dirname(__DIR__) . '/config/conexionbd.php';
    }

    public function getAll() {
        $stmt = $this->pdo->query("CALL sp_listar_categorias()");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($nombre) {
        $stmt = $this->pdo->prepare("CALL sp_insertar_categoria(?)");
        return $stmt->execute([$nombre]);
    }

    public function update($id, $nombre) {
        $stmt = $this->pdo->prepare("CALL sp_editar_categoria(?, ?)");
        return $stmt->execute([$id, $nombre]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_categoria(?)");
        return $stmt->execute([$id]);
    }
}
?>
