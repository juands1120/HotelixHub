document.addEventListener('DOMContentLoaded', function() {

    // ==================================================================
    // FUNCIONES UTILITARIAS
    // ==================================================================
    function mostrarError(input, mensaje) {
        const erroresAnteriores = input.parentNode.querySelectorAll('.error-js');
        erroresAnteriores.forEach(error => error.remove());
        const error = document.createElement('span');
        error.className = 'error error-js';
        error.textContent = mensaje;
        input.parentNode.appendChild(error);
        input.classList.add('input-error');
    }

    function limpiarError(input) {
        input.addEventListener('input', function() {
            input.classList.remove('input-error');
            const error = input.parentNode.querySelector('.error-js');
            if (error) error.remove();
        });
    }

    // ==================================================================
    // FORMULARIO RÁPIDO DE RESERVAS (home.html)
    // ==================================================================
    const formRapido = document.querySelector('#formulario form');
    if (formRapido) {
        const inputsRapido = formRapido.querySelectorAll('input');
        inputsRapido.forEach(input => limpiarError(input));

        formRapido.addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            const checkin = document.getElementById('checkin');
            const checkout = document.getElementById('checkout');
            const invitados = document.getElementById('invitados');

            // VALIDACIÓN CHECK-IN
            if (checkin.value === '') {
                mostrarError(checkin, 'Ingresa fecha de check-in');
                isValid = false;
            } else {
                const fechaCheckin = new Date(checkin.value + "T00:00:00");
                const hoy = new Date();
                hoy.setHours(0,0,0,0);
                if (fechaCheckin < hoy) {
                    mostrarError(checkin, 'No puedes seleccionar fechas pasadas');
                    isValid = false;
                }
            }

            // VALIDACIÓN CHECK-OUT
            if (checkout.value === '') {
                mostrarError(checkout, 'Ingresa fecha de check-out');
                isValid = false;
            } else if (checkin.value && checkout.value && new Date(checkin.value) >= new Date(checkout.value)) {
                mostrarError(checkout, 'Check-out debe ser posterior');
                isValid = false;
            }

            // VALIDACIÓN INVITADOS
            if (!invitados.value || parseInt(invitados.value) < 1) {
                mostrarError(invitados, 'Debe haber al menos 1 invitado');
                isValid = false;
            }

            if (isValid) {
                const url = `/HotelixHub/codigo/views/reservas.php?checkin=${checkin.value}&checkout=${checkout.value}&huespedes=${invitados.value}`;
                window.location.href = url;
            }
        });
    }

    // ==================================================================
    // AUTO-RELLENO EN reservas.html
    // ==================================================================
    const formularioReserva = document.querySelector('#formulario-reserva');
    if (formularioReserva) {
        const params = new URLSearchParams(window.location.search);
        const checkin = params.get('checkin');
        const checkout = params.get('checkout');
        const huespedes = params.get('huespedes');

        if (checkin) document.getElementById('check-in').value = checkin;
        if (checkout) document.getElementById('check-out').value = checkout;
        if (huespedes) document.getElementById('huesped').value = huespedes;
    }

    // ==================================================================
    // VALIDACIÓN FORMULARIO DE CONTACTO
    // ==================================================================
    const contactoForm = document.querySelector('#contacto form');
    if (contactoForm) {
        const inputsContacto = contactoForm.querySelectorAll('input, select');
        inputsContacto.forEach(input => limpiarError(input));

        const nombreInput = contactoForm.querySelector('input[name="nombre"]');
        if (nombreInput) {
            nombreInput.addEventListener('keypress', function(e) {
                const key = String.fromCharCode(e.which);
                if (!/[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(key)) {
                    e.preventDefault();
                    mostrarError(this, 'Solo letras');
                    setTimeout(() => {
                        const error = this.parentNode.querySelector('.error-js');
                        if (error) error.remove();
                    }, 1000);
                }
            });

            nombreInput.addEventListener('input', function() {
                const original = this.value;
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                if (original !== this.value) {
                    mostrarError(this, 'Se eliminaron caracteres inválidos');
                    setTimeout(() => {
                        const error = this.parentNode.querySelector('.error-js');
                        if (error) error.remove();
                    }, 1500);
                }
                this.classList.remove('input-error');
            });
        }

        contactoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            const nombre = contactoForm.querySelector('input[name="nombre"]');
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/.test(nombre.value.trim())) {
                mostrarError(nombre, 'Mínimo 3 letras');
                isValid = false;
            }

            const telefono = contactoForm.querySelector('input[name="telefono"]');
            if (!/^\+?[0-9]{7,15}$/.test(telefono.value.trim())) {
                mostrarError(telefono, 'Teléfono inválido');
                isValid = false;
            }

            const email = contactoForm.querySelector('input[type="email"]');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                mostrarError(email, 'Email inválido');
                isValid = false;
            }

            const ciudad = contactoForm.querySelector('#ciudad');
            if (ciudad.value === '#') {
                mostrarError(ciudad, 'Selecciona una ciudad');
                isValid = false;
            }

            const motivo = contactoForm.querySelector('#motivo');
            if (motivo.value === '#') {
                mostrarError(motivo, 'Selecciona un motivo');
                isValid = false;
            }

            const mensaje = contactoForm.querySelector('input[name="mensaje"]');
            if (mensaje.value.trim().length < 10) {
                mostrarError(mensaje, 'Mínimo 10 caracteres');
                isValid = false;
            }

            if (isValid) {
                const datos = {
                    nombre: nombre.value.trim(),
                    telefono: telefono.value.trim(),
                    email: email.value.trim(),
                    ciudad: ciudad.value,
                    motivo: motivo.value,
                    mensaje: mensaje.value.trim()
                };

                fetch('/HotelixHub/codigo/api/apiContacto.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(datos)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        contactoForm.reset();
                        mostrarModalExito();
                    } else {
                        alert('Error: ' + (data.error || 'No se pudo enviar'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al enviar el formulario');
                });
            }
        });
    }

    // ==================================================================
    // MODAL DE ÉXITO PARA CONTACTO
    // ==================================================================
    const cerrarModal = document.getElementById('cerrar-modal-exito');
    if (cerrarModal) {
        cerrarModal.addEventListener('click', function() {
            document.getElementById('modal-exito').style.display = 'none';
        });
    }

    window.mostrarModalExito = function() {
        const modal = document.getElementById('modal-exito');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 15000);
    };

});
