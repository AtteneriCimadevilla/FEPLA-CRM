DROP DATABASE IF EXISTS FEPLA_CRM;

CREATE DATABASE FEPLA_CRM CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE FEPLA_CRM;

-- Tabla de tipos de usuario
CREATE TABLE tipos_usuario (
    tipo ENUM('root', 'admin', 'user') PRIMARY KEY
);

-- Tabla de profesores
CREATE TABLE profesores (
    dni_nie CHAR(9) PRIMARY KEY,
    contrasenya VARCHAR(255),
    nombre VARCHAR(100) NOT NULL,
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100),
    telefono VARCHAR(50),
    email VARCHAR(255),
    tipo_usuario ENUM('root', 'admin', 'user') DEFAULT 'user',
    FOREIGN KEY (tipo_usuario) REFERENCES tipos_usuario (tipo)
);

-- Tabla de empresas
CREATE TABLE empresas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nif CHAR(9) UNIQUE,
    nombre_comercial VARCHAR(100) NOT NULL,
    nombre_empresa VARCHAR(100),
    telefono_empresa VARCHAR(50),
    nombre_contacto VARCHAR(255),
    telefono_contacto VARCHAR(50),
    email_contacto VARCHAR(255),
    direccion VARCHAR(255),
    cp CHAR(5),
    web VARCHAR(255),
    email_empresa VARCHAR(255),
    interesado TINYINT(1),
    cantidad_alumnos TINYINT UNSIGNED,
    descripcion TEXT,
    actividad_principal VARCHAR(255),
    otras_actividades TEXT,
    dni_profesor CHAR(9),
    FOREIGN KEY (dni_profesor) REFERENCES profesores (dni_nie)
);

-- Tabla de catálogo de ciclos
CREATE TABLE catalogo_ciclos (
    id_ciclo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255),
    alias CHAR(9) UNIQUE
);

-- Tabla de grupos
CREATE TABLE grupos (
    id_grupo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    curso ENUM('24/25', '25/26', '26/27') DEFAULT '24/25',
    id_ciclo INT UNSIGNED,
    dni_tutor CHAR(9),
    alias_grupo CHAR(2),
    FOREIGN KEY (id_ciclo) REFERENCES catalogo_ciclos (id_ciclo) ON UPDATE CASCADE,
    FOREIGN KEY (dni_tutor) REFERENCES profesores (dni_nie) ON UPDATE CASCADE
);

-- Tabla de alumnos
CREATE TABLE alumnos (
    dni_nie CHAR(9) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100),
    fecha_nacimiento DATE,
    telefono VARCHAR(50),
    email VARCHAR(255),
    direccion VARCHAR(255),
    vehiculo ENUM('Si', 'No') DEFAULT 'No',
    id_grupo INT UNSIGNED,
    id_empresa INT UNSIGNED,
    FOREIGN KEY (id_grupo) REFERENCES grupos (id_grupo),
    FOREIGN KEY (id_empresa) REFERENCES empresas (id)
);

-- Tabla de formaciones
CREATE TABLE formaciones (
    dni_nie_alumno CHAR(9),
    id_empresa INT UNSIGNED,
    curso ENUM('24/25', '25/26', '26/27') DEFAULT '24/25',
    FOREIGN KEY (dni_nie_alumno) REFERENCES alumnos (dni_nie) ON UPDATE CASCADE,
    FOREIGN KEY (id_empresa) REFERENCES empresas (id) ON UPDATE CASCADE,
    PRIMARY KEY (dni_nie_alumno, id_empresa)
);

-- Tabla de registro
CREATE TABLE registro (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dni_nie_profesor CHAR(9),
    fecha DATE NOT NULL,
    tipo_actividad ENUM('Llamada', 'Email', 'Visita'),
    id_empresa INT UNSIGNED,
    dni_nie_alumno CHAR(9),
    texto_registro VARCHAR(255),
    FOREIGN KEY (dni_nie_profesor) REFERENCES profesores (dni_nie) ON UPDATE CASCADE,
    FOREIGN KEY (id_empresa) REFERENCES empresas (id) ON UPDATE CASCADE,
    FOREIGN KEY (dni_nie_alumno) REFERENCES alumnos (dni_nie) ON UPDATE CASCADE
);

-- Inserción de datos

-- Tipos de usuario
INSERT INTO
    tipos_usuario (tipo)
