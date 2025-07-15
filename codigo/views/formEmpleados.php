<?php
// ==============================================
// SECCIÓN PHP: CONFIGURACIONES INICIALES Y SEGURIDAD
// ==============================================

// Incluir archivos necesarios
require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../models/empleadoRegistro.php';
require_once __DIR__ . '/../config/conexionbd.php';

// Verificar si el usuario está logueado, si no, redirigir a login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Crear instancia del modelo de empleado y obtener datos según filtro
$empleado = new empleadoRegistro($pdo);
$rol = $_GET['rolFiltro'] ?? '';
$empleados = !empty($rol)
    ? $empleado->obtenerEmpleadosPorRol($rol)
    : $empleado->obtenerEmpleados();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <!-- METADATOS Y ENLACES -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HotelixHub - Empleados</title>
  <link rel="stylesheet" href="../assets/css/formEmpleados.css">
</head>
<body>

<!-- ==============================================
     BARRA LATERAL - MENÚ DE NAVEGACIÓN
     ============================================== -->
<div class="barra-lateral">
    <div class="logo">
      <a href="dashEmpleado.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo"></a>
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

    <h1 class="page-title">Empleados</h1>

    <!-- SECCIÓN DE FILTROS Y BOTÓN AGREGAR -->
    <div class="container-filtroBoton">
        <!-- Filtro por rol -->
        <section id="filtro-reporte">
            <form id="formFiltros" method="get" action="formEmpleados.php">
              <label for="rolFiltro">Filtrar por rol:</label>
              <select name="rolFiltro" id="rolFiltro">
                <option value="">Todos</option>
                <option value="Recepcionista" <?= isset($_GET['rolFiltro']) && $_GET['rolFiltro'] == 'Recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
                <option value="Cocinero" <?= isset($_GET['rolFiltro']) && $_GET['rolFiltro'] == 'Cocinero' ? 'selected' : '' ?>>Cocinero</option>
                <option value="Camarero" <?= isset($_GET['rolFiltro']) && $_GET['rolFiltro'] == 'Camarero' ? 'selected' : '' ?>>Camarero</option>
              </select>
              <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </section>

        <!-- Botón para agregar nuevo empleado -->
        <section id="agregar-empleado">
            <button type="button" onclick="abrirModal()" class="btn btn-primary">
              Agregar Empleado
            </button>
        </section>
    </div>

    <!-- ==============================================
         MODAL PARA AGREGAR NUEVO EMPLEADO
         ============================================== -->
<div id="modalEmpleado" class="modal" style="display: none;">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <h2>Registrar nuevo empleado</h2>
        <form id="formEmpleado" method="POST" action="../controller/guardarEmpleado.php">
            <div class="form-columnas">
                <div class="columna">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>

                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>

                    <label for="tipoDocumento">Tipo de Documento:</label>
                    <select id="tipoDocumento" name="tipoDocumento" required>
                        <option value="">Seleccione un tipo de documento</option>
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="PPP">Pasaporte</option>
                    </select>

                    <label for="numeroDocumento">Número de Documento:</label>
                    <input type="text" id="numeroDocumento" name="numeroDocumento"
                           pattern="^\d{6,12}$" maxlength="12" title="Ingrese entre 6 y 12 números" required>

                    <label for="numeroTelefono">Teléfono:</label>
                    <input type="tel" id="numeroTelefono" name="numeroTelefono"
                           pattern="^\d{7,10}$" maxlength="10" title="Ingrese un número de teléfono válido de 7 a 10 dígitos" required>
                </div>

                <div class="columna">
                    <label for="email">Correo:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="rol">Rol:</label>
                    <select id="rol" name="usu_idrol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="3">Recepcionista</option>
                        <option value="4">Cocinero</option>
                        <option value="5">Camarero</option>
                    </select>

                    <label for="estado">Estado:</label>
    <select id="estado" name="estado" required>
    <option value="">Seleccione un estado</option>
    <option value="en turno">En turno</option>
    <option value="fuera de turno">Fuera de turno</option>
    <option value="vacaciones">Vacaciones</option>
    </select>


                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password"
                           pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                           title="La contraseña debe tener al menos 8 caracteres, incluyendo letras y números" required>

                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success" name="guardarEmpleado">Guardar</button>
        </form>
    </div>
