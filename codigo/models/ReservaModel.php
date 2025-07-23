<?php
// models/ReservaModel.php
class ReservaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function crearReserva($datos) {
        $stmt = $this->pdo->prepare("CALL SP_CrearReserva(
            :id_usuario, :id_habitacion, :fecha_entrada, :fecha_salida, 
            :num_huespedes, :servicios_adicionales, :precio_total)");

        $success = $stmt->execute([
            ':id_usuario' => $datos['id_usuario'],
            ':id_habitacion' => $datos['id_habitacion'],
            ':fecha_entrada' => $datos['fecha_entrada'],
            ':fecha_salida' => $datos['fecha_salida'],
            ':num_huespedes' => $datos['num_huespedes'],
            ':servicios_adicionales' => $datos['servicios_adicionales'],
            ':precio_total' => $datos['precio_total']
        ]);

        // Obtener ID de la última reserva insertada
        if ($success) {
            $stmt = $this->pdo->query("SELECT LAST_INSERT_ID() as id");
            return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        }

        return false;
    }

    public function listarReservasPorEmail($email) {
        $stmt = $this->pdo->prepare("CALL SP_ListarReservasPorEmail(:email)");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelarReserva($id) {
        $stmt = $this->pdo->prepare("CALL SP_CancelarReserva(:id)");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerIdUsuarioPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn();
    }

    public function obtenerHabitacionDisponiblePorTipoYFechas($tipo, $fecha_entrada, $fecha_salida) {
        $sql = "SELECT h.id_habitacion 
                FROM habitacion h
                WHERE LOWER(h.tipoHabitacion) = LOWER(:tipo)
                AND NOT EXISTS (
                    SELECT 1 
                    FROM reserva r 
                    WHERE r.id_habitacion = h.id_habitacion
                    AND r.estado != 'Cancelada'
                    AND (
                        (r.fecha_entrada <= :fecha_salida AND r.fecha_salida >= :fecha_entrada)
                    )
                )
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tipo' => $tipo,
            ':fecha_entrada' => $fecha_entrada,
            ':fecha_salida' => $fecha_salida
        ]);

        return $stmt->fetchColumn();
    }

    public function verificarDisponibilidadFechas($id_habitacion, $fecha_entrada, $fecha_salida) {
        $stmt = $this->pdo->prepare("CALL SP_VerificarDisponibilidad(:id_habitacion, :fecha_entrada, :fecha_salida)");
        $stmt->execute([
            ':id_habitacion' => $id_habitacion,
            ':fecha_entrada' => $fecha_entrada,
            ':fecha_salida' => $fecha_salida
        ]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado && $resultado['disponible'] == 1;
    }

    public function habitacionOcupada($idHabitacion, $entrada, $salida, $idReservaActual) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM reserva
            WHERE id_habitacion = ? AND id_reserva != ? 
            AND fecha_entrada < ? AND fecha_salida > ?
            AND estado = 'Confirmada'
        ");
        $stmt->execute([$idHabitacion, $idReservaActual, $salida, $entrada]);
        return $stmt->fetchColumn() > 0;
    }

    public function editarReserva($id, $entrada, $salida) {
        $stmt = $this->pdo->prepare("UPDATE reserva SET fecha_entrada = ?, fecha_salida = ? WHERE id_reserva = ?");
        return $stmt->execute([$entrada, $salida, $id]);
    }

    public function obtenerReservaPorId($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.id_reserva,
                r.id_habitacion,
                r.fecha_entrada,
                r.fecha_salida,
                r.num_huespedes,
                r.servicios_adicionales,
                h.nombre AS nombre_habitacion,
                h.tipoHabitacion
            FROM reserva r
            INNER JOIN habitacion h ON r.id_habitacion = h.id_habitacion
            WHERE r.id_reserva = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function editarReservaFechas($idReserva, $idHabitacion, $fechaEntrada, $fechaSalida)
    {
        try {
            $stmt = $this->pdo->prepare("CALL SP_EditarReserva(:id, :habitacion, :entrada, :salida)");
            $stmt->bindParam(':id', $idReserva, PDO::PARAM_INT);
            $stmt->bindParam(':habitacion', $idHabitacion, PDO::PARAM_INT);
            $stmt->bindParam(':entrada', $fechaEntrada);
            $stmt->bindParam(':salida', $fechaSalida);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

public function registrarFecha($id_usuario, $tipo)
{
    try {
        $stmt = $this->pdo->prepare("INSERT INTO fechas (id_usuario, fecha, tipo) VALUES (:id_usuario, NOW(), :tipo)");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

}