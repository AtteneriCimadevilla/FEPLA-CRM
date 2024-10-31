CREATE DATABASE IF NOT EXISTS FEPLA_CRM CHARACTER SET utf8mb4;
USE FEPLA_CRM;

CREATE TABLE empresas (
	id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	nombreComercial varchar(50) NOT NULL,
	nombreContacto varchar(50) NOT NULL
);

CREATE TABLE alumnos (
dni char(9) PRIMARY KEY,
nombre varchar(50),
id_empresa int UNSIGNED NOT NULL,
FOREIGN KEY (id_empresa) REFERENCES empresas(id)
);