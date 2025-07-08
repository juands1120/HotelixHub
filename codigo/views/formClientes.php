<?php
// ==============================================
// SECCIÓN DE SEGURIDAD Y CONTROL DE ACCESO
// ==============================================
require_once __DIR__ . '/../services/sessionManager.php';

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- METADATOS BÁSICOS -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HotelixHub - Clientes</title>
    
    <!-- HOJA DE ESTILOS -->
    <link rel="stylesheet" href="../assets/css/FormClientes.css">
</head>
<body>
    <!-- ==============================================
         BARRA LATERAL (MENÚ DE NAVEGACIÓN)
         ============================================== -->
    <div class="barra-lateral">
        <!-- LOGO DEL HOTEL -->
        <div class="logo">
            <a href="Home.php">
                <img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo">
            </a>
        </div>
        <br><br>
        
        <!-- ELEMENTOS DEL MENÚ -->
        <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
        <a href="habitacion.html"><div class="menu-item">Habitaciones</div></a>
        
        <!-- SUBMENÚ DE USUARIOS -->
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

    <!-- ==============================================
         CONTENIDO PRINCIPAL
         ============================================== -->
    <main class="main">
        <!-- CABECERA CON PERFIL DE USUARIO -->
        <header class="header">
            <div class="profile" id="profile">
                <span class="profile-name">
                    <?php echo htmlspecialchars($_SESSION['usuario']['nombre']. ' ' . $_SESSION['usuario']['apellido']); ?>
                </span>
                <div class="profile-img">👤</div>
            </div>
        </header>

        <!-- TÍTULO DE LA PÁGINA -->
        <h1 class="page-title">Clientes</h1>

        <!-- FILTRO POR ESTADO -->

    <!-- Filtro por estado -->
            <section id="filtro-estado">
                <form id="formEstadoFiltro">
                    <label for="estadoFiltro">Filtrar por estado:</label>
                    <select name="estadoFiltro" id="estadoFiltro">
                        <option value="">Todos</option>
                        <option value="Confirmado">Confirmado</option>
                        <option value="Cancelado">Cancelado</option>
                        <option value="Sin reserva">Sin reserva</option>
                        <option value="Pendiente">Pendiente</option>
                    </select>
                </form>
            </section>

        <!-- ==============================================
             SECCIÓN DE DETALLES DEL CLIENTE
             ============================================== -->
        <section class="client-details">
            <div class="client-detail-header">
                <div>Info. Cliente</div>
                <div>Info. Habitación</div>
                <div>Info. Reserva</div>
                <div>Valor</div>
            </div>

            <div class="client-detail-content">
                <!-- INFORMACIÓN DEL CLIENTE -->
                <div class="client-info">
                    <div class="client-avatar">👤</div>
                    <div class="client-data">
                        <div class="name" id="detalle-nombre">-</div>
                        <div class="details" id="detalle-documento">-</div>
                        <div class="details" id="detalle-nacionalidad">-</div>
                        <div class="details" id="detalle-telefono">-</div>
                    </div>
                </div>

                <!-- DETALLES DE LA HABITACIÓN -->
                <div class="room-details">
                    <div class="room-number" id="detalle-numero">Indicativo: -</div>
                    <div class="room-type" id="detalle-tipo">Tipo: -</div>
                    <div class="room-extra" id="detalle-servicio">Adicional: -</div>
                </div>

                <!-- DETALLES DE LA RESERVA -->
                <div class="reservation-details">
                    <div class="reservation-dates">
                        <div id="detalle-checkin">Check-In: -</div>
                        <div id="detalle-checkout">Check-Out: -</div>
                    </div>
                    <div class="reservation-status" id="detalle-estado">Estado: -</div>
                </div>

                <!-- DETALLES DE VALORES -->
                <div class="value-details">
                    <div class="value-item value-total" id="detalle-mensaje" style="display: none;">
                        <span>No se encontró una reserva activa para este cliente.</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==============================================
             TABLA DE CLIENTES
             ============================================== -->
        <section class="clients-table">
            <div class="clients-table-header">
                <div>Habitacion</div>
                <div>Nombre</div>
                <div>Check-In</div>
                <div>Check-Out</div>
                <div>Estado</div>
                <div></div>
            </div>

            <!-- CONTENEDOR PARA CLIENTES DINÁMICOS -->
            <div id="clientes-contenedor"></div>
        </section>

        <!-- BOTÓN PARA GENERAR PDF -->
        <section class="reporte-clientes">
            <button id="btnGenerarPDF" class="btn-reporte">Generar PDF</button>
        </section>


<!-- SCRIPTS -->

