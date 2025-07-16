
<?php
require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../config/conexionbd.php';

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
    <title>Administrar Productos</title>
    <link rel="stylesheet" href="/HotelixHub/codigo/assets/css/productosAdmin.css">
</head>
<body>
    <div class="barra-lateral">
        <!-- LOGO DEL HOTEL CON ENLACE AL HOME -->
        <div class="logo">
            <a href="dashAdmin.php"><img src="../assets/img/imgHabitacion/Copia de Logo Positivo.png" alt="Logo" width="200px" height="60px"></a>
        </div>
        <br><br>
        <div></div>
        
        <!-- ELEMENTOS DEL MENÚ -->
        <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
        <a href="habitacion.php"><div class="menu-item">Habitaciones</div></a> <!-- Elemento activo actual -->

        <!-- MENÚ DESPLEGABLE DE USUARIOS -->
        <div class="usu">
            <button id="usuario">Usuarios</button>
            <div class="usu-contenido">
                <a href="dashEmpleado.php">Empleados</a>
                <a href="dashClientes.php">Clientes</a>
            </div>
        </div>
        
        <!-- ENLACE A MÓDULO DE PRODUCTOS -->
        <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>

        <a href="../controller/logout.php"><button class="menu-item">Cerrar Sesion</button></a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Gestión de Productos</h1>
            <div>
                <button id="btnAgregarCategoria" class="agregar-btn">Agregar Categoría</button>
                <button id="btnAgregar" class="agregar-btn"> Agregar Producto</button>
                <button id="exportPDF" class="agregar-btn">Exportar PDF</button>
            </div>
        </div>

        <div class="content">
            <div id="filtrosCategorias" style="margin-bottom:15px;"></div>

            <!-- Aquí antes estaba tu tabla -->
            <!-- Ahora es un div que contendrá las tarjetas -->
            <div id="tablaProductos" class="tabla-productos"></div>
        </div>
    </div>

    <!-- Modal Producto -->
    <div id="modalProducto" class="modal">
        <div class="modal-content">
            <span class="close" id="cerrarModal">&times;</span>
            <h2 id="tituloModal">Nuevo Producto</h2>
            <form id="formProducto" enctype="multipart/form-data">
                <input type="hidden" id="productoId" name="id">
                <input type="hidden" id="imagenActual" name="imagen_actual" accept=".jpg,.jpeg,.png">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" id="precio" name="precio" class="form-input" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-textarea" required></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" id="stock" name="stock" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría</label>
                        <select id="categoria" name="id_categoria" class="form-input" required></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Imagen</label>
                        <input type="file" id="imagen" name="imagen" class="form-input" accept=".jpg,.jpeg,.png">
                    </div>
                </div>
                <button type="submit" class="agregar-btn">Guardar</button>
            </form>
        </div>
    </div>

    <!-- Modal Categoría -->
    <div id="modalCategoria" class="modal">
        <div class="modal-content">
            <span class="close" id="cerrarModalCategoria">&times;</span>
            <h2>Administrar Categorías</h2>
            <form id="formCategoria">
                <input type="hidden" id="categoriaId">
                <input type="text" id="nombreCategoria" class="form-input" placeholder="Nombre categoría" required>
                <button type="submit" class="agregar-btn">Guardar</button>
            </form>
            <div id="listaCategorias" style="margin-top:20px;"></div>
        </div>
    </div>

    <script src="/HotelixHub/codigo/assets/js/productosAdmin.js"></script>
    
    <div id="toast-container"></div>
    
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <p id="confirm-text">¿Estás seguro?</p>
            <div style="display:flex; justify-content: flex-end; gap:10px; margin-top:20px;">
                <button id="confirm-yes" class="agregar-btn">Sí</button>
                <button id="confirm-no" class="remove-btn">No</button>
            </div>
        </div>
    </div>

</body>
</html>
