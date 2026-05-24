-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-10-2024 a las 16:11:24
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
-- Estructura de tabla para la tabla `sw_periodo_evaluacion_curso`
--

CREATE TABLE `sw_periodo_evaluacion_curso` (
  `id_periodo_evaluacion_curso` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_curso` int(11) NOT NULL,
  `pe_ponderacion` float DEFAULT NULL,
  `pc_orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  ADD PRIMARY KEY (`id_periodo_evaluacion_curso`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  MODIFY `id_periodo_evaluacion_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  ADD CONSTRAINT `sw_periodo_evaluacion_curso_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
