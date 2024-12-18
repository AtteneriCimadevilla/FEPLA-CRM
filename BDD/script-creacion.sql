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

CREATE TABLE tipos_usuario (
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
	FOREIGN KEY (tipo_usuario) REFERENCES tipos_usuario(tipo)
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
	clase enum('2º DAM', '1º DAM', '2º SMR', '1º SMR'),
	dni_tutor CHAR(9),
	id_empresa int UNSIGNED,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id),
	FOREIGN KEY (dni_tutor) REFERENCES profesores(dni_nie) ON UPDATE CASCADE
);

CREATE TABLE formaciones (
	dni_nie_alumno char(9),
	id_empresa int UNSIGNED,
	curso enum('24/25', '25/26', '26/27'),
	FOREIGN KEY (dni_nie_alumno) REFERENCES alumnos(dni_nie) ON UPDATE CASCADE,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id) ON UPDATE CASCADE,
	PRIMARY KEY (dni_nie_alumno, id_empresa)
);

CREATE TABLE registro (
	id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	dni_nie_profesor char(9),
	fecha date NOT NULL,
	tipo_actividad enum('Llamada', 'Email', 'Visita'),
	id_empresa int UNSIGNED,
	dni_nie_alumno char(9),
	texto_registro varchar(255),
	FOREIGN KEY (dni_nie_profesor) REFERENCES profesores(dni_nie) ON UPDATE CASCADE,
	FOREIGN KEY (id_empresa) REFERENCES empresas(id) ON UPDATE CASCADE,
	FOREIGN KEY (dni_nie_alumno) REFERENCES alumnos(dni_nie) ON UPDATE CASCADE
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
INSERT INTO tipos_usuario (tipo)
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
INSERT INTO alumnos (dni_nie, nombre, apellido1, apellido2, fecha_nacimiento, telefono, email, direccion, vehiculo, clase)
VALUES 
('12345678Q', 'Juan', 'Vega', 'Saenz', '2000-08-08', '618253876', 'juanvegasaenz@gmail.com', 'C/Mercurio', 'Si', '2º DAM'),
('23456789S', 'Salvador', 'Perez', 'Sanchez', '2003-01-08', '950254837', 'salvadorperez@gmail.com', 'C/Real del barrio alto', 'No', '2º DAM'),
('34567890H', 'Helen', 'Koss', NULL, '1999-05-06', '628349590', 'Helenkoos@gmail.com', 'C/Estrella fugaz', 'Si', '2º DAM'),
('45678901J', 'María', 'García', 'Lopez', '2002-11-10', '612345678', 'mariagarcia@gmail.com', 'C/Luna Nueva', 'No', '2º DAM'),
('56789012K', 'Pablo', 'Hernández', 'Martínez', '2001-04-22', '613245678', 'pablohernandez@gmail.com', 'C/Arco Iris', 'Si', '2º DAM'),
('67890123L', 'Lucía', 'Ruiz', 'González', '2000-07-15', '614345678', 'luciaruiz@gmail.com', 'C/Norte', 'No', '2º DAM'),
('78901234M', 'Alejandro', 'Díaz', 'Torres', '2003-09-05', '615445678', 'alejandrodiaz@gmail.com', 'C/Campo Verde', 'Si', '2º DAM'),
('89012345N', 'Sara', 'Moreno', 'Pérez', '1999-03-18', '616545678', 'saramoreno@gmail.com', 'C/Sol Naciente', 'No', '2º DAM'),
('90123456O', 'Miguel', 'Santos', 'Romero', '2002-12-24', '617645678', 'miguelsantos@gmail.com', 'C/Océano', 'Si', '2º DAM'),
('11234567P', 'Carmen', 'Lopez', 'Ramos', '2001-05-30', '618745678', 'carmenlopez@gmail.com', 'C/Lago Azul', 'No', '2º DAM'),
('Y1234567Q', 'Raúl', 'Jiménez', 'Serrano', '2000-10-17', '619845678', 'rauljimenez@gmail.com', 'C/Cerro Alto', 'Si', '2º DAM'),
('Z2345678R', 'Ana', 'Martínez', 'Ortega', '2003-02-28', '620945678', 'anamartinez@gmail.com', 'C/Los Pinos', 'No', '2º DAM'),
('X3456789S', 'David', 'Gómez', 'Gil', '1999-06-09', '621045678', 'davidgomez@gmail.com', 'C/Roca', 'Si', '2º DAM'),
('Y4567890T', 'Elena', 'Ramírez', 'Flores', '2001-08-13', '622145678', 'elenaramirez@gmail.com', 'C/Puesta del Sol', 'No', '2º DAM'),
('Z5678901U', 'Jorge', 'Castro', 'Ibáñez', '2002-07-21', '623245678', 'jorgecastro@gmail.com', 'C/Llano Verde', 'Si', '2º DAM');

/*Registro*/
INSERT INTO registro (dni_nie_profesor, fecha, tipo_actividad, id_empresa, dni_nie_alumno, texto_registro) VALUES
('45678123Y', '2024-12-01', 'Llamada', 1, null, 'Dicen que llame al nuevo teléfono entre las 9 y las 12 de la mañana.'),
('45678123Y', '2024-12-02', 'Llamada', 1, null, 'Me dicen que están interesados y que me confirmarán cuántos alumnos.'),
('45678123Y', '2024-12-03', 'Email', 1, '12345678Q', 'Quieren tres alumnos en prácticas.'),
('45678123Y', '2024-12-07', 'Visita', 1, null, 'Dos alumnos de 2º DAM y uno de 1º SMR.'),
('45678123Y', '2024-12-08', 'Llamada', 2, '12345678Q', 'Confirmaron que les envíe los CVs de los alumnos.'),
('45678123Y', '2024-12-09', 'Email', 3, null, 'Solicitan más información sobre la duración de las prácticas.'),
('45678123Y', '2024-12-10', 'Visita', 4, '12345678Q', 'Me piden dos alumnos para su oficina en el centro de la ciudad.'),
('45678123Y', '2024-12-11', 'Email', 5, '12345678Q', 'Han recibido la información y están en proceso de decidir.'),
('45678123Y', '2024-12-12', 'Llamada', 6, null, 'Me comentan que les interesa un alumno con conocimientos en marketing.'),
('45678123Y', '2024-12-13', 'Visita', 7, null, 'Requieren alumnos para cubrir horarios de tardes.'),
('45678123Y', '2024-12-14', 'Llamada', 8, null, 'Confirman que necesitan tres alumnos para prácticas en administración.'),
('45678123Y', '2024-12-15', 'Email', 9, '12345678Q', 'Me piden detalles sobre los requisitos de los alumnos.'),
('45678123Y', '2024-12-16', 'Visita', 10, null, 'Quieren cinco alumnos para prácticas en distintas áreas de la empresa.');

