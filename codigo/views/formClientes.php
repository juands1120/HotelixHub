<?php
require_once __DIR__ . '/../../controlador/sessionManager.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HotelixHub - Clientes</title>
  <link rel="stylesheet" href="../css/FormClientes.css">
</head>
<body>
  <div class="barra-lateral">

    <div class="logo">
      <a href="Home.php"><img src="/Codigo/vista/img/img_ProductosCliente/Logo Positivo (1).png" alt="HotelixHub"  class="logo">
    </div>
    <br><br>
            
    <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
    <a href="Habitacion.php"><div class="menu-item">Habitaciones</div></a>

    <div class="usu">
      <button id="usuario">Usuarios</button>
      <div class="usu-contenido">
        <a href="DashEmpleados.php">Empleados   </a>
        <a href="DashClientes.php">Clientes</a>
      </div>
    </div>
    <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>
  </div> 

  <main class="main">
    <header class="header">
      <div class="search-bar">
        <input type="text" placeholder="Buscar" />
        <button>🔍</button>
        <button>🔽</button>
      </div>
      <div class="profile">
        <span class="profile-name">Jose Cuervo</span>
        <div class="profile-img">👤</div>
      </div>
    </header>

    <h1 class="page-title">Clientes</h1>
    <h2 class="page-subtitle">Detalles</h2>

    <section class="client-details">
      <div class="client-detail-header">
        <div>Info. Cliente</div>
        <div>Info. Habitación</div>
        <div>Info. Reserva</div>
        <div>Valor</div>
      </div>
      <div class="client-detail-content">
        <div class="client-info">
          <div class="client-avatar">👤</div>
          <div class="client-data">
            <div class="name">Juan Mauricio Perez Florez</div>
            <div class="details">C.C. 32546223</div>
            <div class="details">Nacionalidad: Colombia</div>
            <div class="details">Cel: 3163968546</div>
          </div>
        </div>
        <div class="room-details">
          <div class="room-number">Indicativo: 301</div>
          <div class="room-type">Tipo: Doble</div>
          <div class="room-extra">Adicional: Romantica</div>
        </div>
        <div class="reservation-details">
          <div class="reservation-dates">
            <div>Check-In: 15/02/2024</div>
            <div>Check-Out: 16/02/2024</div>
          </div>
          <div class="reservation-status">Estado: Confirmado</div>
        </div>
        <div class="value-details">
          <div class="value-item"><span>Habitación:</span> <span>250k</span></div>
          <div class="value-item"><span>Adicional:</span> <span>50k</span></div>
          <div class="value-item"><span>Comida:</span> <span>80k</span></div>
          <div class="value-item"><span>Abono:</span> <span>300k</span></div>
          <div class="value-item value-total"><span>Saldo:</span> <span>80k</span></div>
          <div class="action-buttons">
            <button class="action-button">✏</button>
          </div>
        </div>
      </div>
    </section>

    <section class="clients-table">
      <div class="clients-table-header">
        <div>Habitacion</div>
        <div>Nombre</div>
        <div>Check-In</div>
        <div>Check-Out</div>
        <div>Estado</div>
        <div></div>
      </div>
      <div class="clients-table-row">
        <div class="room-cell">
          <div class="room-number">301</div>
          <div class="room-type">Doble</div>
        </div>
        <div class="client-cell">
          <div class="client-icon">👤</div>
          <div class="client-name">Juan Mauricio Perez Florez</div>
        </div>
        <div class="date-cell">15 - Febrero - 2024</div>
        <div class="date-cell">16 - Febrero - 2024</div>
        <div class="status-cell">
          <div class="status-indicator status-confirmed"></div>
          <span>Confirmado</span>
        </div>
        <div class="action-cell"></div>
      </div>
      <div class="clients-table-row">
        <div class="room-cell">
          <div class="room-number">205</div>
          <div class="room-type">Sencilla</div>
        </div>
        <div class="client-cell">
          <div class="client-icon">👤</div>
          <div class="client-name">Ingrid Lorena Acevedo</div>
        </div>
        <div class="date-cell">20 - Marzo - 2024</div>
        <div class="date-cell">21 - Marzo - 2024</div>
        <div class="status-cell">
          <div class="status-indicator status-canceled"></div>
          <span>Cancelado</span>
        </div>
        <div class="action-cell">
          <button class="action-button">✏</button>
          <button class="action-button">📄</button>
          <button class="action-button">🔍</button>
        </div>
      </div>
      <div class="clients-table-row">
        <div class="room-cell">
          <div class="room-number">506</div>
          <div class="room-type">Triple</div>
        </div>
        <div class="client-cell">
          <div class="client-icon">👤</div>
          <div class="client-name">Maria Camila Florez Ortiz</div>
        </div>
        <div class="date-cell">3 - Julio - 2024</div>
        <div class="date-cell">8 - Julio - 2024</div>
        <div class="status-cell">
          <div class="status-indicator status-pending"></div>
          <span>Por confirmar</span>
        </div>
        <div class="action-cell">
          <button class="action-button">✏</button>
          <button class="action-button">📄</button>
          <button class="action-button">🔍</button>
        </div>
      </div>
      <div class="clients-table-row">
        <div class="room-cell">
          <div class="room-number">403</div>
          <div class="room-type">Doble</div>
        </div>
        <div class="client-cell">
          <div class="client-icon">👤</div>
          <div class="client-name">Julian Santiago Montoya</div>
        </div>
        <div class="date-cell">2 - Septiembre - 2024</div>
        <div class="date-cell">20 - Septiembre - 2024</div>
        <div class="status-cell">
          <div class="status-indicator status-confirmed"></div>
          <span>Confirmado</span>
        </div>
        <div class="action-cell">
          <button class="action-button">✏</button>
          <button class="action-button">📄</button>
          <button class="action-button">🔍</button>
        </div>
      </div>
    </section>
  </main>

</body>
</html>