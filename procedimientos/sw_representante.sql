-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 25-10-2024 a las 14:13:02
-- Versión del servidor: 10.6.19-MariaDB-cll-lve
-- Versión de PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegion_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_representante`
--

CREATE TABLE `sw_representante` (
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `re_apellidos` varchar(32) DEFAULT NULL,
  `re_nombres` varchar(32) DEFAULT NULL,
  `re_nombre_completo` varchar(64) DEFAULT NULL,
  `re_cedula` varchar(10) DEFAULT NULL,
  `re_genero` varchar(1) DEFAULT NULL,
  `re_email` varchar(64) DEFAULT NULL,
  `re_sector` varchar(36) DEFAULT NULL,
  `re_direccion` varchar(64) DEFAULT NULL,
  `re_telefono` varchar(16) DEFAULT NULL,
  `re_observacion` varchar(256) DEFAULT NULL,
  `re_parentesco` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='					';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `fk_representante_estudiante_idx` (`id_estudiante`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD CONSTRAINT `sw_representante_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
