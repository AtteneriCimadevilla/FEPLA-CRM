DROP DATABASE IF EXISTS instituto;
CREATE DATABASE instituto CHARACTER SET utf8mb4;
USE instituto;

CREATE TABLE profesor(
dni_profesor varchar(100) primary key,
nombre varchar(100) not null,
apellido1 varchar(100) not null,
apellido2 varchar(100),
telefono varchar(9),
email varchar(100)
);

CREATE TABLE alumno (
dni_alumno VARCHAR(100) primary key,
nombre VARCHAR(100) not null,
apellido1 VARCHAR(100) not null,
apellido2 VARCHAR(100),
fecha_nacimiento DATE not null,
telefono VARCHAR(9),
email VARCHAR(100) not null,
direccion VARCHAR(100),
vehiculo ENUM('Si','No') not null,
dni_tutor VARCHAR(100),
FOREIGN KEY (dni_tutor) REFERENCES profesor(dni_profesor) ON DELETE SET NULL ON UPDATE CASCADE
);

create table usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(100),
    tipo ENUM('root', 'admin', 'user'),
    foreign key (dni) references profesor(dni_profesor) on delete cascade on update cascade
);

USE instituto;

/*Profesor*/
INSERT INTO profesor (dni_profesor, nombre, apellido1, apellido2, telefono, email)
VALUES 
('45678Y', 'David', 'Schmidt', 'Fisher', '618223876', 'davidschmidt@gmail.com'),
('56789Z', 'Zoe', 'Ramirez', 'Gea', '678516294', 'zoeramirez@gmail.com'),
('67891Q', 'Alfredo', 'Stiedemann', 'Morissette', '950896725', 'alfredomorissete@gmail.com');

/*Alumnos*/
INSERT INTO alumno (dni_alumno, nombre, apellido1, apellido2, fecha_nacimiento, telefono, email, direccion, vehiculo, dni_tutor)
VALUES 
('1234A', 'Juan', 'Vega', 'Saenz', '2000-08-08', '618253876', 'juanvegasaenz@gmail.com', 'C/Mercurio', 'Si', '45678Y'),
('23456S', 'Salvador', 'Perez', 'Sanchez', '2003-01-08', '950254837', 'salvadorperez@gmail.com', 'C/Real del barrio alto', 'No', '56789Z'),
('34567H', 'Helen', 'Koss', NULL, '1999-05-06', '628349590', 'Helenkoos@gmail.com', 'C/Estrella fugaz', 'Si', '67891Q');


/*Usuario*/
INSERT INTO usuario (dni, tipo)
VALUES 
('45678Y', 'root'), 
('56789Z', 'admin'), 
('67891Q', 'user');
