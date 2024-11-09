DROP DATABASE IF EXISTS FEPLA_CRM;
CREATE DATABASE FEPLA_CRM CHARACTER SET utf8mb4;
USE FEPLA_CRM;

CREATE TABLE empresas (
	id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	cif char(9) UNIQUE, 
	nombre_comercial varchar(100) NOT NULL,
	nombre_empresa varchar(100),
	telefono_empresa varchar(50),
	nombre_contacto varchar(255),
	telefono_contacto varchar(50),
	email_contacto varchar(255),
	direccion varchar(255),
	interesado bit(1),
	cantidad_alumnos TINYINT,
	notas text
);

CREATE TABLE usuarios (
    id_tipo_usuario TINYINT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('root', 'admin', 'user')
);

CREATE TABLE profesores(
	dni_nie char(9) PRIMARY KEY,
	nombre varchar(100) not null,
	apellido1 varchar(100) not null,
	apellido2 varchar(100),
	telefono varchar(50),
	email varchar(255),
	tipo_usuario TINYINT,
	FOREIGN KEY (tipo_usuario) REFERENCES usuarios(id_tipo_usuario)
);

CREATE TABLE alumnos (
	dni_nie char(9) PRIMARY KEY,
	nombre VARCHAR(100) not null,
	apellido1 VARCHAR(100) not null,
	apellido2 VARCHAR(100),
	fecha_nacimiento DATE,
	telefono VARCHAR(50),
	email VARCHAR(255),
	direccion VARCHAR(255),
	vehiculo ENUM('Si','No'),
	curso CHAR(6),
	dni_tutor CHAR(9),
	id_empresa int UNSIGNED,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id),
	FOREIGN KEY (dni_tutor) REFERENCES profesores(dni_nie) ON DELETE SET NULL ON UPDATE CASCADE
);


/*Empresas*/
INSERT INTO empresas (nombre_comercial) VALUES
('GTT'),
('NTT Data'),
('Accenture'),
('EUIPO');

/*Usuarios*/
INSERT INTO usuarios (tipo)
VALUES 
('root'), 
('admin'), 
('user');

/*Profesores*/
INSERT INTO profesores (dni_nie, nombre, apellido1, apellido2, telefono, email)
VALUES 
('45678Y', 'David', 'Schmidt', 'Fisher', '618223876', 'davidschmidt@gmail.com'),
('56789Z', 'Zoe', 'Ramirez', 'Gea', '678516294', 'zoeramirez@gmail.com'),
('67891Q', 'Alfredo', 'Stiedemann', 'Morissette', '950896725', 'alfredomorissete@gmail.com');

/*Alumnos*/
INSERT INTO alumnos (dni_nie, nombre, apellido1, apellido2, fecha_nacimiento, telefono, email, direccion, vehiculo, curso)
VALUES 
('1234A', 'Juan', 'Vega', 'Saenz', '2000-08-08', '618253876', 'juanvegasaenz@gmail.com', 'C/Mercurio', 'Si', '2ºDAM'),
('23456S', 'Salvador', 'Perez', 'Sanchez', '2003-01-08', '950254837', 'salvadorperez@gmail.com', 'C/Real del barrio alto', 'No', '2ºDAM'),
('34567H', 'Helen', 'Koss', NULL, '1999-05-06', '628349590', 'Helenkoos@gmail.com', 'C/Estrella fugaz', 'Si', '2ºDAM'),
('45678J', 'María', 'García', 'Lopez', '2002-11-10', '612345678', 'mariagarcia@gmail.com', 'C/Luna Nueva', 'No', '2ºDAM'),
('56789K', 'Pablo', 'Hernández', 'Martínez', '2001-04-22', '613245678', 'pablohernandez@gmail.com', 'C/Arco Iris', 'Si', '2ºDAM'),
('67890L', 'Lucía', 'Ruiz', 'González', '2000-07-15', '614345678', 'luciaruiz@gmail.com', 'C/Norte', 'No', '2ºDAM'),
('78901M', 'Alejandro', 'Díaz', 'Torres', '2003-09-05', '615445678', 'alejandrodiaz@gmail.com', 'C/Campo Verde', 'Si', '2ºDAM'),
('89012N', 'Sara', 'Moreno', 'Pérez', '1999-03-18', '616545678', 'saramoreno@gmail.com', 'C/Sol Naciente', 'No', '2ºDAM'),
('90123O', 'Miguel', 'Santos', 'Romero', '2002-12-24', '617645678', 'miguelsantos@gmail.com', 'C/Océano', 'Si', '2ºDAM'),
('01234P', 'Carmen', 'Lopez', 'Ramos', '2001-05-30', '618745678', 'carmenlopez@gmail.com', 'C/Lago Azul', 'No', '2ºDAM'),
('12345Q', 'Raúl', 'Jiménez', 'Serrano', '2000-10-17', '619845678', 'rauljimenez@gmail.com', 'C/Cerro Alto', 'Si', '2ºDAM'),
('23456R', 'Ana', 'Martínez', 'Ortega', '2003-02-28', '620945678', 'anamartinez@gmail.com', 'C/Los Pinos', 'No', '2ºDAM'),
('34567S', 'David', 'Gómez', 'Gil', '1999-06-09', '621045678', 'davidgomez@gmail.com', 'C/Roca', 'Si', '2ºDAM'),
('45678T', 'Elena', 'Ramírez', 'Flores', '2001-08-13', '622145678', 'elenaramirez@gmail.com', 'C/Puesta del Sol', 'No', '2ºDAM'),
('56789U', 'Jorge', 'Castro', 'Ibáñez', '2002-07-21', '623245678', 'jorgecastro@gmail.com', 'C/Llano Verde', 'Si', '2ºDAM');