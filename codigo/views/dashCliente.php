<?php
require_once __DIR__ . '/../services/sessionManager.php';


if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Verificar que el rol sea cliente
if ($_SESSION['usuario']['usu_idrol'] != 2) {
    header('Location: ../login.php'); // O a una página de error
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="../assets/css/dashCliente.css">

</head>
<body>

  <!-- BARRA LATERAL -->
  <aside class="barra-lateral">
    <div class="logo">
      <img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub"  class="logo">
    </div>
    <nav>
      <a href="dashCliente.php"><i class="fa fa-home"></i>Inicio</a>
      <a href="reservas.html"><i class="fa fa-bed"></i>Reservas</a>
      <a href="#"><i class="fa fa-box"></i>Productos</a>
      <a href=""><i class="fa fa-cog"></i>Ajustes</a>
      <a href="../controller/logout.php"><i class="sesion"></i>Cerrar Sesion</a>
    </nav>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main">
    <div class="main-header">
      <h1>Perfil</h1>
      <div class="btn-group">
        <button id="editBtn" class="btn-edit">Editar perfil</button>
        <button id="saveBtn" class="btn-save" disabled>Guardar</button>
      </div>
    </div>

    <!-- DATOS PERSONALES -->
    <section class="card">
      <h2>Datos Personales</h2>
      <div class="group">
        <div class="col display">
          <p><strong>Nombre:</strong> <span id="dispNombre"></span></p>
          <p><strong>Tipo Doc.:</strong> <span id="dispTipo"></span></p>
          <p><strong>Número Doc.:</strong> <span id="dispNum"></span></p>
          <p><strong>País:</strong> <span id="dispPais"></span></p>
        </div>
        <div class="col edit">
          <label for="inpNombre">Nombre completo</label>
          <input id="inpNombre" type="text" value="" disabled/>
          <label for="inpTipo">Tipo de documento</label>
          <input id="inpTipo" type="text" value="" disabled/>
          <label for="inpNum">Número de documento</label>
          <input id="inpNum" type="text" value="" disabled/>
          <label for="inpPais">País de procedencia</label>
          <input id="inpPais" type="text" value="" disabled/>
        </div>
      </div>
    </section>

    <!-- DATOS DE CONTACTO -->
    <section class="card">
      <h2>Datos de Contacto</h2>
      <div class="group">
        <div class="col display">
          <p><strong>Email:</strong> <span id="dispEmail"></span></p>
          <p><strong>Teléfono:</strong> <span id="dispTel"></span></p>
        </div>
        <div class="col edit">
          <label for="inpEmail">Email</label>
          <input id="inpEmail" type="email" value="" disabled/>
          <label for="inpTel">Teléfono</label>
          <input id="inpTel" type="tel" value="" disabled/>
        </div>
      </div>
    </section>

    <!-- HISTORIAL -->
    <section class="history">
      <h2>Historial de Reservas</h2>
      <table>
        <thead>
          <tr>
            <th>Hotel</th>
            <th>Fecha de Reserva</th>
            <th>Fecha de Entrada</th>
            <th>Fecha de Salida</th>
            <th>Estado</th>
          </tr>
        </thead>
          <tbody id="tablaReservasBody">
            <!-- Se llenará dinámicamente -->
          </tbody>

      </table>
    </section>
  </main>

  <!-- MODAL -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle"></h3>
      <p id="modalMessage"></p>
      <button id="modalCloseBtn">Cerrar</button>
    </div>
  </div>

<script>
/* ========== 1. AL CARGAR LA PÁGINA: CARGAR DATOS DEL CLIENTE Y SUS RESERVAS ========== */
document.addEventListener('DOMContentLoaded', () => {
  fetch('../controller/clienteInfoController.php')
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        const historial = data.data;
        const tbody = document.getElementById('tablaReservasBody');
        tbody.innerHTML = '';

        // Si no hay reservas
        if (historial.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5">No hay reservas registradas.</td></tr>';
        } else {
          // Mostrar reservas en tabla
          historial.forEach(reserva => {
            const tr = document.createElement('tr');
            const fechaReserva = reserva.fecha_reserva ? reserva.fecha_reserva.substring(0, 10) : '';
            const fechaEntrada = reserva.fecha_entrada ? reserva.fecha_entrada.substring(0, 10) : '';
            const fechaSalida = reserva.fecha_salida ? reserva.fecha_salida.substring(0, 10) : '';

            tr.innerHTML = `
              <td>${reserva.nombre_hotel || 'Hotel El Campin'}</td>
              <td>${fechaReserva}</td>
              <td>${fechaEntrada}</td>
              <td>${fechaSalida}</td>
              <td>${reserva.estado || 'Desconocido'}</td>
            `;
            tbody.appendChild(tr);
          });
        }

        // Mostrar datos del cliente (de la primera reserva)
        if (historial.length > 0) {
          const cliente = historial[0];
          document.getElementById('dispNombre').textContent = cliente.nombre + " " + cliente.apellido;
          document.getElementById('dispTipo').textContent = cliente.tipoDocumento;
          document.getElementById('dispNum').textContent = cliente.numeroDocumento;
          document.getElementById('dispPais').textContent = cliente.paisProcedencia;
          document.getElementById('dispEmail').textContent = cliente.email;
          document.getElementById('dispTel').textContent = cliente.numeroTelefono;

          // Campos editables
          document.getElementById('inpNombre').value = cliente.nombre + " " + cliente.apellido;
          document.getElementById('inpTipo').value = cliente.tipoDocumento;
          document.getElementById('inpNum').value = cliente.numeroDocumento;
          document.getElementById('inpPais').value = cliente.paisProcedencia;
          document.getElementById('inpEmail').value = cliente.email;
          document.getElementById('inpTel').value = cliente.numeroTelefono;
        }

      } else {
        openModal("Error", "No se pudo obtener la información del cliente.");
      }
    })
    .catch(error => {
      console.error(error);
      openModal("Error", "Error al cargar datos.");
    });
});


