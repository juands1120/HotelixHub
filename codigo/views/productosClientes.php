<?php
require_once __DIR__ . '/../services/sessionManager.php';


if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}
?>
    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estructura con Barra Lateral</title>
    <link rel="stylesheet" href="/HotelixHub/codigo/assets/css/productosClientes.css">
</head>

<body>
    <div class="contenido-principal">

        <!--BARRA LATERAL-->    
        <div class="barra-lateral">
            <div class="logo">
                <a href="dashCliente.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="Logo" width="200px" height="60px"></a>
            </div>
            <a href="dashCliente.php"><button class="menu-btn" id="btnProductos">Inicio</button></a>
            <a href="reservas.php"><button class="menu-btn" id="btnProductos">Reservas</button></a>
            <a href="productosClientes.php"><button class="menu-btn" id="btnProductos">Productos</button></a>
            <a href="../controller/logout.php"><button class="cerrar-s">Cerrar Sesion</button></a>
        </div>


        <!--INFORMACION DEL CONTENIDO PRINCIPAL-->
        <section class="informacion-contenido-principal">
            <!-- Barra de búsqueda -->
            <div class="barra-busqueda">
                <button class="toggle-menu" onclick="document.querySelector('.barra-lateral').classList.toggle('abierta')">☰</button>
                <input type="text" placeholder="Buscar producto..." id="input-busqueda">
                <button id="btn-buscar">
                    <img src="../assets/img/imgProductosCliente/lupa busqueda.png" alt="Buscar" width="25px" height="25px">
                </button>
            </div>

            <!-- Carrusel de Anuncios-->
            <div class="carrusel-anuncios" id="carrusel"></div>

            <h2 class="titulo-categoria">Productos</h2>
            <!-- Categorías de productos -->
            <div class="categorias"></div>


            <!--PRODCUTOS-->
            <div class="seccion-productos">
                <button class="btn-scroll" id="btn-izq">←</button>
                <div class="contenedor-productos"></div>
                <button class="btn-scroll" id="btn-der">→</button>
            </div>
        </section>
        
        
        <!--INFORMACION DEL USUARIO-->
        <div class="info-usuario">
            <?php
                $nombreCompleto = $_SESSION['usuario']['nombre'];
                $emailUsuario = $_SESSION['usuario']['email'];
                $iniciales = strtoupper(substr($nombreCompleto, 0, 1)) . strtoupper(substr(strrchr($nombreCompleto, ' '), 1, 1));
                ?>
                <div class="iniciales"><?= $iniciales ?></div>
                <div class="nombre-email">
                    <h5><?= htmlspecialchars($nombreCompleto) ?></h5>
                    <p><?= htmlspecialchars($emailUsuario) ?></p>
                </div>
        </div>

        <!--INFORMACION DEL CARRITO-->
        <div class="carrito">
            <h3>Carrito</h3>
            <div class="Orden">
                <h3>Orden</h3>
                <div class="lista-carrito">
                </div>
            </div>
            <div class="resumen-compra" id="resumen-compra">
                <p>Subtotal: <span id="subtotal">$0</span></p>
                <p>IVA (19%): <span id="iva">$0</span></p>
                <p>Total: <span id="total">$0</span></p>
            </div>
            <button class="btnCompra">Realizar compra</button>
        </div>


    <!--AQUI CIERRA CONTENIDO PRINCIPAL-->
    </div>

    <!-- MODAL DE COMPRA -->
    <div class="modal-compra" id="modalCompra">
    <div class="contenido-modal">
        <h2>Finalizar Compra</h2>

        <!-- Resumen dinámico -->
        <div id="resumenCompraModal" style="margin-bottom: 20px;"></div>

        <form id="formPago" novalidate>
        <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" required pattern="^[A-Za-zÁÉÍÓÚÑáéíóúñ ]{3,}$" placeholder="Ej. Laura Martínez">
        </div>

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required placeholder="Ej. laura@email.com">
        </div>

        <div class="form-group">
            <label for="metodo">Método de pago</label>
            <select id="metodo" name="metodo" required>
            <option value="">Selecciona un método</option>
            <option value="credito">Tarjeta de crédito</option>
            <option value="debito">Tarjeta de débito</option>
            <option value="efectivo">Pago en efectivo</option>
            </select>
        </div>

        <div class="form-group" id="grupo-tarjeta">
            <label for="tarjeta">Número de tarjeta</label>
            <input type="text" id="tarjeta" name="tarjeta" maxlength="16" minlength="13" pattern="\d{13,16}" placeholder="Ej. 1234567890123">
        </div>


        <div class="form-group botones">
            <button type="submit" id="btnConfirmarCompra">Confirmar Compra</button>
            <button type="button" onclick="cerrarModalCompra()">Cancelar</button>
        </div>
        </form>
    </div>
    </div>


    <!--FUNCIONES Y VALIDACIONES JAVA SCRIPT-->
    <script src="/HotelixHub/codigo/assets/js/productosClientes.js"></script>
</body>
</html>
