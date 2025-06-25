<?php

class HabitacionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function crearHabitacion($datos) {
        $imagenRuta = $this->procesarImagen($datos);
        $stmt = $this->pdo->prepare("CALL SP_CrearHabitacion(:nombre, :tipo, :piso, :precio, :servicios, :estado, :imagen)");

        return $stmt->execute([
            ':nombre' => $datos['numero'],
            ':tipo' => $datos['tipo'],
            ':piso' => $datos['piso'],
            ':precio' => $datos['precio'],
            ':servicios' => $datos['servicios'],
            ':estado' => $datos['estado'],
            ':imagen' => $imagenRuta
        ]);
    }

    public function editarHabitacion($datos) {
        $imagenRuta = $this->procesarImagen($datos);
        $stmt = $this->pdo->prepare("CALL SP_EditarHabitacion(:numero, :tipo, :piso, :precio, :servicios, :estado, :imagen)");

        return $stmt->execute([
            ':numero' => $datos['numero'],
            ':tipo' => $datos['tipo'],
            ':piso' => $datos['piso'],
            ':precio' => $datos['precio'],
            ':servicios' => $datos['servicios'],
            ':estado' => $datos['estado'],
            ':imagen' => $imagenRuta
        ]);
    }

    public function eliminarHabitacion($numero) {
        $stmt = $this->pdo->prepare("CALL SP_EliminarHabitacion(:numero)");
        return $stmt->execute([':numero' => $numero]);
    }

    public function obtenerHabitaciones() {
        $stmt = $this->pdo->query("CALL SP_ObtenerHabitaciones()");
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($habitaciones as &$fila) {
            $fila['servicios'] = explode(',', $fila['serviciosIncluidos'] ?? '');
            $fila['numero'] = $fila['nombre'] ?? '';
            $fila['tipo'] = $fila['tipoHabitacion'] ?? '';

            // Proteger imágenes si están en formato base64 inválido
            if (isset($fila['imagen']) && str_starts_with($fila['imagen'], 'data:image')) {
                $fila['imagen'] = 'uploads/habitaciones/no-imagen.png';
            }
        }

        return $habitaciones;
    }

    // Función privada para manejar carga o conservación de imagen
    private function procesarImagen($datos) {
        if (isset($datos['imagen']) && $datos['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreTemporal = $datos['imagen']['tmp_name'];
            $nombreFinal = uniqid() . "_" . basename($datos['imagen']['name']);
            $rutaDestino = "uploads/habitaciones/" . $nombreFinal;

            move_uploaded_file($nombreTemporal, $rutaDestino);
            return $rutaDestino;
        }

        return $datos['imagenRuta'] ?? 'uploads/habitaciones/no-imagen.png';
    }

    public function obtenerHabitacionPorNumero($numero) {
        $stmt = $this->pdo->prepare("CALL SP_ObtenerHabitacionPorNumero(:numero)");
        $stmt->execute([':numero' => $numero]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($habitacion) {
            $habitacion['servicios'] = explode(',', $habitacion['serviciosIncluidos'] ?? '');
            $habitacion['numero'] = $habitacion['nombre'] ?? '';
            $habitacion['tipo'] = $habitacion['tipoHabitacion'] ?? '';
            
            if (isset($habitacion['imagen']) && str_starts_with($habitacion['imagen'], 'data:image')) {
                $habitacion['imagen'] = 'uploads/habitaciones/no-imagen.png';
            }
        }
        
        return $habitacion;
    }

    public function listarHabitacionesDisponibles() {
        $stmt = $this->pdo->query("CALL SP_ListarHabitacionesDisponibles()");
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($habitaciones as &$hab) {
            $hab['servicios'] = explode(',', $hab['serviciosIncluidos'] ?? '');
            $hab['numero'] = $hab['nombre'] ?? '';
            $hab['tipo'] = $hab['tipoHabitacion'] ?? '';

            if (isset($hab['imagen']) && str_starts_with($hab['imagen'], 'data:image')) {
                $hab['imagen'] = 'uploads/habitaciones/no-imagen.png';
            }
        }

        return $habitaciones;
    }



}
