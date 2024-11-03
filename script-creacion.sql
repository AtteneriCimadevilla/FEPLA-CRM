CREATE DATABASE IF NOT EXISTS FEPLA_CRM CHARACTER SET utf8mb4;
USE FEPLA_CRM;

CREATE TABLE empresas (
	id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	cif char(9) UNIQUE, 
	nombreComercial varchar(100) NOT NULL,
	nombreEmpresa varchar(100),
	telefonoEmpresa varchar(50),
	nombreContacto varchar(255),
	telefonoContacto varchar(50),
	emailContacto varchar(255),
	direccion varchar(255),
	interesado bit(1),
	cantidadAlumnos TINYINT,
	notas text
);

CREATE TABLE profesores(
	dni_nie char(9) PRIMARY KEY,
	nombre varchar(100) not null,
	apellido1 varchar(100) not null,
	apellido2 varchar(100),
	telefono varchar(50),
	email varchar(255),
	tipoUsuario TINYINT,
	FOREIGN KEY (tipoUsuario) REFERENCES usuarios(id_usuario)
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
	dni_tutor CHAR(9),
	id_empresa int UNSIGNED,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id)
	FOREIGN KEY (dni_tutor) REFERENCES profesores(dni_profesor) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE usuarios (
    id_usuario TINYINT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('root', 'admin', 'user'),
);

/*Empresas*/
INSERT INTO empresas (nombreComercial) VALUES
('GTT'),
('NTT Data'),
('Accenture'),
('EUIPO');

/*Profesores*/
INSERT INTO profesores (dni_profesor, nombre, apellido1, apellido2, telefono, email)
VALUES 
('45678Y', 'David', 'Schmidt', 'Fisher', '618223876', 'davidschmidt@gmail.com'),
('56789Z', 'Zoe', 'Ramirez', 'Gea', '678516294', 'zoeramirez@gmail.com'),
('67891Q', 'Alfredo', 'Stiedemann', 'Morissette', '950896725', 'alfredomorissete@gmail.com');

/*Alumnos*/
INSERT INTO alumnos (dni_alumno, nombre, apellido1, apellido2, fecha_nacimiento, telefono, email, direccion, vehiculo, dni_tutor)
VALUES 
('1234A', 'Juan', 'Vega', 'Saenz', '2000-08-08', '618253876', 'juanvegasaenz@gmail.com', 'C/Mercurio', 'Si', '45678Y'),
('23456S', 'Salvador', 'Perez', 'Sanchez', '2003-01-08', '950254837', 'salvadorperez@gmail.com', 'C/Real del barrio alto', 'No', '56789Z'),
('34567H', 'Helen', 'Koss', NULL, '1999-05-06', '628349590', 'Helenkoos@gmail.com', 'C/Estrella fugaz', 'Si', '67891Q');


/*Usuarios*/
INSERT INTO usuarios (dni, tipo)
VALUES 
('root'), 
('admin'), 
('user');