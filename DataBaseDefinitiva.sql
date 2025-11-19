CREATE DATABASE PROYECT_DataBase_MyCoop6;
USE PROYECT_DataBase_MyCoop6;

CREATE TABLE Persona (
    Cedula INT(8) NOT NULL,
    Nombre VARCHAR(100)NOT NULL,
    Apellido VARCHAR(100)NOT NULL,
    Direccion VARCHAR(150)NOT NULL,
    edad int(3) NOT NULL,
    Comunicacion VARCHAR(100)NOT NULL,
    Contrasena VARCHAR(100)NOT NULL,
    pronombres varchar(20),
    horas_trabajadas int default 0,
    fotoperfil varchar(255) default'defaultPerfile.png',
    Aceptado tinyint,
	PRIMARY KEY (Cedula)
);

ALTER TABLE Persona ADD COLUMN Bloqueado BOOLEAN DEFAULT FALSE;
ALTER TABLE Persona ADD COLUMN Rol ENUM('usuario','administrador','tesorero') NOT NULL DEFAULT 'usuario';
ALTER TABLE Persona ADD COLUMN HorasTrabajadas INT NOT NULL DEFAULT 0;
ALTER TABLE Persona ADD COLUMN FotoPerfil VARCHAR(255) DEFAULT 'DefaultPerfile.png';

INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado)VALUES (10000001, 'Ana', 'Pérez', 'Calle 123, Montevideo', '091111111', 97, 'ClaveAna','Ella',True);
INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado)VALUES (10000002, 'Luis', 'Gómez', 'Av. Libertador 456, Montevideo','luis.gomez@email.com', 35,'ClaveLuis','El',True);
INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado)VALUES (10000003, 'María', 'Rodríguez', 'Calle 789, Canelones', '093333333',  20,'ClaveMaria','Ella',true);
INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado)VALUES (10000004, 'Jorge', 'Fernández', 'Ruta 8, Las Piedras','jorge.fernandez@email.com',45, 'ClaveJorge','El', true);
INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado)VALUES (10000005, 'Lucía', 'Martínez', 'Calle Rivera 321, Montevideo', '095555555',  40, 'ClaveLucia','Ella',true);
INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado, Rol) VALUES(56991299, 'Max', 'Mendina','Juan Rosas 4525D','099550908', 20,'holamundo', 'ella', true, 'tesorero');
INSERT INTO Persona (Cedula, Nombre, Apellido, Direccion, Comunicacion, edad, contrasena, pronombres, Aceptado, Rol) VALUES(56991475, 'Aileen', 'Argañaras','Juan Rosas 4525D','097473736',18 ,'equipoInfrog', 'ella', true, 'administrador');

CREATE TABLE Administrador (
    Cedula INT(8)NOT NULL,
    idAdmin INT(8)NOT NULL auto_increment,
    PRIMARY KEY (idAdmin),
    FOREIGN KEY (Cedula) REFERENCES Persona(Cedula)
);
INSERT INTO Administrador (Cedula)VALUES (10000001);

CREATE TABLE Tesorero (
    Cedula INT(8)NOT NULL,
    idTesorero INT(8) NOT NULL auto_increment,
    PRIMARY KEY (idTesorero),
    FOREIGN KEY (Cedula) REFERENCES Persona(Cedula)
);
INSERT INTO Tesorero (Cedula)VALUES (10000002);

CREATE TABLE FondoMonetario (
    IdFondo INT(10) NOT NULL,
    Monto DECIMAL(12,2),
    DescripcionFondo TEXT NOT NULL,
    Cedula_Tesorero INT(8) NOT NULL,
    PRIMARY KEY (IdFondo)
);
INSERT INTO FondoMonetario (IdFondo, Monto, DescripcionFondo, Cedula_Tesorero)VALUES (1, 50000.00, 'Fondo inicial para construcción', 10000002);
INSERT INTO FondoMonetario (IdFondo, Monto, DescripcionFondo, Cedula_Tesorero)VALUES (2, 120000.00, 'Fondo proveniente de donaciones', 10000002);

CREATE TABLE Eventos (
    IdEvento INT(10) NOT NULL auto_increment,
    NombreEvento VARCHAR(150)NOT NULL,
    FechaEvento DATE NOT NULL,
    DescripcionEvento TEXT NOT NULL,
	PRIMARY KEY (IdEvento)
);
INSERT INTO Eventos (NombreEvento, FechaEvento, DescripcionEvento)VALUES ('Asamblea General', '2025-02-10', 'Reunión con todos los miembros para definir objetivos del año');
INSERT INTO Eventos (NombreEvento, FechaEvento, DescripcionEvento)VALUES ('Jornada de limpieza', '2025-03-15', 'Actividad comunitaria en el predio de la cooperativa');

CREATE TABLE Archivos (
    IdArchivo INT(10) NOT NULL auto_increment,
    NombreArchivo VARCHAR(150)NOT NULL,
    Fecha DATE NOT NULL,
    DescripcionArch TEXT NOT NULL,
    PRIMARY KEY (IdArchivo)
);
INSERT INTO Archivos (NombreArchivo, Fecha, DescripcionArch)VALUES ('ActaAsamblea.pdf', '2025-02-10', 'Acta de la asamblea general del 10 de febrero');
INSERT INTO Archivos (NombreArchivo, Fecha, DescripcionArch)VALUES ('ListaParticipantes.xlsx', '2025-03-15', 'Listado de miembros que participaron en la jornada de limpieza');

