CREATE DATABASE IF NOT EXISTS `BD`;
-- STRICT_TRANS_TABLES garante que o ENUM rejeite valores inválidos em vez de gravar vazio
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO,STRICT_TRANS_TABLES";
SET NAMES utf8mb4;
USE `BD`;

DROP VIEW  IF EXISTS vw_login;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS Permi;
DROP TABLE IF EXISTS motos;

CREATE TABLE IF NOT EXISTS Funcionario (
  Id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Nome      VARCHAR(50),
  Endereco  VARCHAR(50),
  Idade     INT,
  DataNasc  DATETIME,
  Foto      VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS Cliente (
  Id_Cli       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Nome_Cli     VARCHAR(100) NOT NULL,
  Tel_Cli      VARCHAR(20)  NULL,
  CPF_Cli      VARCHAR(14)  NOT NULL UNIQUE,
  Email_Cli    VARCHAR(100) NULL,
  Endereco_Cli VARCHAR(200) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nivel_usu: '0' = funcionário normal, '1' = gerente
CREATE TABLE IF NOT EXISTS Usuarios (
  Id_Usu     INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Login_usu  VARCHAR(50)  NOT NULL UNIQUE,
  Senha_usu  VARCHAR(255) NOT NULL,
  Foto_usu   VARCHAR(30)  NULL,
  Nivel_usu  ENUM('0','1') NOT NULL DEFAULT '0',
  Ativo_usu  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Usuarios (Login_usu, Senha_usu, Nivel_usu) VALUES
  ('admin',   '$2y$10$u8bMgvzFnp5PhNNH7BTlvuX2eQI9pOv7ePThg3IlLPuPh4KLLCDrq', '1'),
  ('usuario', '$2y$10$ldGuxBa1dxjOa8959QNoROa9KRRwj1LdCtd1qYLznCacJgMacsMC6', '0')
ON DUPLICATE KEY UPDATE
  Senha_usu = VALUES(Senha_usu),
  Nivel_usu = VALUES(Nivel_usu),
  Ativo_usu = 1;
