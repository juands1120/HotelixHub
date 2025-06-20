<?php
require_once __DIR__ . '/../services/sessionManager.php';


if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Verificar que el rol sea administrador 
if ($_SESSION['usuario']['usu_idrol'] != 1) {
    header('Location: ../views/login.php'); // O a una página de error
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoteluxHub Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashAdmin.css">
</head>

<body>
    <div class="barra-lateral">
        <div class="logo">
            <a href="Home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo">
        </div>
        <br><br>
            
        <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
        <a href="habitacion.html"><div class="menu-item">Habitaciones</div></a>

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

    </div>    

    <div class="main-content">
        <div class="header">
            <div class="profile">
                <span>jose cuervo</span>
                <div class="profile-img">JC</div>
            </div>
        </div>

        <div class="welcome">
            <h2>Bienvenido, jose cuervo</h2>
        </div>

        <div class="dashboard-content">
            <div class="dashboard-left">
                <div class="room-stats">
                    <div class="room-category">
                        <h3>Habitaciones Piso 2</h3>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-number">30</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Limpiando</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Disponibles</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">15</div>
                                <div class="stat-label">Ocupadas</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">15</div>
                                <div class="stat-label">Check-Out</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">15</div>
                                <div class="stat-label">Check-Out</div>
                            </div>
                        </div>
                    </div>

                    <div class="room-category">
                        <h3>Habitaciones Piso 3</h3>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-number">20</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Limpiando</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Disponibles</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Ocupadas</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Check-In</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Check-Out</div>
                            </div>
                        </div>
                    </div>

                        <div class="room-category">
                        <h3>Habitaciones Piso 4</h3>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-number">20</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Limpiando</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Disponibles</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Ocupadas</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Check-In</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Check-Out</div>
                            </div>
                        </div>
                    </div>

                    <div class="room-category">
                        <h3>Habitaciones Piso 5</h3>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-number">20</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Limpiando</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Disponibles</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Disponibles</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Check-In</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Check-Out</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reservas">
                    <div class="reservas-header">
                        <h3>Reservas cumplidas</h3>
                        <div class="date-filter">Fecha: Nov 15 - Nov 20</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="reservasChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="dashboard-right">
                <h3 class="notifications-title">Notificaciones</h3>
                <div class="notification-card active">
                    
                    <div class="notification-user">
                        <div class="notification-avatar">J</div>
                        <div class="notification-name">Juan Mauricio Perez Florez</div>
                    </div>
                    <div class="notification-message">
                        El huésped ordenó almuerzo del día a la habitación.
                    </div>
                    <div class="notification-room">Habitación: 301</div>
                </div>

                <div class="notification-list">
                    <div class="notification-item">
                        <div class="notification-item-avatar">J</div>
                        <div class="notification-item-content">
                            <div class="notification-item-name">Juan Mauricio Perez Florez</div>
                            <div class="notification-item-room">301</div>
                        </div>
                        <a id="notificacion" href="">
                            <div class="notification-item-actions">
                            <span class="action-icon">🟣</span>
                        </div>
                        </a>
                    </div>
                    <div class="notification-item">
                </div>
            </div>
        </div>
    </div>
</body> 
</html>


