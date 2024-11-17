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

/*
CREATE TABLE Auditoria_Gasto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    año YEAR NOT NULL,
    es_historico BOOLEAN DEFAULT FALSE,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);
*/
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
    fecha DATE NOT NULL,
    year YEAR NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

--Falta documentar
CREATE TABLE Plan_Anual_Operativo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year YEAR NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

--
CREATE TABLE Plan_Sectorial (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year YEAR NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);