<?php
/**
 * Dashboard de Administrador - HotelixHub
 * 
 * Este archivo muestra el panel de control para administradores con estadísticas
 * de habitaciones y gráficos de reservas.
 */

// 1. INCLUSIÓN DE ARCHIVOS NECESARIOS
require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/habitacionesdash.php';

// 2. VERIFICACIÓN DE SESIÓN Y ROLES
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Solo permitir acceso a administradores (rol 1)
if (!in_array($_SESSION['usuario']['usu_idrol'], [1])) {
    header('Location: ../views/login.php');
    exit();
}

// 3. OBTENER ESTADÍSTICAS DE HABITACIONES POR PISO
$piso2 = obtenerEstadisticasPiso(2, $pdo);
$piso3 = obtenerEstadisticasPiso(3, $pdo);
$piso4 = obtenerEstadisticasPiso(4, $pdo);
$piso5 = obtenerEstadisticasPiso(5, $pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HotelixHub Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashAdmin.css">
</head>
<body>
    <!-- ==================== BARRA LATERAL ==================== -->
    <div class="barra-lateral">
        <div class="logo">
            <a href="dashAdmin.php">
                <img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo">
            </a>
        </div>
        
        <br><br>
        
        <!-- Menú de navegación -->
        <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>

        
        
        <!-- Menú desplegable de Usuarios -->
        <a href="habitacion.php"><div class="menu-item">Habitaciones</div></a>
        <div class="usu">
            <button id="usuario">Usuarios</button>
            <div class="usu-contenido">
                <a href="formEmpleados.php">Empleados</a>
                <a href="formClientes.php">Clientes</a>
            </div>
        </div>
        
        <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>
        <a href="../controller/logout.php"><div class="logout">Cerrar Sesión</div></a>
    </div>

    <!-- ==================== CONTENIDO PRINCIPAL ==================== -->
    <div class="main-content">
        <!-- Perfil del usuario -->
        <div class="profile" id="profile">
            <span class="profile-name">
                <?php echo htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']); ?>
            </span>
            <div class="profile-img">👤</div>
        </div>

        <!-- Mensaje de bienvenida -->
        <div class="welcome">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']); ?></h2>
        </div>

        <div class="dashboard-content">
            <div class="dashboard-left">
                <!-- ==================== ESTADÍSTICAS DE HABITACIONES ==================== -->
                <div class="room-stats">
                    <!-- Piso 2 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 2</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number"><?= $piso2['total'] ?></div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso2['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso2['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso2['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                        </div>
                    </div>

                    <!-- Piso 3 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 3</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number"><?= $piso3['total'] ?></div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso3['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso3['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso3['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                        </div>
                    </div>

                    <!-- Piso 4 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 4</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number"><?= $piso4['total'] ?></div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso4['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso4['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso4['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                        </div>
                    </div>

                    <!-- Piso 5 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 5</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number"><?= $piso5['total'] ?></div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso5['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso5['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number"><?= $piso5['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                        </div>
                    </div>
                </div>

                <!-- ==================== GRÁFICO DE RESERVAS ==================== -->
                <div class="reservas">
                    <div class="reservas-header">
                        <h3>Reservas Completadas</h3>
                    </div>

                    <div class="chart-container">
                        <canvas id="reservasChart"></canvas>
                    </div>

                    <!-- Filtro de fechas -->
                    <div class="date-filter">
                        <input type="date" id="fechaInicio">
                        <input type="date" id="fechaFin">
                        <button id="filtrarBtn">Filtrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    /**
     * Script para manejar el gráfico de reservas
     */
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById("reservasChart").getContext("2d");
        let reservasChart;

        // Función para cargar el gráfico con datos
        function cargarGrafico(fechaInicio, fechaFin) {
            fetch(`../controller/getReservas.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error del servidor:", data.error);
                        return;
                    }

                    const fechas = data.map(r => r.fecha);
                    const totales = data.map(r => r.total);

                    // Destruir gráfico existente si hay uno
                    if (reservasChart) {
                        reservasChart.destroy();
                    }

                    // Crear nuevo gráfico
                    reservasChart = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: fechas,
                            datasets: [{
                                label: "Reservas completadas",
                                data: totales,
                                backgroundColor: "#6c63ff",
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(err => console.error("Error de red:", err));
        }

        // Evento del botón de filtrar
        document.getElementById("filtrarBtn").addEventListener("click", function () {
            const inicio = document.getElementById("fechaInicio").value;
            const fin = document.getElementById("fechaFin").value;

            if (!inicio || !fin) {
                alert("Por favor selecciona ambas fechas.");
                return;
            }

            cargarGrafico(inicio, fin);
        });

        // Cargar datos por defecto (últimos 7 días)
        const hoy = new Date();
        const hace7dias = new Date(hoy);
        hace7dias.setDate(hoy.getDate() - 7);

        const hoyStr = hoy.toISOString().split("T")[0];
        const hace7Str = hace7dias.toISOString().split("T")[0];

        document.getElementById("fechaInicio").value = hace7Str;
        document.getElementById("fechaFin").value = hoyStr;
        cargarGrafico(hace7Str, hoyStr);
    });
    </script>
</body>
</html>