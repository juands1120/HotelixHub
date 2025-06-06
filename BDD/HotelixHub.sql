
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


