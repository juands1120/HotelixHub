<?php
// controllers/HabitacionController.php

// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/HabitacionModel.php';

class HabitacionController {
    private $model;

    public function __construct($pdo) {
        $this->model = new HabitacionModel($pdo);
    }

    public function manejarSolicitud() {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        switch ($accion) {
            case 'crear':
                $datos = $this->recogerDatosFormulario();
                $exito = $this->model->crearHabitacion($datos);

                if ($exito) {
                    $habitacionCreada = $this->model->obtenerHabitacionPorNumero($datos['numero']);
                    echo json_encode(['exito' => true, 'datos' => $habitacionCreada]);
                } else {
                    echo json_encode(['exito' => false]);
                }
                break;


            case 'editar':
                $datos = $this->recogerDatosFormulario();
                $exito = $this->model->editarHabitacion($datos);

                if ($exito) {
                    $habitacionActualizada = $this->model->obtenerHabitacionPorNumero($datos['numero']);
                    echo json_encode([
                        'exito' => true,
                        'datos' => $habitacionActualizada
                    ]);
                } else {
                    echo json_encode(['exito' => false]);
                }
                break;

            case 'eliminar':
                $numero = $_POST['numero'] ?? $_GET['numero'] ?? null;
                $exito = $this->model->eliminarHabitacion($numero);
                echo json_encode(['exito' => $exito]);
                break;

            case 'listar':
                $habitaciones = $this->model->obtenerHabitaciones();
                echo json_encode($habitaciones);
                break;

            case 'listarDisponibles':
                try {
                    $habitaciones = $this->model->listarHabitacionesDisponibles();
                    echo json_encode($habitaciones);
                } catch (Exception $e) {
                    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
                }
                break;

            default:
                echo json_encode(['exito' => false, 'error' => 'Acción inválida']);
        }
    }

    private function recogerDatosFormulario() {
        return [
            'numero' => $_POST['numero'] ?? '',
            'tipo' => $_POST['tipo'] ?? '',
            'piso' => $_POST['piso'] ?? '',
            'precio' => $_POST['precio'] ?? '',
            'servicios' => $_POST['servicios'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'imagen' => $_FILES['imagen'] ?? null,
            'imagenRuta' => $_POST['imagenRuta'] ?? null
        ];
    }
}

// ========== EJECUTAR CONTROLADOR ==========
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/conexionbd.php';
    $controller = new HabitacionController($pdo);
    $controller->manejarSolicitud();
}
