<?php
// ==============================================
// SECCIÓN DE SEGURIDAD Y CONTROL DE ACCESO
// ==============================================

require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../config/conexionbd.php';

// Verificar sesión y roles
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}


// Definir el usu_idrol para usarlo en la vista
$usu_idrol = $_SESSION['usuario']['usu_idrol'];
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
              <a href="<?php echo $usu_idrol === 1 ? 'dashAdmin.php' : 'dashEmpleado.php'; ?>">
                  <img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo">
              </a>
          </div>
          <br><br>

          <!-- BOTÓN INICIO -->
          <?php if ($usu_idrol === 1): ?>
              <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
          <?php elseif (in_array($usu_idrol, [3, 4, 5])): ?>
              <a href="dashEmpleado.php"><div class="menu-item">Inicio</div></a>
          <?php endif; ?>

          <!-- HABITACIONES (solo admin) -->
          <?php if ($usu_idrol === 1): ?>
              <a href="habitacion.php"><div class="menu-item">Habitaciones</div></a>
          <?php endif; ?>

          <!-- SUBMENÚ USUARIOS -->
          <div class="usu">
              <button id="usuario">Usuarios</button>
              <div class="usu-contenido">
                  <?php if ($usu_idrol === 1): ?>
                      <a href="formEmpleados.php">Empleados</a>
                      <a href="formClientes.php">Clientes</a>
                  <?php elseif (in_array($usu_idrol, [3, 4, 5])): ?>
                      <a href="formClientes.php">Clientes</a>
                  <?php endif; ?>
              </div>
          </div>

          <!-- PRODUCTOS (solo admin) -->
          <?php if ($usu_idrol === 1): ?>
              <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>
          <?php endif; ?>

          <!-- PERFIL (solo empleado) -->
          <?php if (in_array($usu_idrol, [3, 4, 5])): ?>
              <a href="perfilEmpleado.php"><div class="menu-item">Perfil</div></a>
          <?php endif; ?>

          <!-- CERRAR SESIÓN -->
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
        <section id="filtro-estado">
            <form id="formEstadoFiltro">
                <label for="estadoFiltro">Filtrar por estado:</label>
                <select name="estadoFiltro" id="estadoFiltro">
                    <option value="">Todos</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Cancelada">Cancelada</option>
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
          const selectFiltroEstado = document.getElementById('estadoFiltro');
          const panelDetalle = document.querySelector('.client-details');

          // Ocultar detalle al cargar
          panelDetalle.classList.remove('active');

          // Aplicar estilos según estado
          function aplicarColorSelect(select, estado) {
            select.classList.remove('estado-pendiente', 'estado-confirmada', 'estado-cancelada', 'estado-sinreserva');
            
            if (!estado) return;
            
            const estadoClase = estado.toLowerCase().replace(' ', '');
            select.classList.add(`estado-${estadoClase}`);
          }

          // Renderizar lista de clientes
          function renderClientes(filtrados) {
            contenedor.innerHTML = '';

            if (filtrados.length === 0) {
              contenedor.innerHTML = '<div style="padding: 10px; color: #555;">No se encontraron clientes.</div>';
              panelDetalle.classList.remove('active');
              return;
            }

            filtrados.forEach(cliente => {
              const fila = document.createElement('div');
              fila.className = 'clients-table-row';

              // Determinar el estado actual o usar "Sin reserva" por defecto
              const estadoActual = cliente.estado || 'Sin reserva';
              
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
                  <select class="select-estado" data-id-reserva="${cliente.id_reserva || ''}">
                    <option value="Confirmada" ${estadoActual === 'Confirmada' ? 'selected' : ''}>Confirmada</option>
                    <option value="Cancelada" ${estadoActual === 'Cancelada' ? 'selected' : ''}>Cancelada</option>
                    <option value="Pendiente" ${estadoActual === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                    <option value="Sin reserva" ${estadoActual === 'Sin reserva' ? 'selected' : ''}>Sin reserva</option>
                  </select>
                </div>
                <div class="action-cell">
                  <button class="action-button">🔍</button>
                </div>
              `;

              // Mostrar detalles al hacer clic
              fila.querySelector('.action-button').addEventListener('click', () => {
                mostrarDetallesCliente(cliente);
              });

              // Manejar cambio de estado
              const selectEstado = fila.querySelector('.select-estado');
              aplicarColorSelect(selectEstado, estadoActual);
              
              selectEstado.addEventListener('change', manejarCambioEstado);
              
              contenedor.appendChild(fila);
            });
          }

          // Mostrar detalles del cliente
          function mostrarDetallesCliente(cliente) {
            panelDetalle.classList.add('active');

            document.getElementById('detalle-nombre').textContent = `${cliente.nombre} ${cliente.apellido}`;
            document.getElementById('detalle-documento').textContent = `${cliente.tipoDocumento || '-'} ${cliente.numeroDocumento || '-'}`;
            document.getElementById('detalle-nacionalidad').textContent = `Nacionalidad: ${cliente.paisProcedencia || '-'}`;
            document.getElementById('detalle-telefono').textContent = `Cel: ${cliente.numeroTelefono || '-'}`;

            const tieneReserva = cliente.fecha_entrada && cliente.fecha_salida && cliente.nombre_habitacion;
            
            if (tieneReserva) {
              document.getElementById('detalle-numero').textContent = `Indicativo: ${cliente.nombre_habitacion}`;
              document.getElementById('detalle-tipo').textContent = `Tipo: ${cliente.tipoHabitacion}`;
              document.getElementById('detalle-servicio').textContent = `Adicional: ${cliente.serviciosIncluidos || '-'}`;
              document.getElementById('detalle-checkin').textContent = `Check-In: ${cliente.fecha_entrada}`;
              document.getElementById('detalle-checkout').textContent = `Check-Out: ${cliente.fecha_salida}`;
              document.getElementById('detalle-estado').textContent = `Estado: ${cliente.estado || 'Sin reserva'}`;
              document.getElementById('detalle-mensaje').style.display = 'none';
            } else {
              document.getElementById('detalle-numero').textContent = 'Indicativo: -';
              document.getElementById('detalle-tipo').textContent = 'Tipo: -';
              document.getElementById('detalle-servicio').textContent = 'Adicional: -';
              document.getElementById('detalle-checkin').textContent = 'Check-In: -';
              document.getElementById('detalle-checkout').textContent = 'Check-Out: -';
              document.getElementById('detalle-estado').textContent = 'Estado: -';
              document.getElementById('detalle-mensaje').style.display = 'block';
            }
          }

          // Manejar cambio de estado
          function manejarCambioEstado(event) {
            const select = event.target;
            const nuevoEstado = select.value;
            const idReserva = select.getAttribute('data-id-reserva');
            
            if (!idReserva) {
              alert('No se puede actualizar: ID de reserva no válido');
              return;
            }

            // Mostrar feedback visual durante la carga
            const originalEstado = select.dataset.originalEstado;
            select.disabled = true;
            
            fetch('../controller/actualizarEstadoReserva.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `id_reserva=${encodeURIComponent(idReserva)}&estado=${encodeURIComponent(nuevoEstado)}`
            })
            .then(response => {
              if (!response.ok) throw new Error('Error en la respuesta del servidor');
              return response.json();
            })
            .then(data => {
              if (data.status === 'success') {
                aplicarColorSelect(select, nuevoEstado);
                
                // Actualizar el estado en el array global
                const cliente = clientesGlobal.find(c => c.id_reserva == idReserva);
                if (cliente) cliente.estado = nuevoEstado;
                
                // Actualizar panel de detalles si está visible
                const detalleEstado = document.getElementById('detalle-estado');
                if (detalleEstado.textContent.includes('Estado:')) {
                  detalleEstado.textContent = `Estado: ${nuevoEstado}`;
                }
              } else {
                throw new Error(data.message || 'Error al actualizar el estado');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert(error.message);
              select.value = originalEstado; // Revertir el cambio
            })
            .finally(() => {
              select.disabled = false;
            });
          }

          // Filtrado por estado
          selectFiltroEstado.addEventListener('change', () => {
            const valorFiltro = selectFiltroEstado.value;
            let filtrados = clientesGlobal;

            if (valorFiltro) {
              filtrados = clientesGlobal.filter(cliente => {
                const estado = cliente.estado || 'Sin reserva';
                return estado.toLowerCase() === valorFiltro.toLowerCase();
              });
            }

            panelDetalle.classList.remove('active');
            renderClientes(filtrados);
          });

          // Cargar datos iniciales
          function cargarClientes() {
            fetch('../controller/clienteController.php')
              .then(response => {
                if (!response.ok) throw new Error('Error al obtener clientes');
                return response.json();
              })
              .then(data => {
                if (data.status === 'success') {
                  clientesGlobal = data.data;
                  renderClientes(clientesGlobal);
                } else {
                  throw new Error(data.message || 'Error en los datos recibidos');
                }
              })
              .catch(error => {
                console.error('Error:', error);
                contenedor.innerHTML = `<div style="padding: 10px; color: #ff0000;">Error al cargar los datos: ${error.message}</div>`;
              });
          }

          // Iniciar
          cargarClientes();
        });

        // Generar PDF
        document.getElementById('btnGenerarPDF').addEventListener('click', function() {
          const estadoSeleccionado = document.getElementById('estadoFiltro').value;
          const form = document.createElement('form');
          
          form.method = 'POST';
          form.action = '../pdf/generarReportesClientes.php';
          form.target = '_blank';

          const inputEstado = document.createElement('input');
          inputEstado.type = 'hidden';
          inputEstado.name = 'estadoFiltro';
          inputEstado.value = estadoSeleccionado;

          form.appendChild(inputEstado);
          document.body.appendChild(form);
          form.submit();
          document.body.removeChild(form);
        });
        </script>
    </main>
</body>
</html>