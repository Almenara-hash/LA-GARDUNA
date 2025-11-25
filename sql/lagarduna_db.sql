-- Crear base de datos
CREATE DATABASE IF NOT EXISTS lagarduna_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE lagarduna_db;

-- =========================
-- Tabla: usuarios
-- =========================
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  rol        VARCHAR(20)  NOT NULL DEFAULT 'cliente'  -- 'admin' o 'cliente'
) ENGINE=InnoDB;

-- =========================
-- Tabla: disponibilidad
-- =========================
DROP TABLE IF EXISTS disponibilidad;

CREATE TABLE disponibilidad (
  id_disponibilidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fecha       DATE NOT NULL,
  hora        TIME NOT NULL,
  disponible  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- Índice para buscar por fecha/hora
CREATE INDEX idx_disp_fecha_hora
  ON disponibilidad (fecha, hora);

-- =========================
-- Tabla: citas
-- =========================
DROP TABLE IF EXISTS citas;

CREATE TABLE citas (
  id_cita           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario        INT UNSIGNED NOT NULL,
  id_disponibilidad INT UNSIGNED NOT NULL,
  servicio          VARCHAR(100) NOT NULL,
  estado            VARCHAR(20)  NOT NULL DEFAULT 'pendiente',
  CONSTRAINT fk_citas_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_citas_disponibilidad
    FOREIGN KEY (id_disponibilidad) REFERENCES disponibilidad(id_disponibilidad)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- (Opcional) Insertar un usuario de prueba
-- =========================
-- Regístrate desde la web y luego, en phpMyAdmin, cambia el rol a 'admin':
-- UPDATE usuarios SET rol = 'admin' WHERE id_usuario = 1;
