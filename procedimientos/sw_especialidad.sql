-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-10-2024 a las 16:42:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

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
-- Estructura de tabla para la tabla `sw_especialidad`
--

CREATE TABLE `sw_especialidad` (
  `id_especialidad` int(11) UNSIGNED NOT NULL,
  `id_tipo_educacion` int(11) UNSIGNED NOT NULL,
  `es_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_figura` varchar(50) NOT NULL,
  `es_abreviatura` varchar(15) NOT NULL,
  `es_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  MODIFY `id_especialidad` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD CONSTRAINT `sw_especialidad_tipo_educacion` FOREIGN KEY (`id_tipo_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
