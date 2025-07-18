<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/../models/reservaModel.php';

class ReservaController {
    private $model;

    public function __construct($pdo) {
        $this->model = new ReservaModel($pdo);
    }

    public function manejarSolicitud() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $accion = '';
        $datos = [];

        if ($metodo === 'POST') {
            $json = file_get_contents('php://input');
            $datos = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Formato de datos inválido',
                    'json_error' => json_last_error_msg()
                ]);
                return;
            }

            $accion = $datos['accion'] ?? '';
        } elseif ($metodo === 'GET') {
            $accion = $_GET['accion'] ?? '';
        }

        // Log de entrada (opcional)
        if (!empty($datos)) {
            file_put_contents(__DIR__ . '/../log_reserva.txt', print_r($datos, true));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($datos['accion']) && $datos['accion'] === 'editar') {
            file_put_contents(__DIR__ . '/../debug_editar.txt', print_r($datos, true));
            try {
                $id = $datos['id_reserva'];
                $nuevaEntrada = $datos['checkIn'];
                $nuevaSalida = $datos['checkOut'];
                $idHabitacion = $datos['id_habitacion'];

                if ($this->model->habitacionOcupada($idHabitacion, $nuevaEntrada, $nuevaSalida, $id)) {
                    echo json_encode(['success' => false, 'error' => 'La habitación ya está reservada en ese rango']);
                    return;
                }

                $resultado = $this->model->editarReserva($id, $nuevaEntrada, $nuevaSalida);

                if ($resultado) {
                    echo json_encode(['success' => true, 'mensaje' => 'Reserva actualizada con éxito']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al actualizar reserva']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Excepción: ' . $e->getMessage()]);
            }
            return;
        }


        switch ($accion) {
            case 'crear':
                $camposRequeridos = ['nombre', 'telefono', 'email', 'huesped', 'tipoHabitacion', 'checkIn', 'checkOut', 'id_habitacion'];
                foreach ($camposRequeridos as $campo) {
                    if (empty($datos[$campo])) {
                        echo json_encode(['success' => false, 'error' => "El campo $campo es requerido"]);
                        return;
                    }
                }

                $servicios = $datos['servicios'] ?? [];
                if (count($servicios) > 3) {
                    echo json_encode(['success' => false, 'error' => 'Máximo 3 servicios permitidos']);
                    return;
                }

                $id_usuario = $this->model->obtenerIdUsuarioPorEmail($datos['email']);
                $id_habitacion = $datos['id_habitacion'];

                if (!$id_usuario) {
                    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
                    return;
                }

                if (!$this->model->verificarDisponibilidadFechas($id_habitacion, $datos['checkIn'], $datos['checkOut'])) {
                    echo json_encode(['success' => false, 'error' => 'La habitación seleccionada está ocupada en esas fechas.']);
                    return;
                }

                $noches = (strtotime($datos['checkOut']) - strtotime($datos['checkIn'])) / (60 * 60 * 24);
                if ($noches < 1 || $noches > 30) {
                    echo json_encode(['success' => false, 'error' => 'La reserva debe tener entre 1 y 30 noches']);
                    return;
                }

                $preciosHabitacion = [
                    'sencilla' => 150000,
                    'doble' => 220000,
                    'triple' => 300000
                ];

                $preciosServicios = [
                    "Spa" => 80000,
                    "Desayuno Buffet" => 35000 * (int)$datos['huesped'],
                    "Parqueadero" => 20000 * $noches,
                    "Lavandería" => 45000,
                    "Transporte" => 60000
                ];

                // 1. Precio habitación por noches
                $precioBasePorNoche = $preciosHabitacion[strtolower($datos['tipoHabitacion'])];
                $subtotalHabitacion = $precioBasePorNoche * $noches;
                $ivaHabitacion = $subtotalHabitacion * 0.19;
                $totalHabitacion = $subtotalHabitacion + $ivaHabitacion;

                // 2. Precio servicios seleccionados
                $precioServicios = 0;
                foreach ($servicios as $servicio) {
                    if ($servicio === "Desayuno Buffet") {
                        $precioServicios += 35000 * (int)$datos['huesped'];
                    } elseif ($servicio === "Parqueadero") {
                        $precioServicios += 20000 * $noches;
                    } elseif (isset($preciosServicios[$servicio])) {
                        $precioServicios += $preciosServicios[$servicio];
                    }
                }
                $ivaServicios = $precioServicios * 0.19;
                $totalServicios = $precioServicios + $ivaServicios;

                // 3. Total general
                $precioTotal = $totalHabitacion + $totalServicios;



                $datosReserva = [
                    'id_usuario' => $id_usuario,
                    'id_habitacion' => $id_habitacion,
                    'fecha_entrada' => $datos['checkIn'],
                    'fecha_salida' => $datos['checkOut'],
                    'num_huespedes' => $datos['huesped'],
                    'servicios_adicionales' => json_encode($servicios),
                    'precio_total' => $precioTotal
                ];

                $idReserva = $this->model->crearReserva($datosReserva);

                if ($idReserva) {
                    echo json_encode(['success' => true, 'id_reserva' => $idReserva]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No se pudo guardar la reserva']);
                }

                break;

            case 'listar':
                $email = $_GET['email'] ?? '';
                if (!$email) {
                    echo json_encode([]);
                    return;
                }
                $reservas = $this->model->listarReservasPorEmail($email);
                file_put_contents(__DIR__ . '/../debug_listar.txt', print_r($reservas, true));
                echo json_encode($reservas);
                break;

            case 'eliminar':
                if (!isset($datos['id_reserva'])) {
                    echo json_encode(['success' => false, 'error' => 'Falta ID de la reserva']);
                    return;
                }
                $id = (int)$datos['id_reserva'];

                $success = $this->model->cancelarReserva($id);

                echo json_encode(['success' => $success]);
                return;

            case 'obtener':
                if (!isset($_GET['id'])) {
                    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
                    return;
                }

                $id = (int)$_GET['id'];
                $reserva = $this->model->obtenerReservaPorId($id);

                if (!$reserva) {
                    echo json_encode(['success' => false, 'error' => 'Reserva no encontrada']);
                    return;
                }

                echo json_encode(['success' => true, 'reserva' => $reserva]);
                return;

            case 'editar':
                $idReserva = $_POST['id_reserva'] ?? null;
                $idHabitacion = $_POST['id_habitacion'] ?? null;
                $fechaEntrada = $_POST['checkIn'] ?? null;
                $fechaSalida = $_POST['checkOut'] ?? null;

                if (!$idReserva || !$idHabitacion || !$fechaEntrada || !$fechaSalida) {
                    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                    exit;
                }

                $result = $reservaModel->editarReservaFechas($idReserva, $idHabitacion, $fechaEntrada, $fechaSalida);
                echo json_encode($result);
                break;


            
            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    }
}

// Ejecutar el controlador si se accede directamente
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    require_once __DIR__ . '/../config/conexionbd.php';
    $controller = new ReservaController($pdo);
    $controller->manejarSolicitud();
}


?>