<script>
document.addEventListener('DOMContentLoaded', () => {
  let clientesGlobal = [];

  const contenedor = document.getElementById('clientes-contenedor');
  const selectEstado = document.getElementById('estadoFiltro');
  const panelDetalle = document.querySelector('.client-details');

  // ✅ Ocultar el detalle por defecto al cargar
  panelDetalle.classList.remove('active');

  // Función para renderizar según filtro
  function renderClientes(filtrados) {
    contenedor.innerHTML = '';

    if (filtrados.length === 0) {
      contenedor.innerHTML = '<div style="padding: 10px; color: #555;">No se encontraron clientes.</div>';
      // Oculta el panel también
      panelDetalle.classList.remove('active');
      return;
    }

    filtrados.forEach(cliente => {
      const fila = document.createElement('div');
      fila.className = 'clients-table-row';

      fila.innerHTML = `
        <div class="room-cell">
          <div class="room-number">${cliente.nombre_habitacion || '-'}</div>
          <div class="room-type">${cliente.tipoHabitacion || '-'}</div>
        </div>
        <div class="client-cell">
          <div class="client-icon">👤</div>
          <div class="client-name">${cliente.nombre} ${cliente.apellido}</div>
        </div>
        <div class="date-cell">${cliente.fecha_entrada || '-'}</div>
        <div class="date-cell">${cliente.fecha_salida || '-'}</div>
        <div class="status-cell">
          <div class="status-indicator status-pending"></div>
          <span>${cliente.estado || 'Sin reserva'}</span>
        </div>
        <div class="action-cell">
          <button class="action-button">🔍</button>
        </div>
      `;

      // 🔍 Evento click para mostrar los detalles
      fila.querySelector('.action-button').addEventListener('click', () => {
        panelDetalle.classList.add('active');

        // Mostrar datos del cliente
        document.getElementById('detalle-nombre').textContent = `${cliente.nombre} ${cliente.apellido}`;
        document.getElementById('detalle-documento').textContent = `${cliente.tipoDocumento || '-'} ${cliente.numeroDocumento || '-'}`;
        document.getElementById('detalle-nacionalidad').textContent = `Nacionalidad: ${cliente.paisProcedencia || '-'}`;
        document.getElementById('detalle-telefono').textContent = `Cel: ${cliente.numeroTelefono || '-'}`;

        if (cliente.fecha_entrada && cliente.fecha_salida && cliente.nombre_habitacion) {
          document.getElementById('detalle-numero').textContent = `Indicativo: ${cliente.nombre_habitacion}`;
          document.getElementById('detalle-tipo').textContent = `Tipo: ${cliente.tipoHabitacion}`;
          document.getElementById('detalle-servicio').textContent = `Adicional: ${cliente.servicioIncluido || '-'}`;
          document.getElementById('detalle-checkin').textContent = `Check-In: ${cliente.fecha_entrada}`;
          document.getElementById('detalle-checkout').textContent = `Check-Out: ${cliente.fecha_salida}`;
          document.getElementById('detalle-estado').textContent = `Estado: ${cliente.estado}`;
          document.getElementById('detalle-mensaje').style.display = 'none';
        } else {
          document.getElementById('detalle-numero').textContent = `Indicativo: -`;
          document.getElementById('detalle-tipo').textContent = `Tipo: -`;
          document.getElementById('detalle-servicio').textContent = `Adicional: -`;
          document.getElementById('detalle-checkin').textContent = `Check-In: -`;
          document.getElementById('detalle-checkout').textContent = `Check-Out: -`;
          document.getElementById('detalle-estado').textContent = `Estado: -`;
          document.getElementById('detalle-mensaje').style.display = 'block';
        }
      });

      contenedor.appendChild(fila);
    });
  }

  // Filtro de estado
  selectEstado.addEventListener('change', () => {
    const valorFiltro = selectEstado.value;

    let filtrados = clientesGlobal;

    if (valorFiltro) {
      filtrados = clientesGlobal.filter(cliente => {
        const estado = cliente.estado || 'Sin reserva';
        return estado.toLowerCase() === valorFiltro.toLowerCase();
      });
    }

    // Oculta el panel de detalle al cambiar el filtro
    panelDetalle.classList.remove('active');
    renderClientes(filtrados);
  });

  // Obtener datos
  fetch('../controller/clienteController.php')
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        clientesGlobal = data.data;
        renderClientes(clientesGlobal);
      } else {
        console.error('Error al traer clientes:', data.message);
      }
    })
    .catch(error => {
      console.error('Error en la solicitud:', error);
    });
});

document.getElementById('btnGenerarPDF').addEventListener('click', function () {
  const estadoSeleccionado = document.getElementById('estadoFiltro').value;

  // Crear un formulario dinámico
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '../pdf/generarReportesClientes.php'; // Ruta al script que genera el PDF
  form.target = '_blank'; // Para abrir el PDF en una nueva pestaña

  // Crear input hidden con el estado seleccionado
  const inputEstado = document.createElement('input');
  inputEstado.type = 'hidden';
  inputEstado.name = 'estadoFiltro';
  inputEstado.value = estadoSeleccionado;

  // Agregar el input al formulario
  form.appendChild(inputEstado);

  // Agregar el formulario al body y enviarlo
  document.body.appendChild(form);
  form.submit();

  // Eliminar el formulario del DOM
  document.body.removeChild(form);
});

</script>
</body>
</html>