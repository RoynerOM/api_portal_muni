CREATE DATABASE portal_db;
USE DATABASE portal_db;

CREATE TABLE Presupuesto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year YEAR NOT NULL,
    tipo ENUM('Proyectado', 'Aprobado') NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE Ejecucion_Presupuestaria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('Parcial', 'Final','Aprobado','Ejecutado') NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL,
    es_historico BOOLEAN DEFAULT FALSE
);

CREATE TABLE Auditoria_Gasto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    año YEAR NOT NULL,
    es_historico BOOLEAN DEFAULT FALSE,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);

--Falta documentar
CREATE TABLE Informe_Financiero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    año YEAR NOT NULL,
    fecha DATE NOT NULL,
    url VARCHAR(255),
    nombre VARCHAR(100) NOT NULL
);