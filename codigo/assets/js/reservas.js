// Espera que el contenido del DOM esté completamente cargado
// antes de ejecutar el resto del script.

document.addEventListener('DOMContentLoaded', function () {

    // ========== VARIABLES GLOBALES ========== 
    const form = document.querySelector('.reserva-form'); // Formulario de reserva
    const modalOverlay = document.getElementById('modal-overlay'); // Modal de historial
    const closeModal = document.querySelector('.close-modal'); // Botón cerrar modal
    const btnNav = document.getElementById('btn-nav'); // Botón "Mis Reservas"
    const tipoHabitacion = document.getElementById('tipo-habitacion'); // Selector tipo habitación
    const btnReservar = document.querySelectorAll('.btn-reservar'); // Botones "Reservar" en tarjetas



    // ========== CONFIGURACIÓN INICIAL ==========
    // Asigna el tipo de habitación al hacer clic en los botones "Reservar"
    btnReservar.forEach(btn => {
        btn.addEventListener('click', function() {
            tipoHabitacion.value = this.getAttribute('data-tipo');
            document.querySelector('#formulario-reserva').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // ========== VALIDACIONES DEL FORMULARIO ==========
    function setupInputValidation() {

        // Valida que el nombre solo contenga letras y espacios
        const nombre = document.getElementById('nombre');
        nombre.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]/g, '');
        });

        // Valida que el teléfono solo contenga números y +
        const telefono = document.getElementById('telf');
        telefono.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });

        // Limpia errores al escribir en cualquier campo
        document.querySelectorAll('.reserva-form input, .reserva-form select, .reserva-form textarea').forEach(input => {
            input.addEventListener('input', function() {
                const error = this.parentElement.querySelector('.error');
                if (error) error.remove();
                this.classList.remove('input-error');
            });
        });
    }

    // Configura validación de fechas (entrada debe ser hoy o después)
    function setupDateValidation() {
        const checkIn = document.getElementById('check-in');
        const checkOut = document.getElementById('check-out');
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        checkIn.min = today.toISOString().split('T')[0];
        checkIn.min = today;

        checkIn.addEventListener('change', function() {
            if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOut.min = nextDay.toISOString().split('T')[0];

                if (checkOut.value && this.value > checkOut.value) {
                    mostrarError(checkOut, 'La salida debe ser posterior a la entrada');
                }
                calcularPrecio();
            }
        });

        checkOut.addEventListener('change', function() {
            if (checkIn.value && this.value < checkIn.value) {
                mostrarError(this, 'La salida debe ser posterior a la entrada');
            }
            calcularPrecio();
        });
    }

    // ========== FUNCIONES UTILITARIAS ==========
    function mostrarError(input, mensaje) {
        const existingError = input.parentElement.querySelector('.error');
        if (existingError) return;
        const error = document.createElement('p');
        error.className = 'error';
        error.textContent = mensaje;
        input.classList.add('input-error');
        input.parentElement.appendChild(error);
    }

    // Eliminar todos los errores visibles en el formulario
    function limpiarErrores() {
        document.querySelectorAll('.error').forEach(e => e.remove());
        document.querySelectorAll('.input-error').forEach(e => e.classList.remove('input-error'));
    }

    // Mostrar una notificación emergente (tipo puede ser "exito" o "error")
    function mostrarNotificacion(mensaje, tipo = 'exito') {
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion ${tipo}`;
        notificacion.textContent = mensaje;

        // Eliminar notificaciones anteriores para evitar acumulación
        document.querySelectorAll('.notificacion').forEach(n => n.remove());
        document.body.appendChild(notificacion);

        // Eliminar después de la animación
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
        }, 7000);
    }

    // ============= VALIDACION DE CORREO Y TELEFONO EN TIMEPO REAL ================
    function setupRealTimeValidation() {
        const emailInput = document.getElementById('email');
        const telefonoInput = document.getElementById('telf');

        // Validacion de correo 
        emailInput.addEventListener('input', function () {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const errorElement = this.parentElement.querySelector('.error');

            if (this.value && !emailRegex.test(this.value)) {
                if (!errorElement) {
                    mostrarError(this, 'Correo electrónico inválido');
                }
            } else if (errorElement) {
                errorElement.remove();
                this.classList.remove('input-error');
            }
        });

        // Validacion de telefono
        telefonoInput.addEventListener('input', function () {
            const telefonoRegex = /^(\+?[0-9]{1,3}[- ]?)?[0-9]{7,15}$/;
            const errorElement = this.parentElement.querySelector('.error');

            if (this.value && !telefonoRegex.test(this.value)) {
                if (!errorElement) {
                    mostrarError(this, 'Teléfono inválido. Ejemplo: +57 1234567');
                }
            } else if (errorElement) {  
                errorElement.remove();
                this.classList.remove('input-error');
            }
        });
    }

    // ========== MANEJO DEL MODAL ==========
    btnNav.addEventListener('click', function() {
        cargarReservas();
        modalOverlay.style.display = 'flex';
    });

    closeModal.addEventListener('click', function() {
        modalOverlay.style.display = 'none';
    });

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });

    async function cargarReservas() {
        const email = document.getElementById('email').value.trim();
        
        if (!email) {
            mostrarNotificacion('Ingrese su email para ver reservas', 'info');
            return;
        }

        try {
            const response = await fetch(`/HotelixHub/codigo/controller/ReservaController.php?accion=listar&email=${encodeURIComponent(email)}`);
            
            // Validar que la respuesta sea válida
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }

            const reservas = await response.json();

            if (!Array.isArray(reservas) || reservas.length === 0) {
                document.querySelector('.historial-contenedor').innerHTML = '<p>No hay reservas registradas para este email.</p>';
                return;
            }

            // Mostrar en el modal
            mostrarReservasEnModal(reservas);
            
        } catch (error) {
            console.error('Error al cargar reservas:', error);
            mostrarNotificacion('Error al cargar reservas', 'error');
        }
    }




    function mostrarReservasEnModal(reservas) {
        const contenedor = document.querySelector('.historial-contenedor');
        contenedor.innerHTML = '';

        reservas.forEach(reserva => {
            let servicios = 'Ninguno';
            try {
                const lista = JSON.parse(reserva.servicios_adicionales);
                if (Array.isArray(lista) && lista.length > 0) {
                    servicios = lista.join(', ');
                }
            } catch (e) {
                console.warn('Error al parsear servicios:', e);
            }


            const fechaEntrada = new Date(reserva.fecha_entrada).toLocaleDateString();
            const fechaSalida = new Date(reserva.fecha_salida).toLocaleDateString();
            const fechaReserva = new Date(reserva.fecha_reserva).toLocaleString();

            const reservaItem = document.createElement('div');
            reservaItem.className = 'reserva-item';
            reservaItem.innerHTML = `
                <h3>${reserva.nombre_habitacion} - ${reserva.tipoHabitacion}</h3>
                <p><strong>Fecha reserva:</strong> ${fechaReserva}</p>
                <p><strong>Estancia:</strong> ${fechaEntrada} al ${fechaSalida}</p>
                <p><strong>Huéspedes:</strong> ${reserva.num_huespedes}</p>
                <p><strong>Servicios:</strong> ${servicios}</p>
                <p><strong>Total:</strong> $${Number(reserva.precio_total).toLocaleString('es-CO')} COP</p>
                <p><strong>Estado:</strong> <span class="estado-${reserva.estado.toLowerCase()}">${reserva.estado}</span></p>

                <div class="botones-reserva">
                    <button class="btn-eliminar" data-id="${reserva.id_reserva}">Cancelar</button>
                    <a href="/HotelixHub/codigo/pdf/generarRecibo.php?id=${reserva.id_reserva}" target="_blank" class="btn-descargar"> Ver Recibo</a>
                </div>
            `;
            reservaItem.querySelector('.btn-eliminar').addEventListener('click', async function () {
                const id = this.getAttribute('data-id');
                if (confirm('¿Seguro que deseas cancelar esta reserva del historial?')) {
                    try {
                        const res = await fetch(`/HotelixHub/codigo/controller/ReservaController.php`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ accion: 'cancelar', id_reserva: id })
                        });

                        const data = await res.json();
                        if (data.success) {
                            mostrarNotificacion('Reserva cancelada con éxito');
                            cargarReservas(); // recarga historial
                        } else {
                            mostrarNotificacion('No se pudo cancelar la reserva', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        mostrarNotificacion('Error al cancelar reserva', 'error');
                    }
                }
            });

            contenedor.appendChild(reservaItem);
        });
    }



    // ========== CÁLCULO DE PRECIO ==========
    // ---------------------------
    // FUNCIÓN: Calcular precio total estimado de la reserva
    // Incluye habitación + servicios adicionales
    // ---------------------------
    function calcularPrecio() {
        // Obtener valores clave desde el formulario
        const tipo = tipoHabitacion.value;
        const checkIn = document.getElementById('check-in').value;
        const checkOut = document.getElementById('check-out').value;
        const huespedes = parseInt(document.getElementById('huesped').value) || 1;

        // Obtener lista de servicios seleccionados
        const serviciosSeleccionados = Array.from(
            document.querySelectorAll('input[name="servicios"]:checked')
        ).map(cb => cb.value);

        // Solo calcular si hay tipo de habitación y fechas válidas
        if (!tipo || !checkIn || !checkOut) return;

        // Precios base por noche según tipo de habitación
        const preciosHabitacion = {
            sencilla: 150000,
            doble: 220000,
            triple: 300000
        };

        // Precios fijos de los servicios adicionales
        const precioServicios = {
            "Spa": 80000,
            "Desayuno Buffet": 35000 * huespedes,       // Por persona
            "Parqueadero": 20000,                       // Por noche
            "Lavandería": 45000,
            "Transporte": 60000
        };

        // Calcular número de noches entre check-in y check-out
        const fechaInicio = new Date(checkIn);
        const fechaFin = new Date(checkOut);
        const noches = (fechaFin - fechaInicio) / (1000 * 60 * 60 * 24); // Diferencia en días

        // Calcular precio base por noches
        let total = preciosHabitacion[tipo] * noches;

        // Agregar precio de servicios seleccionados
        for (const servicio of serviciosSeleccionados) {
            if (servicio === "Parqueadero") {
                total += precioServicios[servicio] * noches; // Por noche
            } else {
                total += precioServicios[servicio]; // Precio fijo o por persona
            }
        }

        // Mostrar resultado debajo del formulario
        let precioElement = document.getElementById('precio-total');
        if (!precioElement) {
            precioElement = document.createElement('div');
            precioElement.id = 'precio-total';
            precioElement.className = 'precio-total';
            form.insertBefore(precioElement, form.querySelector('button[type="submit"]'));
        }

        // Formatear en pesos colombianos y mostrar
        precioElement.innerHTML = `
            <strong>Total estimado:</strong> ${total.toLocaleString('es-CO', { style: 'currency', currency: 'COP' })}
            <small>(${noches} noches x habitación + servicios)</small>
        `;
    }


    
    function setupServiciosValidation() {
        const checkboxes = document.querySelectorAll('input[name="servicios"]');
        const errorServicios = document.getElementById('error-servicios');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const seleccionados = document.querySelectorAll('input[name="servicios"]:checked');
                if (seleccionados.length > 3) {
                    this.checked = false;
                    errorServicios.style.display = 'inline';
                } else {
                    errorServicios.style.display = 'none';
                }
            });
        });
    }

    // ========== FUNCIÓN DE VALIDACIÓN COMPLETA ==========

    function validarReservaCompleta() {
        let isValid = true;
        
        // Obtener valores actuales del formulario
        const reserva = {
            nombre: document.getElementById('nombre').value.trim(),
            apellido: document.getElementById('apellido').value.trim(),
            telefono: document.getElementById('telf').value.trim(),
            email: document.getElementById('email').value.trim(),
            tipoHabitacion: document.getElementById('tipo-habitacion').value,
            huespedes: parseInt(document.getElementById('huesped').value) || 0,
            checkIn: document.getElementById('check-in').value,
            checkOut: document.getElementById('check-out').value
        };


        // Limpiar errores previos
        limpiarErrores();

        

        // 1. Validación de nombre (solo caracteres permitidos y longitud mínima)
        if (reserva.nombre.length < 2 || !/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]+$/.test(reserva.nombre)) {
            mostrarError(document.getElementById('nombre'), 'Ingrese un nombre válido (mínimo 2 letras)');
            isValid = false;
        }


        // 2. Validación de teléfono (formato internacional)
        if (!/^(\+?[0-9]{1,3}[- ]?)?[0-9]{7,15}$/.test(reserva.telefono)) {
            mostrarError(document.getElementById('telf'), 'Teléfono inválido. Ejemplo: +57 1234567');
            isValid = false;
        }

        // Apellido: solo letras
        if (reserva.apellido.length < 2 || !/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]+$/.test(reserva.apellido)) {
            mostrarError(document.getElementById('apellido'), 'Ingrese un apellido válido');
            isValid = false;
        }

        // Tipo de documento
        if (!document.getElementById('tipoDocumento').value) {
            mostrarError(document.getElementById('tipoDocumento'), 'Seleccione un tipo de documento');
            isValid = false;
        }

        const tipoDoc = document.getElementById('tipoDocumento');
        const numeroDocumento = document.getElementById('numeroDocumento').value.trim();

        if (tipoDoc.value === 'PA') {
            if (!/^[a-zA-Z0-9]{6,20}$/.test(numeroDocumento)) {
                mostrarError(document.getElementById('numeroDocumento'), 'Pasaporte inválido (6-20 letras/números)');
                isValid = false;
            }
        } else {
            if (!/^[0-9]{6,20}$/.test(numeroDocumento)) {
                mostrarError(document.getElementById('numeroDocumento'), 'Documento inválido (solo números, entre 6 y 20 dígitos)');
                isValid = false;
            }
        }



        // 3. Validación de número de huéspedes según tipo de habitación
        const maxHuespedes = { 
            sencilla: 1, 
            doble: 2, 
            triple: 3,
            default: 1 // Valor por defecto si el tipo no coincide
        };

        // Obtener el máximo permitido para el tipo de habitación (con manejo de casos no definidos)
        const tipoHabitacionLower = reserva.tipoHabitacion?.toLowerCase();
        const maxPermitido = maxHuespedes[tipoHabitacionLower] || maxHuespedes.default;

        if (!reserva.tipoHabitacion) {
            mostrarError(document.getElementById('tipo-habitacion'), 'Seleccione un tipo de habitación');
            isValid = false;
        } else if (reserva.huespedes < 1) {
            mostrarError(document.getElementById('huesped'), 'Debe haber al menos 1 huésped');
            isValid = false;
        } else if (reserva.huespedes > maxPermitido) {
            mostrarError(document.getElementById('huesped'), 
                `Máximo ${maxPermitido} huéspedes para habitación ${reserva.tipoHabitacion}`);
            isValid = false;
        }

        // 4. Validación de email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(reserva.email)) {
            mostrarError(document.getElementById('email'), 'Correo electrónico inválido');
            isValid = false;
        }

        // 5. Validación de tipo de habitación seleccionado
        if (!reserva.tipoHabitacion) {
            mostrarError(document.getElementById('tipo-habitacion'), 'Seleccione un tipo de habitación');
            isValid = false;
        }

        // 6. Validación de fechas
        const checkInInput = document.getElementById('check-in');
        const checkOutInput = document.getElementById('check-out');
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0); // Normalizar hora

        let fechaEntrada = null;
        let fechaSalida = null;

        // Validar check-in
        if (!checkInInput.value) {
            mostrarError(checkInInput, 'Seleccione una fecha de entrada');
            isValid = false;
        } else {
            fechaEntrada = new Date(checkInInput.value + 'T00:00'); // Asegura formato válido
            if (fechaEntrada < hoy) {
                mostrarError(checkInInput, 'Fecha inválida (debe ser hoy o después)');
                isValid = false;
            }
        }

        // Validar check-out
        if (!checkOutInput.value) {
            mostrarError(checkOutInput, 'Seleccione una fecha de salida');
            isValid = false;
        } else {
            fechaSalida = new Date(checkOutInput.value + 'T00:00');
            if (fechaEntrada && fechaSalida <= fechaEntrada) {
                mostrarError(checkOutInput, 'Debe ser posterior a la entrada');
                isValid = false;
            }
        }

        // Validación de duración de estadía
        if (fechaEntrada && fechaSalida) {
            const diferenciaDias = (fechaSalida - fechaEntrada) / (1000 * 60 * 60 * 24);

            if (diferenciaDias < 1) {
                mostrarError(checkOutInput, 'Mínimo 1 noche de estadía');
                isValid = false;
            } else if (diferenciaDias > 30) {
                mostrarError(checkOutInput, 'Máximo 30 noches de estadía');
                isValid = false;
            }
        }

        // 8. Validación de servicios adicionales (máximo 3)
        const serviciosSeleccionados = document.querySelectorAll('input[name="servicios"]:checked');
        if (serviciosSeleccionados.length > 3) {
            document.getElementById('error-servicios').style.display = 'inline';
            isValid = false;
        } else {
            document.getElementById('error-servicios').style.display = 'none';
        }

        const idHabitacion = document.getElementById('id_habitacion_asignada').value;
        if (!idHabitacion) {
            mostrarError(document.getElementById('tipo-habitacion'), 'No hay habitación disponible para estas fechas');
            isValid = false;
        }

        return isValid;
    }

    async function autocompletarFormularioConSesion() {
        try {
            const res = await fetch('/HotelixHub/codigo/api/apiUsuario.php');
            const datos = await res.json();

            if (datos.error) {
                console.warn('Sesión no activa');
                return;
            }

            document.getElementById('nombre').value = datos.nombre || '';
            document.getElementById('apellido').value = datos.apellido || '';
            document.getElementById('tipoDocumento').value = datos.tipoDocumento || '';
            document.getElementById('numeroDocumento').value = datos.numeroDocumento || '';
            document.getElementById('telf').value = datos.numeroTelefono || '';
            document.getElementById('email').value = datos.email || '';
        } catch (error) {
            console.error('Error al cargar datos del usuario:', error);
        }
    }

    /*async function consultarHabitacionDisponible() {
        const tipo = document.getElementById('tipo-habitacion').value;
        const entrada = document.getElementById('check-in').value;
        const salida = document.getElementById('check-out').value;

        if (!tipo || !entrada || !salida) {
            document.getElementById('habitacionAsignada').value = '';
            document.getElementById('id_habitacion_asignada').value = '';
            return;
        }

        try {
            const res = await fetch(`/Codigo/apiHabitacionDisponible.php?tipo=${encodeURIComponent(tipo)}&entrada=${entrada}&salida=${salida}`);
            const data = await res.json();

            if (data.disponible) {
                document.getElementById('habitacionAsignada').value = data.nombre_habitacion;
                document.getElementById('id_habitacion_asignada').value = data.id_habitacion;
            } else {
                document.getElementById('habitacionAsignada').value = 'No disponible en esas fechas';
                document.getElementById('id_habitacion_asignada').value = '';
            }

        } catch (error) {
            console.error('Error consultando habitación:', error);
            document.getElementById('habitacionAsignada').value = 'Error al verificar';
        }
    }*/

    // ================== MODAL DE RESUMEN DE RESERVA ==================

    // Muestra el resumen de la reserva en el modal
    function mostrarResumenReserva() {
        document.getElementById('res-nombre').textContent = document.getElementById('nombre').value;
        document.getElementById('res-apellido').textContent = document.getElementById('apellido').value;
        document.getElementById('res-doc-tipo').textContent = document.getElementById('tipoDocumento').value;
        document.getElementById('res-doc-num').textContent = document.getElementById('numeroDocumento').value;
        document.getElementById('res-email').textContent = document.getElementById('email').value;
        document.getElementById('res-telefono').textContent = document.getElementById('telf').value;
        document.getElementById('res-huespedes').textContent = document.getElementById('huesped').value;
        document.getElementById('res-checkin').textContent = document.getElementById('check-in').value;
        document.getElementById('res-checkout').textContent = document.getElementById('check-out').value;

        const servicios = Array.from(document.querySelectorAll('input[name="servicios"]:checked'))
            .map(cb => cb.value)
            .join(', ');
        document.getElementById('res-servicios').textContent = servicios || 'Ninguno';

        // Datos de habitación desde variables globales (defínelas al seleccionar habitación)
        document.getElementById('res-hab-nombre').textContent = document.getElementById('habitacionAsignada').value;
        document.getElementById('res-hab-tipo').textContent = document.getElementById('tipo-habitacion').value;
        document.getElementById('res-hab-piso').textContent = window.habPisoSeleccionado || '-';
        document.getElementById('res-hab-servicios').textContent = window.habServiciosSeleccionados || '-';
        document.getElementById('res-hab-precio').textContent = window.habPrecioSeleccionado
            ? `$${parseInt(window.habPrecioSeleccionado).toLocaleString('es-CO')} COP`
            : '-';
        document.getElementById('res-imagen').src = window.habImagenSeleccionada || '';

        // Mostrar modal
        document.getElementById('modal-confirmacion').style.display = 'flex';
    }

    // Ocultar modal si se hace clic en "Cancelar"
    document.getElementById('btn-cancelar').addEventListener('click', () => {
        document.getElementById('modal-confirmacion').style.display = 'none';
    });


    // Interceptar el formulario original para mostrar el modal primero
    document.getElementById('formulario-reserva').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validarReservaCompleta()) return;

        mostrarResumenReserva();

        // Obtener ID de la habitación seleccionada
        const idHab = document.getElementById('id_habitacion_asignada').value;
        const habitacion = habitacionesDisponibles.find(h => String(h.id_habitacion) === String(idHab));

        if (habitacion) {
            document.getElementById('res-hab-nombre').textContent = habitacion.nombre;
            document.getElementById('res-hab-tipo').textContent = habitacion.tipoHabitacion;
            document.getElementById('res-hab-piso').textContent = habitacion.piso;
            document.getElementById('res-hab-servicios').textContent = habitacion.serviciosIncluidos;
            document.getElementById('res-hab-precio').textContent = `$${Number(habitacion.precio).toLocaleString('es-CO')} COP`;

            const imagenPath = habitacion.imagen && habitacion.imagen !== ''
                ? '/HotelixHub/codigo/' + habitacion.imagen
                : '/HotelixHub/codigo/uploads/habitaciones/no-imagen.png';

            document.getElementById('res-imagen').src = imagenPath;
            document.getElementById('res-imagen').alt = 'Imagen habitación ' + habitacion.nombre;
        }

        // Mostrar el modal de confirmación
        document.getElementById('modal-confirmacion').style.display = 'flex';
    });



    // ================== ASIGNAR DATOS AL CLIC EN RESERVAR ==================
    document.querySelectorAll('.btn-reservar').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.card-hab');
            const tipo = this.getAttribute('data-tipo');

            // Asigna tipo de habitación al formulario
            document.getElementById('tipo-habitacion').value = tipo;
            document.querySelector('#formulario-reserva').scrollIntoView({ behavior: 'smooth' });

            // Asignar datos simulados (deberías conectar con la BDD si es dinámico)
            window.habPisoSeleccionado = card.getAttribute('data-piso') || '3';
            window.habServiciosSeleccionados = card.getAttribute('data-servicios') || 'TV, Wifi';
            window.habPrecioSeleccionado = card.getAttribute('data-precio') || '150000';
            window.habImagenSeleccionada = card.querySelector('img')?.src || '';
        });
    });

    // ================= CARGAR HABITACIONES DINÁMICAS =================

    let habitacionesDisponibles = []; // Variable global

    async function cargarHabitaciones() {
        try {
            const response = await fetch('/HotelixHub/codigo/api/apiHabitaciones.php?accion=listarDisponibles');

            habitacionesDisponibles = await response.json(); // Guardar globalmente
            const habitaciones = habitacionesDisponibles;

            const contenedor = document.getElementById('contenedor-habitaciones');
            contenedor.innerHTML = '';

            habitaciones.forEach(hab => {
                const card = document.createElement('div');
                card.classList.add('card-hab');
                card.innerHTML = `
                    <img src="/HotelixHub/codigo/${hab.imagen}" alt="${hab.nombre}">
                    <h3>Habitación ${hab.nombre}</h3>
                    <p><strong>Piso:</strong> ${hab.piso}</p>
                    <p><strong>Tipo:</strong> ${hab.tipoHabitacion}</p>
                    <ul>
                        ${hab.serviciosIncluidos.split(',').map(s => `<li>${s.trim()}</li>`).join('')}
                    </ul>
                    <p class="precio">$${Number(hab.precio).toLocaleString('es-CO')} <span>cop</span></p>
                    <button type="button" class="btn-reservar"
                        data-id="${hab.id_habitacion}"
                        data-nombre="${hab.nombre}"
                        data-tipo="${hab.tipoHabitacion}"
                        data-piso="${hab.piso}"
                        data-servicios="${hab.serviciosIncluidos}"
                        data-precio="${hab.precio}"
                        data-imagen="${hab.imagen}">
                        Reservar
                    </button>
                `;

                // Solo llena el formulario (NO muestra modal aquí)
                card.querySelector('.btn-reservar').addEventListener('click', function () {
                    document.getElementById('id_habitacion_asignada').value = this.dataset.id;
                    document.getElementById('habitacionAsignada').value = this.dataset.nombre;
                    document.getElementById('tipo-habitacion').value = this.dataset.tipo.toLowerCase();

                    // Desplazar al formulario
                    const form = document.getElementById('formulario-reserva');
                    if (form) {
                        form.scrollIntoView({ behavior: 'smooth' });
                    }
                });

                contenedor.appendChild(card);
            });
        } catch (error) {
            console.error('Error al cargar habitaciones:', error);
        }
    }


    document.getElementById('btn-confirmar').addEventListener('click', async () => {
        const idHab = document.getElementById('id_habitacion_asignada').value;

        if (!idHab) {
            mostrarNotificacion('Seleccione una habitación antes de confirmar.', 'error');
            return;
        }

        const habitacion = habitacionesDisponibles.find(h => String(h.id_habitacion) === String(idHab));

        if (!habitacion) {
            mostrarNotificacion('No se encontraron datos de la habitación.', 'error');
            return;
        }

        mostrarResumenReserva();

        // Mostrar resumen en el modal
        document.getElementById('res-hab-nombre').textContent = habitacion.nombre;
        document.getElementById('res-hab-tipo').textContent = habitacion.tipoHabitacion;
        document.getElementById('res-hab-piso').textContent = habitacion.piso;
        document.getElementById('res-hab-servicios').textContent = habitacion.serviciosIncluidos;
        document.getElementById('res-hab-precio').textContent = `$${Number(habitacion.precio).toLocaleString('es-CO')} COP`;

        const imagenPath = habitacion.imagen && habitacion.imagen !== ''
            ? '/HotelixHub/codigo/' + habitacion.imagen
            : '/HotelixHub/codigo/uploads/habitaciones/no-imagen.png';

        document.getElementById('res-imagen').src = imagenPath;
        document.getElementById('res-imagen').alt = 'Imagen habitación ' + habitacion.nombre;

        // Enviar datos al backend
        const formData = {
            accion: "crear",
            nombre: document.getElementById("nombre").value,
            apellido: document.getElementById("apellido").value,
            tipoDocumento: document.getElementById("tipoDocumento").value,
            numeroDocumento: document.getElementById("numeroDocumento").value,
            telefono: document.getElementById("telf").value,
            email: document.getElementById("email").value,
            huesped: document.getElementById("huesped").value,
            tipoHabitacion: document.getElementById("tipo-habitacion").value,
            checkIn: document.getElementById("check-in").value,
            checkOut: document.getElementById("check-out").value,
            servicios: Array.from(document.querySelectorAll('input[name="servicios"]:checked')).map(cb => cb.value),
            id_habitacion: idHab
        };

        try {
            const response = await fetch("/HotelixHub/codigo/api/reservas.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const resultado = await response.json();

            if (resultado.success) {
                mostrarNotificacion("Reserva confirmada con éxito");

                // Cerrar el modal de resumen
                document.getElementById('modal-confirmacion').style.display = 'none';

                // Limpiar solo los campos necesarios
                document.getElementById("huesped").value = '';
                document.getElementById("tipo-habitacion").value = '';
                document.getElementById("check-in").value = '';
                document.getElementById("check-out").value = '';
                document.getElementById("habitacionAsignada").value = '';
                document.getElementById("id_habitacion_asignada").value = '';
                document.querySelectorAll('input[name="servicios"]').forEach(cb => cb.checked = false);

                // Recargar historial de reservas
                if (typeof cargarReservas === 'function') {
                    cargarReservas();
                }

                // Descargar PDF automáticamente si el backend devuelve el ID
                if (resultado.id_reserva) {
                    const link = document.createElement('a');
                    link.href = `/HotelixHub/codigo/pdf/generarRecibo.php?id=${resultado.id_reserva}`;
                    link.download = `recibo-reserva-${resultado.id_reserva}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

            } else {
                mostrarNotificacion("Error: " + (resultado.error || "No se pudo guardar la reserva."), 'error');
            }

        } catch (error) {
            console.error("Error en la solicitud:", error);
            mostrarNotificacion("Error al conectar con el servidor", 'error');
        }
    });


    // ========== INICIALIZACIÓN ==========
    cargarHabitaciones();
    autocompletarFormularioConSesion();
    setupInputValidation(); 
    setupDateValidation();
    setupRealTimeValidation();

    tipoHabitacion.addEventListener('change', calcularPrecio);
    document.getElementById('check-out').addEventListener('change', calcularPrecio);
    document.getElementById('huesped').addEventListener('input', calcularPrecio);
    document.querySelectorAll('input[name="servicios"]').forEach(cb =>
        cb.addEventListener('change', calcularPrecio)
    );
    setupServiciosValidation();
    //document.getElementById('tipo-habitacion').addEventListener('change', consultarHabitacionDisponible);
    //document.getElementById('check-in').addEventListener('change', consultarHabitacionDisponible);
    //document.getElementById('check-out').addEventListener('change', consultarHabitacionDisponible);


    // ======================= VALIDACIÓN EN TIEMPO REAL DE CAMPOS ESPECÍFICOS =======================

    document.getElementById('apellido').addEventListener('input', function () {
        this.value = this.value.replace(/[0-9]/g, ''); // elimina números
    });

    document.getElementById('numeroDocumento').addEventListener('input', function () {
        const tipoDoc = document.getElementById('tipoDocumento').value;

        if (tipoDoc === 'PA') {
            // Pasaporte → permitir letras y números, eliminar caracteres no válidos
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
        } else {
            // Otro documento → permitir solo números
            this.value = this.value.replace(/[^0-9]/g, '');
        }

        // Límite de 20 caracteres
        if (this.value.length > 20) {
            this.value = this.value.slice(0, 20);
        }
    });


});