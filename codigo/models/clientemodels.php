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

    public function obtenerHistorialReservasCliente($idUsuario) {
        $stmt = $this->pdo->prepare("CALL sp_obtener_clientes()");
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filtrar todas las reservas de ese usuario
        $reservas = [];
        foreach ($clientes as $cliente) {
            if ($cliente['id_usuario'] == $idUsuario && $cliente['id_habitacion']) {
                $reservas[] = $cliente;
            }
        }

        return $reservas;
    }


}
?>