CREATE TABLE UnidadesHabitacionales (
    IdUH INT(10)NOT NULL auto_increment,
    Estado VARCHAR(50)NOT NULL,
    Direccion VARCHAR(150)NOT NULL,
    PRIMARY KEY(IdUH)
);
INSERT INTO UnidadesHabitacionales (Estado, Direccion)VALUES ('En construcción', 'Solar 1, Complejo Prado');
INSERT INTO UnidadesHabitacionales (Estado, Direccion)VALUES ('Pendiente', 'Solar 2, Complejo Prado');
INSERT INTO UnidadesHabitacionales (Estado, Direccion)VALUES ('Terminado', 'Solar 3, Complejo Prado');


CREATE TABLE Construye (
    Cedula INT(8) NOT NULL,
    IdUH INT(10) NOT NULL,
    Etapa VARCHAR(50) NOT NULL,
    HorasTotales INT NOT NULL DEFAULT 0, -- Acumula todo, sin límite
    HorasSemanales INT NOT NULL CHECK (HorasSemanales BETWEEN 0 AND 168), -- Solo de la semana actual
    SemanaInicio DATE NOT NULL, -- Para saber desde cuándo se cuentan las horas semanales
    DescripcionConst TEXT NOT NULL,
    PRIMARY KEY (Cedula, IdUH),
    FOREIGN KEY (Cedula) REFERENCES Persona(Cedula),
    FOREIGN KEY (IdUH) REFERENCES UnidadesHabitacionales(IdUH)
);
INSERT INTO Construye (Cedula, Etapa, HorasTrabajadas, DescripcionConst)VALUES (10000003,'Cimientos', 6, 'Trabajo en cimientos del bloque 1');
INSERT INTO Construye (Cedula, Etapa, HorasTrabajadas, DescripcionConst)VALUES (10000004,'Paredes', 8, 'Levantamiento de muros en bloque 1');
INSERT INTO Construye (Cedula, Etapa, HorasTrabajadas, DescripcionConst)VALUES (10000005, 'Preparación', 5, 'Limpieza del terreno en solar 2');
INSERT INTO Construye (Cedula, IdUH, Etapa, HorasSemanales, SemanaInicio, DescripcionConst) VALUES(56991299, 1,'Levantando Cimientos', 10, '2025-08-09', 'Trabajos en el bloque 1');

create table Novedades(
	idNovedad int (10) NOT NULL auto_increment,
    Novedad varchar(1000) NOT NULL default '',
    primary key (idNovedad)
);

create table Mensajes(
	idMensaje int(10) NOT NULL auto_increment,
    Mensaje varchar (1000) NOT NULL default '',
    Cedula INT(8),
    PRIMARY KEY (idMensaje),
	foreign key (Cedula) REFERENCES Persona(Cedula)
);
ALTER TABLE Mensajes
ADD COLUMN Respuesta VARCHAR(1000) DEFAULT NULL,
ADD COLUMN Archivado TINYINT(1) DEFAULT 0;

CREATE TABLE Foros (
    IdForo INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(255) NOT NULL,
    Autor VARCHAR(100) NOT NULL,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Respuestas (
    IdRespuesta INT AUTO_INCREMENT PRIMARY KEY,
    IdForo INT NOT NULL,
    Autor VARCHAR(100) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdForo) REFERENCES Foros(IdForo) ON DELETE CASCADE
);

CREATE TABLE Notificaciones (
    IdNotificacion INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50),
    Mensaje TEXT,
    Fecha DATETIME,
    Estado VARCHAR(20)
);

CREATE TABLE ReportesForo (
    idReporte INT AUTO_INCREMENT PRIMARY KEY,
    IdForo INT NOT NULL,
    CedulaReportante INT NOT NULL,
    Motivo VARCHAR(255),
    FechaReporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdForo) REFERENCES Foros(IdForo),
    FOREIGN KEY (CedulaReportante) REFERENCES Persona(Cedula)
);
ALTER TABLE ReportesForo ADD COLUMN Estado VARCHAR(20) DEFAULT 'Pendiente';

CREATE TABLE Configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    font_size TINYINT NOT NULL DEFAULT 3,
    theme ENUM('light','dark') NOT NULL DEFAULT 'light',
    icons ENUM('icons','words') NOT NULL DEFAULT 'icons'
);
INSERT INTO Configuracion (font_size, theme, icons) VALUES (3, 'light', 'icons');

CREATE TABLE ConfiguracionUsuario (
    Cedula INT(8) NOT NULL,
    font_size TINYINT NOT NULL DEFAULT 3,
    theme ENUM('light','dark') NOT NULL DEFAULT 'light',
    icons ENUM('icons','words') NOT NULL DEFAULT 'icons',
    PRIMARY KEY (Cedula),
    FOREIGN KEY (Cedula) REFERENCES Persona(Cedula)
);
 
