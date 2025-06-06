// Espera que el contenido del DOM esté completamente cargado
// antes de ejecutar el resto del script.

document.addEventListener('DOMContentLoaded', function () {

    // ========== VARIABLES GLOBALES ==========
    let reservasTemporales = []; // Almacena reservas temporalmente (sin localStorage)
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

    // ========== MANEJO DEL FORMULARIO ==========
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        limpiarErrores();

        // Declarar submitBtn aquí para que esté disponible en todo el bloque
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Obtener valores del formulario
        const reserva = {
            nombre: document.getElementById('nombre').value.trim(),
            telefono: document.getElementById('telf').value.trim(),
            huespedes: document.getElementById('huesped').value,
            email: document.getElementById('email').value.trim(),
            tipoHabitacion: tipoHabitacion.value,
            checkIn: document.getElementById('check-in').value,
            checkOut: document.getElementById('check-out').value,
            servicios: Array.from(document.querySelectorAll('input[name="servicios"]:checked')).map(el => el.value),
            fechaReserva: new Date().toLocaleString()
        };

        // Validar antes de enviar
        if (!validarReserva(reserva)) return;

        try {
            // Mostrar estado de carga
            submitBtn.innerHTML = '<span class="loading"></span>';
            submitBtn.disabled = true;

            // Simular envío a servidor
            await new Promise(resolve => setTimeout(resolve, 1500));

            // Guardar reserva temporalmente
            reserva.id = Date.now();
            reservasTemporales.push(reserva);

            // Mostrar notificación y limpiar formulario
            mostrarNotificacion('Reserva realizada con éxito!', 'exito');
            form.reset();
            document.getElementById('precio-total')?.remove();
            document.getElementById('error-servicios').style.display = 'none';

        } catch (error) {
            mostrarNotificacion('Error al procesar la reserva', 'error');
            console.error('Error:', error);
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    // ========== FUNCIÓN DE VALIDACIÓN COMPLETA ==========
    function validarReserva(reserva) {
        let isValid = true;

        // Validar nombre (mínimo 2 palabras con más de 1 carácter)
        if (reserva.nombre.length < 3 || reserva.nombre.split(' ').filter(w => w.length > 1).length < 2) {
            mostrarError(document.getElementById('nombre'), 'Ingrese nombre y apellido completos');
            isValid = false;
        }

        // Validar teléfono (formato internacional)
        if (!/^(\+?[0-9]{1,3}[- ]?)?[0-9]{7,15}$/.test(reserva.telefono)) {
            mostrarError(document.getElementById('telf'), 'Teléfono inválido. Ejemplo: +57 1234567');
            isValid = false;
        }

        // Validar número de huéspedes según tipo de habitación
        const maxHuespedes = { sencilla: 1, doble: 2, triple: 3 };
        if (reserva.tipoHabitacion && 
            (reserva.huespedes < 1 || reserva.huespedes > maxHuespedes[reserva.tipoHabitacion])) {
            mostrarError(document.getElementById('huesped'), 
                `Máximo ${maxHuespedes[reserva.tipoHabitacion]} huéspedes para habitación ${reserva.tipoHabitacion}`);
            isValid = false;
        }

        // Validar email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(reserva.email)) {
            mostrarError(document.getElementById('email'), 'Correo electrónico inválido');
            isValid = false;
        }

        // Validar tipo de habitación seleccionado
        if (!reserva.tipoHabitacion) {
            mostrarError(tipoHabitacion, 'Seleccione un tipo de habitación');
            isValid = false;
        }

        // Validar fechas
        const hoy = new Date();
        const año = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0'); // +1 porque enero es 0
        const dia = String(hoy.getDate()).padStart(2, '0');
        const hoyStr = `${año}-${mes}-${dia}`;

        // Validar check-in
        if (!reserva.checkIn) {
            mostrarError(document.getElementById('check-in'), 'Seleccione una fecha de entrada');
            isValid = false;
        } else if (reserva.checkIn < hoyStr) {
            mostrarError(document.getElementById('check-in'), 'Fecha inválida (debe ser hoy o después)');
            isValid = false;
        }

        // Validar check-out
        if (!reserva.checkOut) {
            mostrarError(document.getElementById('check-out'), 'Seleccione una fecha de salida');
            isValid = false;
        } else if (reserva.checkIn && reserva.checkOut <= reserva.checkIn) {
            mostrarError(document.getElementById('check-out'), 'Debe ser posterior a la entrada');
            isValid = false;
        }

        // Validar duración
        if (reserva.checkIn && reserva.checkOut) {
            const fechaInicio = new Date(reserva.checkIn);
            const fechaFin = new Date(reserva.checkOut);
            const unDiaEnMs = 24 * 60 * 60 * 1000;
            const diferenciaDias = (fechaFin - fechaInicio) / unDiaEnMs;

            if (diferenciaDias < 1) {
                mostrarError(document.getElementById('check-out'), 'Mínimo 1 noche de estadía');
                isValid = false;
            }

            if (diferenciaDias > 30) {
                mostrarError(document.getElementById('check-out'), 'Máximo 30 noches de estadía');
                isValid = false;
            }
        }

        
        // Validar que no haya más de 3 servicios seleccionados
        const serviciosSeleccionados = document.querySelectorAll('input[name="servicios"]:checked');
        if (serviciosSeleccionados.length > 3) {
            document.getElementById('error-servicios').style.display = 'inline';
            isValid = false;
        }

        return isValid;
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

    function cargarReservas() {
        const contenedor = document.querySelector('.historial-contenedor');
        contenedor.innerHTML = '';

        if (reservasTemporales.length === 0) {
            contenedor.innerHTML = '<p>No hay reservas registradas en esta sesión.</p>';
            return;
        }

        mostrarReservasEnModal(reservasTemporales);
    }

    function mostrarReservasEnModal(reservas) {
        const contenedor = document.querySelector('.historial-contenedor');
        contenedor.innerHTML = '';

        reservas.forEach(reserva => {
            const reservaItem = document.createElement('div');
            reservaItem.className = 'reserva-item';
            reservaItem.innerHTML = `
                <h3>${reserva.tipoHabitacion.toUpperCase()} - ${reserva.nombre}</h3>
                <p><strong>Fecha reserva:</strong> ${reserva.fechaReserva}</p>
                <p><strong>Estancia:</strong> ${reserva.checkIn} al ${reserva.checkOut}</p>
                <p><strong>Huéspedes:</strong> ${reserva.huespedes}</p>
                ${reserva.servicios ? `<p><strong>Servicios:</strong> ${reserva.servicios}</p>` : ''}
                <p class="nota-bdd"><em>Nota: Estas son reservas de la sesión actual. Con BDD se verían todas.</em></p>
            `;
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


    // ========== INICIALIZACIÓN ==========
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
});