</div>

        <!-- ==============================================
         MODAL PARA EDITAR EMPLEADO (CUADRO GRANDE)
         ============================================== -->


    <div id="modalEditarEmpleado" class="modal" style="display: none;">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Empleado</h2>
            <form id="formEditarEmpleado" method="POST" action="../controller/actualizarEmpleado.php">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-columnas">
                    <div class="columna">
                        <label for="edit_nombre">Nombre:</label>
                        <input type="text" id="edit_nombre" name="nombre" required>

                        <label for="edit_apellido">Apellido:</label>
                        <input type="text" id="edit_apellido" name="apellido" required>

                        <label for="edit_tipoDocumento">Tipo de Documento:</label>
                        <select id="edit_tipoDocumento" name="tipoDocumento" required>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="PPP">Pasaporte</option>
                        </select>

                        <label for="edit_numeroDocumento">Número de Documento:</label>
                        <input type="text" id="edit_numeroDocumento" name="numeroDocumento" required readonly>

                        <label for="edit_numeroTelefono">Teléfono:</label>
                        <input type="tel" id="edit_numeroTelefono" name="numeroTelefono" required>
                    </div>

                    <div class="columna">
                        <label for="edit_email">Correo:</label>
                        <input type="email" id="edit_email" name="email" required>

                        <label for="edit_rol">Rol:</label>
                        <select id="edit_rol" name="usu_idrol" required>
                            <option value="3">Recepcionista</option>
                            <option value="4">Cocinero</option>
                            <option value="5">Camarero</option>
                        </select>

                        <label for="edit_estado">Estado:</label>
        <select id="edit_estado" name="estado" required>
    <option value="en turno">En turno</option>
    <option value="fuera de turno">Fuera de turno</option>
    <option value="vacaciones">Vacaciones</option>
</select>


                        <label for="edit_direccion">Dirección:</label>
                        <input type="text" id="edit_direccion" name="direccion" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="eliminarEmpleado()">Eliminar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
         SECCIÓN DE DETALLES DEL EMPLEADO (CUADRO GRANDE)
         ============================================== -->
    <section class="employee-details" id="employeeDetails" style="display: none;">
        <div class="employee-detail-header">
            <div>Información del Empleado</div>
        </div>
        <div class="employee-detail-content"></div>
    </section>

    <!-- ==============================================
         TABLA DE EMPLEADOS (CUADRO PEQUEÑO)
         ============================================== -->
    <section class="employee-table">
        <div class="employee-table-header">
            <div>Rol</div>
            <div>Nombre</div>
            <div>Estado</div>
        </div>

        <?php foreach ($empleados as $emp): ?>
            <div class="employee-table-row">
                <div class="role-cell">
                    <div><?= htmlspecialchars($emp['rol_nombre']) ?></div>
                </div>
                <div class="employee-cell">
                    <div class="employee-icon">👤</div>
                    <div class="employee-name"><?= htmlspecialchars($emp['nombre']) . ' ' . htmlspecialchars($emp['apellido']) ?></div>
                </div>
                <div class="status-cell">
                    <div>
                        <div class="status-indicator status-<?= strtolower(str_replace(' ', '-', $emp['estado'])) ?>"></div>
                        <span><?= htmlspecialchars($emp['estado']) ?></span>
                    </div>
                    <div class="action-cell">
                        <!-- Botón de lupa con todos los datos del empleado como atributos data -->
                        <button class="ver-detalle"
                            data-id="<?= htmlspecialchars($emp['id_usuario']) ?>"
                            data-nombre="<?= htmlspecialchars($emp['nombre']) ?>"
                            data-apellido="<?= htmlspecialchars($emp['apellido']) ?>"
                            data-tipo-documento="<?= htmlspecialchars($emp['tipoDocumento']) ?>"
                            data-numero-documento="<?= htmlspecialchars($emp['numeroDocumento']) ?>"
                            data-telefono="<?= htmlspecialchars($emp['numeroTelefono']) ?>"
                            data-email="<?= htmlspecialchars($emp['email']) ?>"
                            data-direccion="<?= htmlspecialchars($emp['direccion']) ?>"
                            data-rol="<?= htmlspecialchars($emp['usu_idrol']) ?>"
                            data-rolcodigo="<?= htmlspecialchars($emp['usu_idrol']) ?>"
                            data-estado="<?= htmlspecialchars($emp['estado']) ?>"
                        >
                            🔍
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- BOTÓN PARA GENERAR PDF -->
    <form method="POST" action="../pdf/generarReporteEmpleados.php" target="_blank" style="width: 100%; margin-top: 15px;">
        <input type="hidden" name="rolFiltro" value="<?= htmlspecialchars($rol) ?>">
        <button type="submit" class="btn btn-secondary">Generar PDF</button>
    </form>

    <!-- MODAL PARA MOSTRAR ERRORES -->
    <div id="modalErrores" class="modal" style="display: none;">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarModalErrores()">&times;</span>
            <h3>Error de registro</h3>
            <ul id="listaErrores"></ul>
        </div>
    </div>

