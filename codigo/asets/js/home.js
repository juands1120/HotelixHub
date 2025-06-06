// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // ==================================================================
    // FUNCIONES UTILITARIAS
    // ==================================================================
    
    //Muestra un mensaje de error bajo un campo de formulario

    function mostrarError(input, mensaje) {
        // Eliminar errores previos del mismo campo
        const erroresAnteriores = input.parentNode.querySelectorAll('.error-js');
        erroresAnteriores.forEach(error => error.remove());
        
        // Crear y mostrar nuevo elemento de error
        const error = document.createElement('span');
        error.className = 'error error-js'; // Clases para estilizado CSS
        error.textContent = mensaje;
        input.parentNode.appendChild(error);
        input.classList.add('input-error'); // Resaltar campo inválido
    }

    //Limpia errores cuando el usuario comienza a escribir en un campo

    function limpiarError(input) {
        input.addEventListener('input', function() {
            // Remover estilos de error
            input.classList.remove('input-error');
            // Eliminar mensaje de error si existe
            const error = input.parentNode.querySelector('.error-js');
            if (error) error.remove();
        });
    }

    // ==================================================================
    // SECCIÓN 1: VALIDACIÓN FORMULARIO RÁPIDO DE RESERVAS (HOME)
    // ==================================================================
    const reservaForm = document.querySelector('#formulario form');
    
    if (reservaForm) {
        // Preparación: Agregar listeners para limpiar errores al escribir
        const inputsReserva = reservaForm.querySelectorAll('input, select');
        inputsReserva.forEach(input => limpiarError(input));
        
        // Validación al enviar el formulario
        reservaForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío por defecto
            let isValid = true; // Bandera de validación

            // --- Validación del campo EXPERIENCIA (reemplazo de ubicación) ---
            const experiencia = document.getElementById('experiencia');
            if (experiencia.value === '') {
                mostrarError(experiencia, 'Selecciona una experiencia para personalizar tu estancia');
                isValid = false;
            }

            // --- Validación de FECHAS ---
            const checkin = document.getElementById('checkin');
            const checkout = document.getElementById('checkout');
            const hoy = new Date().toISOString().split('T')[0]; // Fecha actual

            // Validar fecha de entrada
            if (checkin.value === '') {
                mostrarError(checkin, 'Ingresa fecha de check-in');
                isValid = false;
            } else if (checkin.value < hoy) {
                mostrarError(checkin, 'No puedes seleccionar fechas pasadas');
                isValid = false;
            }

            // Validar fecha de salida
            if (checkout.value === '') {
                mostrarError(checkout, 'Ingresa fecha de check-out');
                isValid = false;
            } else if (checkin.value && checkout.value && new Date(checkin.value) >= new Date(checkout.value)) {
                mostrarError(checkout, 'Check-out debe ser posterior al check-in');
                isValid = false;
            }

            // --- Validación de NÚMERO DE INVITADOS ---
            const invitados = document.getElementById('invitados');
            if (!invitados.value || parseInt(invitados.value) < 1) {
                mostrarError(invitados, 'Debe haber al menos 1 invitado');
                isValid = false;
            }

            // Enviar formulario solo si pasa todas las validaciones
            if (isValid) {
                this.submit();
            }
        });
    }

    // ==================================================================
    // SECCIÓN 2: VALIDACIÓN FORMULARIO DE CONTACTO
    // ==================================================================
    const contactoForm = document.querySelector('#contacto form');
    
    if (contactoForm) {
        // Preparación: Agregar listeners para limpiar errores al escribir
        const inputsContacto = contactoForm.querySelectorAll('input, select');
        inputsContacto.forEach(input => limpiarError(input));
        
        // --- VALIDACIÓN ESPECIAL PARA CAMPO NOMBRE (solo letras) ---
        const nombreInput = contactoForm.querySelector('input[name="nombre"]');
        if (nombreInput) {
            // 1. Bloquear teclas no permitidas mientras escribe
            nombreInput.addEventListener('keypress', function(e) {
                const key = String.fromCharCode(e.which);
                const permitidos = /[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/; // Regex: solo letras y espacios
                
                if (!permitidos.test(key)) {
                    e.preventDefault(); // Evitar que se muestre el carácter
                    // Mostrar error temporal (desaparece después de 1s)
                    mostrarError(this, 'Solo se permiten letras');
                    setTimeout(() => {
                        const error = this.parentNode.querySelector('.error-js');
                        if (error) error.remove();
                    }, 1000);
                }
            });

            // 2. Limpieza de texto pegado con contenido inválido
            nombreInput.addEventListener('input', function() {
                const valorOriginal = this.value;
                // Eliminar cualquier carácter que no sea letra o espacio
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                
                // Si se modificó el valor, mostrar advertencia
                if (valorOriginal !== this.value) {
                    mostrarError(this, 'Se eliminaron caracteres no permitidos');
                    setTimeout(() => {
                        const error = this.parentNode.querySelector('.error-js');
                        if (error) error.remove();
                    }, 1500);
                }
                
                this.classList.remove('input-error');
            });
        }

        // Validación al enviar formulario de contacto
        contactoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            // --- VALIDACIÓN CAMPO NOMBRE ---
            const nombre = contactoForm.querySelector('input[name="nombre"]');
            const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/; // Mínimo 3 letras
            
            if (nombre.value.trim() === '') {
                mostrarError(nombre, 'El nombre es requerido');
                isValid = false;
            } else if (!nombreRegex.test(nombre.value.trim())) {
                mostrarError(nombre, 'Mínimo 3 letras (solo se permiten letras y espacios)');
                isValid = false;
            }

            // --- VALIDACIÓN CAMPO TELÉFONO ---
            const telefono = contactoForm.querySelector('input[name="telefono"]');
            const telefonoRegex = /^\+?[0-9]{7,15}$/; // Formato internacional opcional
            
            if (telefono.value.trim() === '') {
                mostrarError(telefono, 'El teléfono es requerido');
                isValid = false;
            } else if (!telefonoRegex.test(telefono.value.trim())) {
                mostrarError(telefono, 'Teléfono inválido (solo números, 7-15 dígitos, puede incluir + al inicio)');
                isValid = false;
            }

            // --- VALIDACIÓN CAMPO EMAIL ---
            const email = contactoForm.querySelector('input[type="email"]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Formato email estándar
            
            if (email.value.trim() === '') {
                mostrarError(email, 'El email es requerido');
                isValid = false;
            } else if (!emailRegex.test(email.value.trim())) {
                mostrarError(email, 'Ingresa un email válido (ejemplo: usuario@dominio.com)');
                isValid = false;
            }

            // --- VALIDACIÓN CAMPO CIUDAD ---
            const ciudad = contactoForm.querySelector('#ciudad');
            if (ciudad.value === '#') {
                mostrarError(ciudad, 'Selecciona una ciudad válida');
                isValid = false;
            }

            // --- VALIDACIÓN CAMPO MOTIVO ---
            const motivo = contactoForm.querySelector('#motivo');
            if (motivo.value === '#') {
                mostrarError(motivo, 'Selecciona un motivo válido');
                isValid = false;
            }

            // --- VALIDACIÓN CAMPO MENSAJE ---
            const mensaje = contactoForm.querySelector('input[name="mensaje"]');
            if (mensaje.value.trim().length < 10) {
                mostrarError(mensaje, 'El mensaje debe tener al menos 10 caracteres');
                isValid = false;
            }

            // Enviar formulario solo si pasa todas las validaciones
            if (isValid) {
                this.submit();
            }
        });
    }
});

// Menú móvil
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }
    
    // Cierra el menú al hacer clic en un enlace
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    });
});