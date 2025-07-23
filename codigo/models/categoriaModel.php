<?php
class CategoriaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
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
    public function editar($id, $nombre) {
    $query = "UPDATE categoria SET nombre = ? WHERE id_categoria = ?";
    $stmt = $this->pdo->prepare($query);
    return $stmt->execute([$nombre, $id]);
}
}
?>