/* ========== 2. FUNCIONALIDAD DEL MODAL DE MENSAJE ========== */
const modal = document.getElementById('modal');
const modalTitle = document.getElementById('modalTitle');
const modalMessage = document.getElementById('modalMessage');
const modalCloseBtn = document.getElementById('modalCloseBtn');

function openModal(title, message) {
  modalTitle.textContent = title;
  modalMessage.textContent = message;
  modal.classList.add('active');
}

modalCloseBtn.addEventListener('click', () => {
  modal.classList.remove('active');
});


/* ========== 3. FUNCIONES DE VALIDACIÓN ========== */
function validarEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

function validarTelefono(tel) {
  return /^[\d+\s]+$/.test(tel.trim());
}


/* ========== 4. BOTÓN EDITAR: SOLO HABILITA EMAIL Y TELÉFONO ========== */
editBtn.addEventListener('click', () => {
  document.getElementById('inpEmail').disabled = false;
  document.getElementById('inpTel').disabled = false;

  // Asegura que los demás sigan deshabilitados
  document.getElementById('inpNombre').disabled = true;
  document.getElementById('inpTipo').disabled = true;
  document.getElementById('inpNum').disabled = true;
  document.getElementById('inpPais').disabled = true;

  editBtn.disabled = true;
  saveBtn.disabled = false;
});


/* ========== 5. BOTÓN GUARDAR: VALIDAR Y ACTUALIZAR EN SERVIDOR ========== */
saveBtn.addEventListener('click', () => {
  const email = inpEmail.value.trim();
  const telefono = inpTel.value.trim();
  let errores = [];

  // Validaciones
  if (!validarEmail(email)) {
    errores.push("El email no tiene un formato válido.");
    inpEmail.classList.add('invalid');
  } else {
    inpEmail.classList.remove('invalid');
  }

  if (!validarTelefono(telefono)) {
    errores.push("El teléfono solo debe contener números, espacios o el símbolo '+'.");
    inpTel.classList.add('invalid');
  } else {
    inpTel.classList.remove('invalid');
  }

  if (errores.length > 0) {
    openModal("Errores de Validación", errores.join('\n'));
    return;
  }

  // Si pasa validación, actualizar interfaz
  dispEmail.textContent = email;
  dispTel.textContent = telefono;

  document.getElementById('inpEmail').disabled = true;
  document.getElementById('inpTel').disabled = true;
  editBtn.disabled = false;
  saveBtn.disabled = true;

  // Enviar al servidor
  const datosActualizados = { email, telefono };

  fetch('../controller/actualizarClienteController.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(datosActualizados)
  })
  .then(response => response.json())
  .then(result => {
    if (result.status === 'success') {
      openModal("Éxito", "Los datos han sido actualizados correctamente.");
    } else {
      openModal("Error", "No se pudieron actualizar los datos.");
    }
  })
  .catch(error => {
    console.error(error);
    openModal("Error", "Error en la comunicación con el servidor.");
  });
});
</script>

</body>
</html>
