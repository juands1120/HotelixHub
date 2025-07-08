<?php
class ClienteModelo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerClientes() {
        $stmt = $this->pdo->prepare("CALL sp_obtener_clientes()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
