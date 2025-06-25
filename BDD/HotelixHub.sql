CREATE TABLE `hotelixhub`.`rol` (
  id_rol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  rol_nombre VARCHAR(50) NOT NULL
);


CREATE TABLE `hotelixhub`.`usuarios` (
  id_usuario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  usu_idrol INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  tipoDocumento ENUM('CC', 'PA', 'TI', 'CE') NOT NULL,
  numeroDocumento VARCHAR(100) NOT NULL UNIQUE,
  numeroTelefono VARCHAR(20) NOT NULL UNIQUE,
  paisProcedencia VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  reset_token VARCHAR(255) DEFAULT NULL,
  token_expires DATETIME DEFAULT NULL,
  FOREIGN KEY (usu_idrol) REFERENCES rol(id_rol)
);

INSERT INTO rol(id_rol, rol_nombre) VALUES ('1','administrador') , ('2','cliente');

CREATE TABLE `habitacion` (
  `id_habitacion` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `tipoHabitacion` ENUM('Sencilla', 'Doble', 'Triple', 'Suite'),
  `piso` ENUM('2', '3', '4', '5'),
  `precio` INT NOT NULL,
  `serviciosIncluidos` VARCHAR(255) NOT NULL,
  `estado` ENUM('Disponible', 'Ocupada', 'Mantenimiento') DEFAULT 'Disponible',
  `imagen` VARCHAR(255) NOT NULL
);


CREATE TABLE `hotelixhub`.`reserva` (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_habitacion INT NOT NULL,
    fecha_entrada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    num_huespedes INT NOT NULL,
    servicios_adicionales TEXT,
    precio_total DECIMAL(10,2) NOT NULL,
    estado ENUM('Pendiente', 'Confirmada', 'Cancelada') DEFAULT 'Pendiente',
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_habitacion) REFERENCES habitacion(id_habitacion)
);

CREATE TABLE contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    ciudad VARCHAR(50),
    motivo VARCHAR(50),
    mensaje TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT DEFAULT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
);
