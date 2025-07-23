<?php
class FechaModelo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarFecha($id_usuario, $tipo) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO fechas (id_usuario, fecha, tipo) VALUES (?, NOW(), ?)");
            $ejecutado = $stmt->execute([$id_usuario, $tipo]);

            if (!$ejecutado) {
                error_log("Error al ejecutar INSERT en fechas");
            }

            return $ejecutado;
        } catch (PDOException $e) {
            error_log("Excepción al registrar fecha: " . $e->getMessage());
            return false;
        }
    }
}
