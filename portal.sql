CREATE DATABASE portal_db;
USE DATABASE portal_db;

CREATE TABLE Presupuesto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year YEAR NOT NULL,
    tipo ENUM('Proyectado', 'Aprobado') NOT NULL,
    categoria ENUM('Ordinario', 'Extraordinario', 'Modificación Presupuestaria') NOT NULL
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

/*

Aplciar filtros por año y categoria Presupuesto
Aplciar filtros por año

*/

CREATE TABLE Ejecucion_Presupuestaria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Parcial', 'Final','Histórico','Auditorías') NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL,
    es_historico BOOLEAN DEFAULT FALSE
);

--Falta documentar
CREATE TABLE Reporte_Financiero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    año YEAR NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(150) NOT NULL
);

--Falta documentar
CREATE TABLE Plan_Institucional (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Plan estratégico/ institucional', 'Plan anual operativo', 'Otros planes específicos o sectoriales') NOT NULL,
    fecha DATE NOT NULL,
    year YEAR NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

--Falta documentar
CREATE TABLE Informes_Cumplimientos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Informes de cumplimiento', 'Informe anual de gestión', 'Informe final de gestión','Histórico de informes anuales','Informes de seguimiento a las recomendaciones') NOT NULL,
    year YEAR NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);


--Falta documentar
-- EL HISTORICO SE SACA DEL ESPECIAL Y ANUAL DE LOS ULTIMOS 5 AÑOS
CREATE TABLE Informes_Institucionales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Especiales', 'Anuales', 'Archivo','Calificación de personal') NOT NULL,
    fecha DATE NOT NULL,
    year YEAR NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

--Falta documentar
CREATE TABLE Informes_Personal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Actividades', 'Viajes') NOT NULL,
    fecha DATE NOT NULL,
    year YEAR NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
); 


--Falta documentar
-- Filtrar o seleccionar actas y acuerdos por año
CREATE TABLE Actas_Orden (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Acta','Orden del día') NOT NULL,
    fecha DATE NOT NULL,
    year YEAR NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
); 

--Filtrar Por actas y seleccionar el acta relacionado
CREATE TABLE Acuerdos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    actaId INT NOT NULL,
    fecha DATE NOT NULL,
    year YEAR NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
); 
 