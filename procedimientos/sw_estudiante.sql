-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 25-10-2024 a las 14:15:40
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
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_tipo_documento` int(11) NOT NULL DEFAULT 1,
  `id_def_genero` int(11) NOT NULL DEFAULT 1,
  `id_def_nacionalidad` int(11) NOT NULL DEFAULT 1,
  `es_apellidos` varchar(32) NOT NULL,
  `es_nombres` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_nombre_completo` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_cedula` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_email` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_sector` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_direccion` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_telefono` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_fec_nacim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_def_genero` (`id_def_genero`),
  ADD KEY `id_def_nacionalidad` (`id_def_nacionalidad`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD CONSTRAINT `sw_estudiante_ibfk_1` FOREIGN KEY (`id_def_genero`) REFERENCES `sw_def_genero` (`id_def_genero`),
  ADD CONSTRAINT `sw_estudiante_ibfk_2` FOREIGN KEY (`id_def_nacionalidad`) REFERENCES `sw_def_nacionalidad` (`id_def_nacionalidad`),
  ADD CONSTRAINT `sw_estudiante_ibfk_3` FOREIGN KEY (`id_tipo_documento`) REFERENCES `sw_tipo_documento` (`id_tipo_documento`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