VALUES ('root'),
    ('admin'),
    ('user');

-- Profesores
INSERT INTO
    profesores (
        dni_nie,
        contrasenya,
        nombre,
        apellido1,
        apellido2,
        telefono,
        email,
        tipo_usuario
    )
VALUES (
        '45678123Y',
        '12345678',
        'David',
        'Perez',
        'Pastor',
        '5464474',
        'd.perezpastor@edu.gva.es',
        'user'
    ),
    (
        '56789123Z',
        '12345678',
        'Vicente Jesús',
        'Santonja',
        'Ivorra',
        '69820058',
        'vj.santonjaivorra@edu.gva.es',
        'admin'
    ),
    (
        '67891123Q',
        '12345678',
        'Luis',
        'Alemañ',
        'Fillol',
        '89626023',
        'l.alemanfillol@edu.gva.es',
        'user'
    ),
    (
        '89246468G',
        '12345678',
        'Virginia Alejandra',
        'Ayala',
        'Munar',
        '06502659',
        'va.ayalamunar@edu.gva.es',
        'user'
    ),
    (
        '86852171L',
        '12345678',
        'Rafael Emilio',
        'Reus',
        'López',
        '9646208',
        're.reuslopez@edu.gva.es',
        'user'
    ),
    (
        '58655450J',
        '12345678',
        'Jose David',
        'Balibrea',
        'Sanchez',
        '6464138',
        'jd.balibrea@edu.gva.es',
        'user'
    ),
    (
        '28464687H',
        '12345678',
        'Jose Antonio',
        'Sanchez',
        'Ortiz',
        '89687525',
        'ja.sanchezortiz@edu.gva.es',
        'user'
    ),
    (
        '86846171L',
        '12345678',
        'Dario',
        'Pascual',
        'Antón',
        '7858560',
        'd.pascualanton@edu.gva.es',
        'user'
    );

-- Empresas
INSERT INTO
    empresas (
        nif,
        nombre_comercial,
        nombre_empresa,
        telefono_empresa,
        nombre_contacto,
        telefono_contacto,
        email_contacto,
        direccion,
        cp,
        web,
        email_empresa,
        interesado,
        cantidad_alumnos,
        descripcion,
        actividad_principal,
        otras_actividades,
        dni_profesor
    )
VALUES (
        'D4567890D',
        'SoftDev',
        'Software Developers S.L.',
        '968456789',
        'Ana Martínez',
        '620567890',
        'ana.martinez@softdev.com',
        'Calle Tecnología 12, Alicante',
        '03004',
        'www.softdev.com',
        'contacto@softdev.com',
        1,
        4,
        'Desarrollo de software a medida para empresas.',
        'Desarrollo de Software',
        'Consultoría TI, Servicios en la nube',
        '45678123Y'
    ),
    (
        'E5678901E',
        'BioHealth',
        'Bio Health Solutions S.A.',
        '969567890',
        'Javier Torres',
        '621678901',
        'javier.torres@biohealth.com',
        'Av. Salud 8, Alicante',
        '03005',
        'www.biohealth.com',
        'info@biohealth.com',
        0,
        3,
        'Soluciones biotecnológicas para la salud.',
        'Biotecnología',
        'Investigación, Desarrollo de productos médicos',
        '56789123Z'
    ),
    (
        'F6789012F',
        'SmartBuild',
        'Smart Building Co.',
        '970678901',
        'Laura Pérez',
        '622789012',
        'laura.perez@smartbuild.com',
        'Calle Construcción 20, Alicante',
        '03006',
        'www.smartbuild.com',
        'administracion@smartbuild.com',
        1,
        6,
        'Tecnología inteligente para la construcción.',
        'Construcción Inteligente',
        'Automatización, Ingeniería Civil',
        '67891123Q'
    ),
    (
        'G7890123G',
        'AgroPlus',
        'Agro Plus S.L.',
        '971789012',
        'Pedro Sánchez',
        '623890123',
        'pedro.sanchez@agroplus.com',
        'Camino Agricultura 5, Alicante',
        '03007',
        'www.agroplus.com',
        'ventas@agroplus.com',
        1,
        2,
        'Equipos avanzados para la agricultura.',
        'Agroindustria',
        'Sistemas de riego, Maquinaria agrícola',
        '89246468G'
    ),
    (
        'H8901234H',
        'EcoDesign',
        'Eco Design Studio S.L.',
        '972890123',
        'Sofía Fernández',
        '624901234',
        'sofia.fernandez@ecodesign.com',
        'Calle Creatividad 18, Alicante',
        '03008',
        'www.ecodesign.com',
        'info@ecodesign.com',
        0,
        1,
        'Diseño sostenible y ecológico.',
        'Diseño y Arquitectura',
        'Diseño de interiores, Arquitectura sostenible',
        '86846171L'
    );