</main>

<!-- ==============================================
     SECCIÓN JAVASCRIPT
     ============================================== -->
<script>
// ==============================================
// FUNCIONES PARA MANEJO DE MODALES
// ==============================================

function abrirModal() {
    document.getElementById('modalEmpleado').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalEmpleado').style.display = 'none';
}

function cerrarModalErrores() {
    document.getElementById("modalErrores").style.display = "none";
}

function abrirModalEdicion(empleado) {
    // Llenar el formulario con los datos del empleado
    const form = document.getElementById('formEditarEmpleado');
    form.elements['id'].value = empleado.id;
    form.elements['nombre'].value = empleado.nombre;
    form.elements['apellido'].value = empleado.apellido;
    form.elements['tipoDocumento'].value = empleado.tipoDocumento;
    form.elements['numeroDocumento'].value = empleado.numeroDocumento;
    form.elements['numeroTelefono'].value = empleado.telefono;
    form.elements['email'].value = empleado.email;
    form.elements['usu_idrol'].value = empleado.rol;
    form.elements['estado'].value = empleado.estado;
    form.elements['direccion'].value = empleado.direccion;
    
    document.getElementById('modalEditarEmpleado').style.display = 'flex';
}

function cerrarModalEditar() {
    document.getElementById('modalEditarEmpleado').style.display = 'none';
}

// ==============================================
// FUNCIONES PARA MANEJO DE EMPLEADOS
// ==============================================

function eliminarEmpleado() {
    if (confirm('¿Estás seguro de eliminar este empleado?')) {
        const id = document.getElementById('edit_id').value;
        fetch(`../controller/eliminarEmpleado.php?id=${id}`)
            .then(handleResponse)
            .then(data => {
                if (data.success) {
                    mostrarModalSuccess('Empleado eliminado correctamente');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    mostrarModalError('Error al eliminar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarModalError('Ocurrió un error al eliminar el empleado');
            });
    }
}

function mostrarDetallesEmpleado(empleado) {
    // Mostrar la sección si estaba oculta
    document.getElementById('employeeDetails').style.display = 'block';

    const roles = {
        '3': 'Recepcionista',
        '4': 'Cocinero', 
        '5': 'Camarero'
    };

    const tiposDoc = {
        'CC': 'C.C.',
        'PPP': 'Pasaporte'
    };

    const detailSection = document.querySelector('.employee-detail-content');
    detailSection.innerHTML = `
        <div class="employee-info">
            <div class="employee-avatar">👤</div>
            <div class="employee-data">
                <div class="name">${empleado.nombre} ${empleado.apellido}</div>
                <div class="details">${tiposDoc[empleado.tipoDocumento] || empleado.tipoDocumento} ${empleado.numeroDocumento}</div>
            </div>
        </div>
        <div class="contact-details">
            <div>Correo: ${empleado.email}</div>
            <div>Rol: ${roles[empleado.rol] || 'Desconocido'}</div>
        </div>
        <div class="contact-details">
            <div>Cel: ${empleado.telefono}</div>
            <div>Dirección: ${empleado.direccion}</div>
            <div class="action-buttons">
                <button class="action-button">✏</button>
            </div>
        </div>
    `;

    detailSection.querySelector('.action-button').addEventListener('click', () => abrirModalEdicion(empleado));
}

// ==============================================
// FUNCIONES AUXILIARES
// ==============================================

function handleResponse(response) {
    if (!response.ok) {
        throw new Error('Error en la respuesta del servidor');
    }
    return response.json();
}

function mostrarModalError(mensaje) {
    const modal = document.createElement('div');
    modal.className = 'notification error';
    modal.textContent = mensaje;
    document.body.appendChild(modal);
    setTimeout(() => modal.remove(), 3000);
}

function mostrarModalSuccess(mensaje) {
    const modal = document.createElement('div');
    modal.className = 'notification success';
    modal.textContent = mensaje;
    document.body.appendChild(modal);
    setTimeout(() => modal.remove(), 3000);
}

function validarInput(input, regex, replaceChars = '') {
    if (input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(regex, replaceChars);
        });
    }
}

