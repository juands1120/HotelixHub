<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - HotelixHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 90%;
            max-width: 1200px;
            height: 80vh;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            flex-wrap: wrap; /* Esto permite que los elementos se ajusten mejor */
        }

        .login-section {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            color: white;
            padding: 20px;
            background-image: url('imagenes/cuadro.jpg');
            background-size: cover;
            width: 100%;
            height: 100%;
            flex: 1 1 300px; /* Se ajusta el tamaño en pantallas pequeñas */
        }

        .register-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            text-align: center;
            padding: 40px;
            flex: 1 1 300px; /* Se ajusta el tamaño en pantallas pequeñas */
        }

        .header {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .logo {
            width: 250px;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 16px;
            background-color: #4A2884;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #3A206A;
        }

        .btn1 {
            background-color: #3A206A;
            width: 60%;
        }

        /* Estilos del Modal */
        .modal {
            display: none; /* Ocultar por defecto */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro */
            overflow: auto;
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            font-size: 18px;
            font-weight: bold;
        }

        .modal-body {
            font-size: 16px;
        }

        /* Media Queries para pantallas pequeñas */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .login-section, .register-section {
                width: 100%;
                padding: 20px;
            }

            .logo {
                width: 200px;
            }

            .btn1 {
                width: 80%;
            }

            form input, form select {
                font-size: 14px;
                padding: 8px;
            }

            button {
                font-size: 14px;
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .logo {
                width: 150px;
            }

            .btn1 {
                width: 100%;
            }

            form input, form select {
                font-size: 12px;
                padding: 6px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }

            .modal-content {
                width: 90%;
            }
        }

    </style>
</head>

<body>
    <div class="header">
        <img src="imagenes/Logo principal (1).png" alt="HotelixHub Logo" class="logo">
    </div>
    <div class="container">
        <div class="login-section">
            <div class="login-text">
                <h1>Bienvenido</h1>
                <p>Únete a nuestra comunidad y disfruta de nuestros servicios exclusivos.</p>
                <button id="iniciarSesionBtn" class="btn1">Iniciar sesión</button>
            </div>
        </div>
        <div class="register-section">
            <h2>Registro</h2>
            <form id="registro" method="POST">
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese su Nombre Completo">  
                <input type="text" id="apellido" name="apellido" placeholder="Ingrese su Apellido Completo"> 
                <select id="tipodocumento" name="tipodocumento">
                    <option value="">Seleccione Tipo de Documento</option>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="PA">Pasaporte</option> 
                </select>
                <input type="text" id="numeroDocumento" name="numeroDocumento" placeholder="Ingrese su Número de Documento">
                <input type="tel" id="numeroTelefono" name="numeroTelefono" placeholder="Ingrese su Número de Celular">
                <input type="email" id="correo" name="correo" placeholder="Ingrese su email">
                <input id="paisProcedencia" name="paisProcedencia" placeholder="Ingrese su País de Procedencia">
                <input type="password" id="contraseña" name="contraseña" placeholder="Ingrese su contraseña">
                <button type="submit" name="registro" class="btn" >Registrarse</button>
            </form>

            <?php
             include 'registrar.php';
            ?>

        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-header" id="modalHeader"></div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <script type="text/javascript">
        // Función para redirigir al login.html
        document.getElementById('iniciarSesionBtn').addEventListener('click', function () {
            window.location.href = 'login.html';  // Redirecciona al archivo login.html
        });

        // Función para mostrar el modal
        function showModal(headerText, bodyText) {
            document.getElementById('modalHeader').textContent = headerText;
            document.getElementById('modalBody').textContent = bodyText;
            document.getElementById('myModal').style.display = "block";
        }

        // Cerrar el modal
        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('myModal').style.display = "none";
        });

        // Cuando el usuario haga clic fuera del modal, se cerrará
        window.onclick = function (event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = "none";
            }
        };

        document.getElementById('registro').addEventListener('submit', function (e) {
            const nombre = document.getElementById('nombre').value.trim();
            const apellido = document.getElementById('apellido').value.trim();
            const tipodocumento = document.getElementById('tipodocumento').value;
            const numeroDocumento = document.getElementById('numeroDocumento').value.trim();
            const numeroTelefono = document.getElementById('numeroTelefono').value.trim();
            const correo = document.getElementById('correo').value.trim();
            const paisProcedencia = document.getElementById('paisProcedencia').value.trim();
            const contraseña = document.getElementById('contraseña').value.trim();


            if (!nombre || !apellido || !tipodocumento || !numeroDocumento || !numeroTelefono || !correo || !paisProcedencia || !contraseña) {
                e.preventDefault(); 
                showModal("Error", "Por favor, completa todos los campos antes de registrarte.");
            }
        });

    </script>
</body>
</html>
