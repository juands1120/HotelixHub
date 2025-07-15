<?php

require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../config/conexionbd.php';
;

// Verificar sesión y roles
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Verificar que el rol sea administrador 
if (!in_array($_SESSION['usuario']['usu_idrol'], [1])) {
    header('Location: ../views/login.php'); // O página de acceso denegado
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones</title> <!-- Título de la página -->
    <link rel="stylesheet" href="../assets/css/habitacion.css"> <!-- Enlace a hoja de estilos CSS -->
</head>
<body>
    <!-- CONTENEDOR PRINCIPAL DEL DASHBOARD -->
    <div class="dashboard-container">
        <!-- PANEL LATERAL - MENÚ DE NAVEGACIÓN -->
        <div class="barra-lateral">
            <!-- LOGO DEL HOTEL CON ENLACE AL HOME -->
            <div class="logo">
                <a href="dashAdmin.php"><img src="../assets/img/imgHabitacion/Copia de Logo Positivo.png" alt="Logo" width="200px" height="60px"></a>
            </div>
            <br><br>
            
            <!-- ELEMENTOS DEL MENÚ -->
            <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
            <a href="habitacion.php"><div class="menu-item">Habitaciones</div></a> <!-- Elemento activo actual -->

            <!-- MENÚ DESPLEGABLE DE USUARIOS -->
            <div class="usu">
                <button id="usuario">Usuarios</button>
                <div class="usu-contenido">
                    <a href="formEmpleados.php">Empleados</a>
                    <a href="formClientes.php">Clientes</a>
                </div>
            </div>
            
            <!-- ENLACE A MÓDULO DE PRODUCTOS -->
            <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>
        </div>

        <!-- CONTENIDO PRINCIPAL DE LA PÁGINA -->
        <div class="main-content">
            <!-- CABECERA DEL CONTENIDO -->
            <div class="header">
                <h1>Habitaciones</h1>
                
                <!-- FILTRO DE PISOS -->
                <select id="filtroPiso" name="filtroPiso">
                    <option value="todos">Ver todos los pisos</option>
                    <option value="2">Piso 2</option>
                    <option value="3">Piso 3</option>
                    <option value="4">Piso 4</option>
                    <option value="5">Piso 5</option>
                </select>
                
                <!-- BOTÓN PARA AGREGAR NUEVA HABITACIÓN -->
                <button id="habitacion" name="habitacion">Agregar Habitación</button>
            </div>
            
            <p>Gestión y estado de las habitaciones del hotel.</p> <!-- Descripción de la sección -->

            <!-- CONTENEDOR DONDE SE MOSTRARÁN LAS HABITACIONES -->
            <div id="habitacionesContainer" class="habitaciones-container"></div>
        </div>
    </div>

    <!-- MODAL PARA AGREGAR/EDITAR HABITACIONES -->
    <div id="modalHabitacion" class="modal-habitacion">
        <div class="modal-contenido">
            <!-- BOTÓN PARA CERRAR EL MODAL -->
            <span class="cerrar-modal" onclick="cerrarModal()">×</span>
            
            <!-- TÍTULO DEL MODAL (CAMBIARÁ ENTRE AGREGAR/EDITAR) -->
            <h2 id="modalTitulo">Agregar Nueva Habitación</h2>

            <!-- FORMULARIO PARA DATOS DE LA HABITACIÓN -->
            <form id="formHabitacion" method="post" enctype="multipart/form-data">
                <!-- NÚMERO DE HABITACIÓN -->
                <input type="text" id="numHabitacion" name="numero" placeholder="Número de habitación" required />
                
                <!-- SELECTOR DE TIPO DE HABITACIÓN -->
                <select id="tipoHabitacion" name="tipo" required>
                    <option value="">Tipo de habitación</option>
                    <option value="Sencilla">Sencilla</option>
                    <option value="Doble">Doble</option>
                    <option value="Triple">Triple</option>
                    <option value="Suite">Suite</option>
                </select>
                
                <!-- SELECTOR DE PISO -->
                <select id="piso" name="piso" required>
                    <option value="">Seleccione un piso</option>
                    <option value="2">Piso 2</option>
                    <option value="3">Piso 3</option>
                    <option value="4">Piso 4</option>
                    <option value="5">Piso 5</option>
                </select>
                
                <!-- PRECIO POR NOCHE -->
                <input type="number" id="precio" name="precio" placeholder="Precio por noche (COP)" required />
                
                <!-- SERVICIOS INCLUIDOS -->
                <label><strong>Servicios incluidos:</strong></label>
                <input type="text" id="servicios" name="servicios" placeholder="Escriba los servicios separados por coma" required />
                
                <br><br>
                
                <!-- SUBIDA DE IMAGEN DE LA HABITACIÓN -->
                <label><strong>Imagen de la habitacion:</strong></label>
                <input type="file" id="imagenHabitacion" name="imagen" accept="image/*" required />
                
                <!-- BOTÓN PARA GUARDAR -->
                <button type="submit" id="guardarBtn" name="guardarBtn">Guardar Habitación</button>
                
                <!-- BOTÓN PARA ELIMINAR (OCULTO INICIALMENTE, SE MUESTRA AL EDITAR) -->
                <button type="button" id="eliminarBtn" style="display:none; background:#e74c3c; color:white; border:none; padding:10px; border-radius:8px; margin-top:10px;">
                    Eliminar habitación
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN (OPERACIÓN EXITOSA) -->
    <div id="modalExito" class="modal-exito">
        <div class="modal-exito-contenido">
            <span class="cerrar-modal" onclick="cerrarModalExito()">x</span>
            <p>Habitación guardada exitosamente</p>
        </div>
    </div>

    <!-- INCLUSIÓN DEL ARCHIVO JAVASCRIPT -->
    <script src="../assets/JS/habitacion.js"></script>
</body>
</html>