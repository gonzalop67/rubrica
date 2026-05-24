-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2022 a las 17:28:47
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rubrica_ci4`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) UNSIGNED NOT NULL,
  `id_tipo_documento` int(11) UNSIGNED NOT NULL,
  `id_def_genero` int(11) UNSIGNED NOT NULL,
  `id_def_nacionalidad` int(11) UNSIGNED NOT NULL,
  `es_apellidos` varchar(32) NOT NULL,
  `es_nombres` varchar(32) NOT NULL,
  `es_nombre_completo` varchar(64) NOT NULL,
  `es_cedula` varchar(10) NOT NULL,
  `es_email` varchar(64) NOT NULL,
  `es_sector` varchar(36) NOT NULL,
  `es_direccion` varchar(64) NOT NULL,
  `es_telefono` varchar(32) NOT NULL,
  `es_fec_nacim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `sw_estudiante_id_tipo_documento_foreign` (`id_tipo_documento`),
  ADD KEY `sw_estudiante_id_def_genero_foreign` (`id_def_genero`),
  ADD KEY `sw_estudiante_id_def_nacionalidad_foreign` (`id_def_nacionalidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD CONSTRAINT `sw_estudiante_id_def_genero_foreign` FOREIGN KEY (`id_def_genero`) REFERENCES `sw_def_genero` (`id_def_genero`),
  ADD CONSTRAINT `sw_estudiante_id_def_nacionalidad_foreign` FOREIGN KEY (`id_def_nacionalidad`) REFERENCES `sw_def_nacionalidad` (`id_def_nacionalidad`),
  ADD CONSTRAINT `sw_estudiante_id_tipo_documento_foreign` FOREIGN KEY (`id_tipo_documento`) REFERENCES `sw_tipo_documento` (`id_tipo_documento`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