// ==============================================
// EVENT LISTENERS Y CONFIGURACIÓN INICIAL
// ==============================================

function setupEventListeners() {
    // Event listeners para los botones de lupa
    document.querySelectorAll('.ver-detalle').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const empleado = {
                id: this.dataset.id,
                nombre: this.dataset.nombre,
                apellido: this.dataset.apellido,
                tipoDocumento: this.dataset.tipoDocumento,
                numeroDocumento: this.dataset.numeroDocumento,
                telefono: this.dataset.telefono,
                email: this.dataset.email,
                direccion: this.dataset.direccion,
                rol: this.dataset.rolcodigo,
                estado: this.dataset.estado
            };
            
            mostrarDetallesEmpleado(empleado);
            
            // Resaltar fila seleccionada
            document.querySelectorAll('.employee-table-row').forEach(row => {
                row.classList.toggle('selected', row === this.closest('.employee-table-row'));
            });
        });
    });

    // Validación de formularios
    validarInput(document.getElementById('nombre'), /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g);
    validarInput(document.getElementById('apellido'), /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g);
    validarInput(document.getElementById('numeroDocumento'), /\D/g);
    validarInput(document.getElementById('numeroTelefono'), /\D/g);
    validarInput(document.getElementById('edit_nombre'), /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g);
    validarInput(document.getElementById('edit_apellido'), /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g);
    validarInput(document.getElementById('edit_numeroTelefono'), /\D/g);

    // Cerrar modales al hacer clic fuera
    window.addEventListener('click', (event) => {
        ['modalEmpleado', 'modalErrores', 'modalEditarEmpleado'].forEach(modalId => {
            if (event.target === document.getElementById(modalId)) {
                document.getElementById(modalId).style.display = "none";
            }
        });
    });
}

// Inicialización cuando el DOM está listo
document.addEventListener("DOMContentLoaded", function() {
    // Mostrar mensajes de error/éxito si existen
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('error')) {
        const errorMessages = {
            'correo': 'El correo electrónico ya está registrado.',
            'documento': 'El número de documento ya está registrado.',
            'telefono': 'El número de teléfono ya está registrado.',
            'direccion': 'La dirección ya está registrada.',
            'update': 'Error al actualizar el empleado.',
            'delete': 'Error al eliminar el empleado.'
        };
        
        const message = errorMessages[urlParams.get('error')];
        if (message) mostrarModalError(message);
    }
    
    if (urlParams.has('success')) {
        mostrarModalSuccess('Empleado actualizado correctamente');
    }

    // Configurar event listeners
    setupEventListeners();
});
// === EVENTO PARA EL FORMULARIO DE EDITAR EMPLEADO ===
document.getElementById('formEditarEmpleado').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevenir el envío tradicional

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            mostrarModalSuccess(data.message);  // Muestra mensaje visual
            cerrarModalEditar();               // Cierra el modal
            setTimeout(() => window.location.reload(), 1500); // Recarga página
        } else {
            mostrarModalError(data.message);   // Muestra error si viene del servidor
        }
    })
    .catch(err => {
        console.error(err);
        mostrarModalError('Error inesperado al actualizar');
    });
});

</script>

</body>
</html>