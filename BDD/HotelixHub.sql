CREATE TABLE `hotelixhub`.`rol` (
  id_rol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  rol_nombre VARCHAR(50) NOT NULL
);


-- Tabla: Usuario
CREATE TABLE IF NOT EXISTS usuarios (
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
  estado ENUM("en turno", "fuera de turno", "vacaciones")
  direccion varchar (100) NOT NULL,
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
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
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


CREATE TABLE categoria (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  descripcion varchar (100) not null
);


CREATE TABLE producto (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion varchar (100) not null,
  precio_unitario DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL,
  imagen VARCHAR(255),
  id_categoria INT,
  FOREIGN KEY (id_categoria) REFERENCES categoria (id_categoria)
);

CREATE TABLE venta (
  id_venta INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_anulacion TIMESTAMP NULL,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('Activa', 'Anulada') DEFAULT 'Activa',
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE detalle_venta (
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_venta) REFERENCES venta(id_venta),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);





/* codigo de ejemplo para bdd orientada a objetos*/

CREATE TABLE fecha_evento (
  id_fecha INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  tipo_fecha ENUM('reserva', 'entrada', 'salida', 'venta', 'contacto') NOT NULL,
  descripcion TEXT
);


CREATE TABLE reserva (
  id_reserva INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_habitacion INT NOT NULL,
  id_fecha_entrada INT,
  id_fecha_salida INT,
  id_fecha_reserva INT,
  num_huespedes INT NOT NULL,
  servicios_adicionales TEXT,
  precio_total DECIMAL(10,2) NOT NULL,
  estado ENUM('Pendiente', 'Confirmada', 'Cancelada') DEFAULT 'Pendiente',
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
  FOREIGN KEY (id_habitacion) REFERENCES habitacion(id_habitacion),
  FOREIGN KEY (id_fecha_entrada) REFERENCES fecha_evento(id_fecha),
  FOREIGN KEY (id_fecha_salida) REFERENCES fecha_evento(id_fecha),
  FOREIGN KEY (id_fecha_reserva) REFERENCES fecha_evento(id_fecha)
);

