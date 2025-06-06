<?php
require_once __DIR__ . '/../../controlador/sessionManager.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HotelixHub - Empleados</title>
  <link rel="stylesheet" href="../css/DashEmpleados.css">
</head>
<body>
          <div class="barra-lateral">

            <div class="logo">
              <a href="Home.html"><img src="/Codigo/vista/img/img_ProductosCliente/Logo Positivo (1).png" alt="HotelixHub" class="logo">
            </div>
            <br><br>
            
            <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
            <a href="Habitacion.html"><div class="menu-item">Habitaciones</div></a>

            <div class="usu">
                <button id="usuario">Usuarios</button>
                <div class="usu-contenido">
                    <a href="DashEmpleados.html">Empleados   </a>
                    <a href="DashClientes.html">Clientes</a>
                </div>
            </div>
            <a href="ProductosAdmin.html"><div class="menu-item">Productos</div></a>
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

    <h1 class="page-title">Empleados</h1>
    <h2 class="page-subtitle">Detalles</h2>

    <section class="employee-details">
      <div class="employee-detail-header">
        <div>Información del Empleado</div>
      </div>
      <div class="employee-detail-content">
        <div class="employee-info">
          <div class="employee-avatar">👤</div>
          <div class="employee-data">
            <div class="name">Juan Mauricio Perez Florez</div>
            <div class="details">C.C. 32546223</div>
            <div class="details">Nacionalidad: Colombia</div>
          </div>
        </div>
        <div class="contact-details">
          <div>Correo: juanma@gmail.com</div>
          <div>Rol: Camarero (Pasarela)</div>
        </div>
        <div class="contact-details">
          <div>Cel: 3003265920</div>
          <div>Dirección: calle 56 #30-65</div>
          <div class="action-buttons">
            <button class="action-button">✏</button>
          </div>
        </div>
      </div>
    </section>

    <section class="employee-table">
      <div class="employee-table-header">
        <div>Rol</div>
        <div>Nombre</div>
        <div>Estado</div>
      </div>
      <div class="employee-table-row">
        <div class="role-cell">
          <div>Camarero</div>
          <div>Pasarela</div>
        </div>
        <div class="employee-cell">
          <div class="employee-icon">👤</div>
          <div class="employee-name">Juan Mauricio Perez Florez</div>
        </div>
        <div class="status-cell">
          <div>
            <div class="status-indicator status-active"></div>
            <span>Activo</span>
          </div>
          <div class="action-cell"></div>
        </div>
      </div>
      <div class="employee-table-row">
        <div class="role-cell">
          <div>Recepcionista</div>
        </div>
        <div class="employee-cell">
          <div class="employee-icon">👤</div>
          <div class="employee-name">Ingrid Lorena Acevedo</div>
        </div>
        <div class="status-cell">
          <div>
            <div class="status-indicator status-vacation"></div>
            <span>Vacaciones</span>
          </div>
          <div class="action-cell">
            <button class="action-button">✏</button>
            <button class="action-button">📄</button>
            <button class="action-button">🔍</button>
          </div>
        </div>
      </div>
      <div class="employee-table-row">
        <div class="role-cell">
          <div>Ayudante de</div>
          <div>Cocina</div>
        </div>
        <div class="employee-cell">
          <div class="employee-icon">👤</div>
          <div class="employee-name">Maria Camila Florez Ortiz</div>
        </div>
        <div class="status-cell">
          <div>
            <div class="status-indicator status-training"></div>
            <span>Capacitación</span>
          </div>
          <div class="action-cell">
            <button class="action-button">✏</button>
            <button class="action-button">📄</button>
            <button class="action-button">🔍</button>
          </div>
        </div>
      </div>
      <div class="employee-table-row">
        <div class="role-cell">
          <div>Aseador</div>
        </div>
        <div class="employee-cell">
          <div class="employee-icon">👤</div>
          <div class="employee-name">Julian Santiago Montoya</div>
        </div>
        <div class="status-cell">
          <div>
            <div class="status-indicator status-off"></div>
            <span>Fuera de Turno</span>
          </div>
          <div class="action-cell">
            <button class="action-button">✏</button>
            <button class="action-button">📄</button>
            <button class="action-button">🔍</button>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>