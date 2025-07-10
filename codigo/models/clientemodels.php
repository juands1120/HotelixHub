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

    public function actualizarEstadoReserva($idReserva, $nuevoEstado) {
    $stmt = $this->pdo->prepare("CALL sp_actualizar_estado_reserva(:idReserva, :nuevoEstado)");
    $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
    $stmt->bindParam(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
    return $stmt->execute();
}

public function obtenerClienteConReservas($idUsuario) {
    $stmt = $this->pdo->prepare("CALL sp_obtener_clientes()");
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $datosCliente = null;
    $reservas = [];

    foreach ($clientes as $cliente) {
        if ($cliente['id_usuario'] == $idUsuario) {
            if (!$datosCliente) {
                // Guardamos los datos básicos del cliente (de la primera coincidencia)
                $datosCliente = [
                    'nombre' => $cliente['nombre'],
                    'apellido' => $cliente['apellido'],
                    'tipoDocumento' => $cliente['tipoDocumento'],
                    'numeroDocumento' => $cliente['numeroDocumento'],
                    'numeroTelefono' => $cliente['numeroTelefono'],
                    'paisProcedencia' => $cliente['paisProcedencia'],
                    'email' => $cliente['email']
                ];
            }

            // Si tiene reserva asociada
            if ($cliente['id_habitacion']) {
                $reservas[] = [
                    'fecha_reserva' => $cliente['fecha_reserva'],
                    'fecha_entrada' => $cliente['fecha_entrada'],
                    'fecha_salida' => $cliente['fecha_salida'],
                    'estado' => $cliente['estado'],
                    'nombre_hotel' => $cliente['nombre_habitacion']
                ];
            }
        }
    }

    return ['cliente' => $datosCliente, 'reservas' => $reservas];
}

}
?>
