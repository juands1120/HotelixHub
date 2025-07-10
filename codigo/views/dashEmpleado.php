<?php
require_once __DIR__ . '/../services/sessionManager.php';


if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Verificar que el rol sea empleado
if (!in_array($_SESSION['usuario']['usu_idrol'], [3, 4, 5])) {
    header('Location: ../views/login.php'); // O página de acceso denegado
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoteluxHub Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashEmpleado.css">
</head>

<body>
<body>
    <div class="barra-lateral">
        <div class="logo">
            <a href="Home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo"></a>
        </div>
        <br><br>

        <a href="dashEmpleado.html"><div class="menu-item">Inicio</div></a>


        <div class="usu">
            <a href="formClientes.php"><button id="usuario">Clientes</button></a>
            
        </div>

        <a href="perfilEmpleado.php"><div class="menu-item">Perfil</div></a>
        <a href="../controller/logout.php"><div class="logout">Cerrar Sesión</div></a>
    </div>    

    <div class="main-content">
        <div class="profile" id="profile">
            <span class="profile-name">
                <?php echo htmlspecialchars($_SESSION['usuario']['nombre']. ' ' . $_SESSION['usuario']['apellido']); ?>
            </span>
            <div class="profile-img">👤</div>
        </div>

        <div class="welcome">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']. ' ' . $_SESSION['usuario']['apellido']); ?></h2>
        </div>

        <div class="dashboard-content">
            <div class="dashboard-left">
                <div class="room-stats">
                    <!-- Habitaciones Piso 2 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 2</h3>
                        <div class="stats-grid">
                            <!-- Aquí están las estadísticas -->
                            <div class="stat-box"><div class="stat-number">30</div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Limpiando</div></div>
                            <div class="stat-box"><div class="stat-number">10</div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number">15</div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number">15</div><div class="stat-label">Check-Out</div></div>
                            <div class="stat-box"><div class="stat-number">15</div><div class="stat-label">Check-Out</div></div>
                        </div>
                    </div>

                    <!-- Habitaciones Piso 3 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 3</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number">20</div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Limpiando</div></div>
                            <div class="stat-box"><div class="stat-number">10</div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Check-In</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Check-Out</div></div>
                        </div>
                    </div>

                    <!-- Habitaciones Piso 4 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 4</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number">20</div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Limpiando</div></div>
                            <div class="stat-box"><div class="stat-number">10</div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Ocupadas</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Check-In</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Check-Out</div></div>
                        </div>
                    </div>

                    <!-- Habitaciones Piso 5 -->
                    <div class="room-category">
                        <h3>Habitaciones Piso 5</h3>
                        <div class="stats-grid">
                            <div class="stat-box"><div class="stat-number">20</div><div class="stat-label">Total</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Limpiando</div></div>
                            <div class="stat-box"><div class="stat-number">10</div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Disponibles</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Check-In</div></div>
                            <div class="stat-box"><div class="stat-number">5</div><div class="stat-label">Check-Out</div></div>
                        </div>
                    </div>
                </div>

                <!-- 🔔 NOTIFICACIONES AQUÍ -->
                <div class="notificaciones-box">
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

        <!-- Nueva Categoría -->
        <div class="notification-category">Categoría: Cocina</div>

        <!-- Botón marcar como leído -->
        <button class="mark-read-btn">Marcar como leído</button>
    </div>

    <div class="notification-list">
        <div class="notification-item">
            <div class="notification-item-avatar">J</div>
            <div class="notification-item-content">
                <div class="notification-item-name">Juan Mauricio Perez Florez</div>
                <div class="notification-item-room">301</div>
                <div class="notification-item-category">Categoría: Aseo</div>
            </div>
            <div class="notification-item-actions">
                <button class="mark-read-btn small">✔</button>
            </div>
        </div>
    </div>
</div>

                <!-- 🔔 FIN NOTIFICACIONES -->
            </div>
        </div>
    </div>
</body>

</body> 
</html>


