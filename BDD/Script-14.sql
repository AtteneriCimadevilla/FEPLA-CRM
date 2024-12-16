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
	interesado tinyint(1),
	cantidad_alumnos TINYINT UNSIGNED,
	notas text
);

CREATE TABLE usuarios (
    tipo ENUM('root', 'admin', 'user') PRIMARY KEY
);

CREATE TABLE profesores(
	dni_nie char(9) PRIMARY KEY,
	contrasenya varchar(255),
	nombre varchar(100) not null,
	apellido1 varchar(100) not null,
	apellido2 varchar(100),
	telefono varchar(50),
	email varchar(255),
	tipo_usuario ENUM('root', 'admin', 'user') DEFAULT 'user',
	FOREIGN KEY (tipo_usuario) REFERENCES usuarios(tipo)
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
	clase enum('2º DAM', '1º DAM', '2º SMR', '1º SMR') 
	dni_tutor CHAR(9) NOT NULL,
	id_empresa int UNSIGNED,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id),
	FOREIGN KEY (dni_tutor) REFERENCES profesores(dni_nie) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE formaciones (
	dni_nie_alumno char(9),
	id_empresa int UNSIGNED,
	curso enum('24/25', '25/26', '26/27'),
	FOREIGN KEY (dni_nie_alumno) REFERENCES alumnos(dni_nie) ON UPDATE CASCADE ON DELETE SET NULL,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id) ON UPDATE CASCADE ON DELETE SET NULL,
	PRIMARY KEY (dni_nie_alumno, id_empresa)
);

CREATE TABLE registro (
	fecha date NOT NULL,
	id_empresa int UNSIGNED,
	dni_nie_alumno char(9),
	texto_registro varchar(255),
	FOREIGN KEY (id_empresa) REFERENCES empresas(id) ON UPDATE CASCADE ON DELETE SET NULL,
	FOREIGN KEY (dni_nie_alumno) REFERENCES alumnos(dni_nie) ON UPDATE CASCADE ON DELETE SET NULL,
	PRIMARY KEY (fecha, id_empresa, dni_nie_alumno)
);

/*Empresas*/
INSERT INTO empresas (cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, telefono_contacto, email_contacto, direccion, interesado, cantidad_alumnos, notas) VALUES
('A1234567A', 'Innova Tech', 'Innova Solutions S.L.', '965123456', 'María López', '617234567', 'maria.lopez@innovatech.com', 'Calle Innovación 5, Alicante', 1, 3, 'Interesado en programadores web.'),
('B2345678B', 'Green Solutions', 'Green Energy S.A.', '966234567', 'Carlos Martín', '618345678', 'carlos.martin@greensolutions.com', 'Av. Ecología 10, Alicante', 0, 2, 'Posible interés en alumnos de marketing digital.'),
('C3456789C', 'GlobalSoft', 'Global Software Corp.', '964345678', 'Laura García', '619456789', 'laura.garcia@globalsoft.com', 'Calle Tecnología 20, Alicante', 1, 4, 'Solicita alumnos para prácticas en desarrollo de aplicaciones.'),
('D4567890D', 'TechWave', 'Tech Wave S.L.', '963456789', 'Juan Fernández', '620567890', 'juan.fernandez@techwave.com', 'Av. Del Progreso 15, Alicante', 1, 1, 'Interés en perfiles de diseño UX/UI.'),
('E5678901E', 'DataWorks', 'DataWorks Ltd.', '962567890', 'Elena Morales', '621678901', 'elena.morales@dataworks.com', 'Calle Análisis 12, Alicante', 0, 2, 'Poco interés en prácticas, pero abierto a colaboración.'),
('F6789012F', 'SmartMedia', 'Smart Media Group', '961678901', 'Pablo Sánchez', '622789012', 'pablo.sanchez@smartmedia.com', 'Calle Multimedia 8, Alicante', 1, 5, 'Muy interesados en alumnos para prácticas de edición multimedia.'),
('G7890123G', 'EcoEnergy', 'EcoEnergy España S.A.', '960789012', 'Sara Ruiz', '623890123', 'sara.ruiz@ecoenergy.com', 'Av. Sustentable 22, Alicante', 1, 2, 'Buscan perfiles de ingenieros ambientales.'),
('H8901234H', 'AlphaNet', 'AlphaNet Corp.', '965890123', 'David Ortiz', '624901234', 'david.ortiz@alphanet.com', 'Calle Red 30, Alicante', 0, 3, 'Interés en perfiles de soporte técnico.'),
('I9012345I', 'Cloudify', 'Cloudify Solutions', '963012345', 'Nuria Torres', '625012345', 'nuria.torres@cloudify.com', 'Calle Nube 18, Alicante', 1, 2, 'Desean alumnos para desarrollo en la nube.'),
('J0123456J', 'BlueOcean', 'Blue Ocean Consulting', '964123456', 'Pedro Giménez', '626123456', 'pedro.gimenez@blueocean.com', 'Av. Del Mar 45, Alicante', 0, 1, 'Poco interés en prácticas, pero dispuestos a colaborar en el futuro.');

/*Usuarios*/
INSERT INTO usuarios (tipo)
VALUES 
('root'), 
('admin'), 
('user');

/*Profesores*/
INSERT INTO profesores (dni_nie, contrasenya, nombre, apellido1, apellido2, telefono, email)
VALUES 
('45678123Y', '12345678', 'David', 'Schmidt', 'Fisher', '618223876', 'davidschmidt@gmail.com'),
('56789123Z', '12345678', 'Zoe', 'Ramirez', 'Gea', '678516294', 'zoeramirez@gmail.com'),
('67891123Q', '12345678', 'Alfredo', 'Stiedemann', 'Morissette', '950896725', 'alfredomorissete@gmail.com');

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