-- Catálogo de ciclos
INSERT INTO
    catalogo_ciclos (nombre, alias)
VALUES (
        'Desarrollo de Aplicaciones Multiplataforma - 2º',
        '2DAM'
    ),
    (
        'Desarrollo de Aplicaciones Multiplataforma - 1º',
        '1DAM'
    ),
    (
        'Sistemas Microinformáticos y Redes - 2º',
        '2SMR'
    ),
    (
        'Sistemas Microinformáticos y Redes - 1º',
        '1SMR'
    ),
    (
        'Administración de Sistemas Informáticos en Red - 2º',
        '2ASIR'
    ),
    (
        'Administración de Sistemas Informáticos en Red - 1º',
        '1ASIR'
    ),
    (
        'Desarrollo de Aplicaciones Web - 2º',
        '2DAW'
    ),
    (
        'Desarrollo de Aplicaciones Web - 1º',
        '1DAW'
    );

-- Grupos
INSERT INTO
    grupos (
        id_ciclo,
        dni_tutor,
        alias_grupo
    )
VALUES (1, '45678123Y', 'A'),
    (1, '56789123Z', 'B'),
    (2, '67891123Q', 'A'),
    (3, null, 'A');

-- Alumnos
INSERT INTO
    alumnos (
        dni_nie,
        nombre,
        apellido1,
        apellido2,
        fecha_nacimiento,
        telefono,
        email,
        direccion,
        vehiculo,
        id_grupo
    )
VALUES (
        '12345678Q',
        'Juan',
        'Vega',
        'Saenz',
        '2000-08-08',
        '618253876',
        'juanvegasaenz@gmail.com',
        'C/ Mercurio',
        'Si',
        1
    ),
    (
        '23456789S',
        'Salvador',
        'Perez',
        'Sanchez',
        '2003-01-08',
        '950254837',
        'salvadorperez@gmail.com',
        'C/ Real del barrio alto',
        'No',
        2
    ),
    (
        '34567890H',
        'Helen',
        'Koss',
        NULL,
        '1999-05-06',
        '628349590',
        'Helenkoos@gmail.com',
        'C/ Estrella fugaz',
        'Si',
        3
    ),
    (
        '45678901J',
        'María',
        'García',
        'Lopez',
        '2002-11-10',
        '612345678',
        'mariagarcia@gmail.com',
        'C/ Luna Nueva',
        'No',
        1
    ),
    (
        '56789012K',
        'Pablo',
        'Hernández',
        'Martínez',
        '2001-04-22',
        '613245678',
        'pablohernandez@gmail.com',
        'C/ Arco Iris',
        'Si',
        3
    ),
    (
        '67890123L',
        'Lucía',
        'Ruiz',
        'González',
        '2000-07-15',
        '614345678',
        'luciaruiz@gmail.com',
        'C/ Norte',
        'No',
        2
    ),
    (
        '78901234M',
        'Alejandro',
        'Díaz',
        'Torres',
        '2003-09-05',
        '615445678',
        'alejandrodiaz@gmail.com',
        'C/ Campo Verde',
        'Si',
        4
    ),
    (
        '89012345N',
        'Sara',
        'Moreno',
        'Pérez',
        '1999-03-18',
        '616545678',
        'saramoreno@gmail.com',
        'C/ Sol Naciente',
        'No',
        4
    ),
    (
        '90123456O',
        'Miguel',
        'Santos',
        'Romero',
        '2002-12-24',
        '617645678',
        'miguelsantos@gmail.com',
        'C/ Océano',
        'Si',
        1
    ),
    (
        '11234567P',
        'Carmen',
        'Lopez',
        'Ramos',
        '2001-05-30',
        '618745678',
        'carmenlopez@gmail.com',
        'C/ Lago Azul',
        'No',
        2
    );