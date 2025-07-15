<?php
/**
 * Dashboard de Cliente - HotelixHub
 * 
 * Muestra el perfil del cliente, datos personales y historial de reservas.
 * Permite editar información de contacto (email y teléfono).
 */

// 1. INCLUSIÓN DE ARCHIVOS NECESARIOS Y VERIFICACIÓN DE SESIÓN
require_once __DIR__ . '/../services/sessionManager.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Verificar que el rol sea cliente (ID 2)
if ($_SESSION['usuario']['usu_idrol'] != 2) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../assets/css/dashCliente.css">
</head>
<body>
    <!-- ==================== MENÚ HAMBURGUESA (SOLO PARA MÓVIL) ==================== -->
    <button class="menu-toggle" id="menuToggle">☰</button>

    <!-- ==================== BARRA LATERAL ==================== -->
    <aside class="barra-lateral" id="sidebar">
        <div class="logo">
            <a href="home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo"></a>
        </div>
        <nav>
            <a href="dashCliente.php"><i class="fa fa-home"></i>Inicio</a>
            <a href="reservas.php"><i class="fa fa-bed"></i>Reservas</a>
            <a href="productosClientes.php"><i class="fa fa-box"></i>Productos</a>
            <a href="../controller/logout.php"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a>
        </nav>
    </aside>

    <!-- ==================== CONTENIDO PRINCIPAL ==================== -->
    <main class="main">
        <!-- Encabezado -->
        <div class="main-header">
            <h1>Perfil</h1>
            <div class="btn-group">
                <button id="editBtn" class="btn-edit">Editar perfil</button>
                <button id="saveBtn" class="btn-save" disabled>Guardar</button>
            </div>
        </div>

        <!-- ==================== DATOS PERSONALES ==================== -->
        <section class="card">
            <h2>Datos Personales</h2>
            <div class="group">
                <!-- Columna de visualización -->
                <div class="col display">
                    <p><strong>Nombre:</strong> <span id="dispNombre"></span></p>
                    <p><strong>Tipo Doc.:</strong> <span id="dispTipo"></span></p>
                    <p><strong>Número Doc.:</strong> <span id="dispNum"></span></p>
                    <p><strong>País:</strong> <span id="dispPais"></span></p>
                </div>
                
                <!-- Columna de edición (solo algunos campos editables) -->
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

        <!-- ==================== DATOS DE CONTACTO ==================== -->
        <section class="card">
            <h2>Datos de Contacto</h2>
            <div class="group">
                <!-- Columna de visualización -->
                <div class="col display">
                    <p><strong>Email:</strong> <span id="dispEmail"></span></p>
                    <p><strong>Teléfono:</strong> <span id="dispTel"></span></p>
                </div>
                
                <!-- Columna de edición -->
                <div class="col edit">
                    <label for="inpEmail">Email</label>
                    <input id="inpEmail" type="email" value="" disabled/>
                    <label for="inpTel">Teléfono</label>
                    <input id="inpTel" type="tel" value="" disabled/>
                </div>
            </div>
        </section>

        <!-- ==================== HISTORIAL DE RESERVAS ==================== -->
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
                    <!-- Se llenará dinámicamente con JavaScript -->
                </tbody>
            </table>
        </section>
    </main>

    <!-- ==================== MODAL DE MENSAJES ==================== -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <p id="modalMessage"></p>
            <button id="modalCloseBtn">Cerrar</button>
        </div>
    </div>

    <!-- ==================== SCRIPTS ==================== -->
    <script>
    /* ========== 1. TOGGLE DEL MENÚ LATERAL (PARA MÓVIL) ========== */
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    /* ========== 2. CARGAR DATOS DEL CLIENTE Y RESERVAS ========== */
    document.addEventListener('DOMContentLoaded', () => {
        fetch('../controller/clienteInfoController.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    const cliente = data.cliente;
                    const reservas = data.reservas;
                    const tbody = document.getElementById('tablaReservasBody');
                    tbody.innerHTML = '';

                    // Mostrar reservas en tabla
                    if (reservas.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5">No hay reservas registradas.</td></tr>';
                    } else {
                        reservas.forEach(reserva => {
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

                    // Mostrar datos personales del cliente
                    if (cliente) {
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
                    } else {
                        openModal("Aviso", "No se encontraron datos del cliente.");
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

    /* ========== 3. FUNCIONALIDAD DEL MODAL ========== */
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

    // Cerrar modal al hacer clic fuera del contenido
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    /* ========== 4. FUNCIONES DE VALIDACIÓN ========== */
    function validarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
    }

    function validarTelefono(tel) {
        return /^[\d+\s]+$/.test(tel.trim());
    }

    /* ========== 5. BOTÓN EDITAR: HABILITA CAMPOS EDITABLES ========== */
    document.getElementById('editBtn').addEventListener('click', () => {
        // Solo habilitamos email y teléfono para edición
        document.getElementById('inpEmail').disabled = false;
        document.getElementById('inpTel').disabled = false;

        // Deshabilitamos el botón de edición y habilitamos el de guardar
        document.getElementById('editBtn').disabled = true;
        document.getElementById('saveBtn').disabled = false;
    });

    /* ========== 6. BOTÓN GUARDAR: VALIDA Y ACTUALIZA DATOS ========== */
    document.getElementById('saveBtn').addEventListener('click', () => {
        const email = document.getElementById('inpEmail').value.trim();
        const telefono = document.getElementById('inpTel').value.trim();
        let errores = [];

        // Validar email
        if (!validarEmail(email)) {
            errores.push("El email no tiene un formato válido.");
            document.getElementById('inpEmail').classList.add('invalid');
        } else {
            document.getElementById('inpEmail').classList.remove('invalid');
        }

        // Validar teléfono
        if (!validarTelefono(telefono)) {
            errores.push("El teléfono solo debe contener números, espacios o el símbolo '+'.");
            document.getElementById('inpTel').classList.add('invalid');
        } else {
            document.getElementById('inpTel').classList.remove('invalid');
        }

        // Mostrar errores si hay alguno
        if (errores.length > 0) {
            openModal("Errores de Validación", errores.join('\n'));
            return;
        }

        // Actualizar la interfaz
        document.getElementById('dispEmail').textContent = email;
        document.getElementById('dispTel').textContent = telefono;

        // Deshabilitar campos y cambiar estado de botones
        document.getElementById('inpEmail').disabled = true;
        document.getElementById('inpTel').disabled = true;
        document.getElementById('editBtn').disabled = false;
        document.getElementById('saveBtn').disabled = true;

        // Enviar datos al servidor
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