-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 06-11-2024 a las 23:44:46
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
-- Estructura de tabla para la tabla `sw_dia_semana`
--

CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `ds_nombre` varchar(10) NOT NULL,
  `ds_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD PRIMARY KEY (`id_dia_semana`),
  ADD KEY `id_horario_def` (`id_horario_def`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD CONSTRAINT `sw_dia_semana_ibfk_1` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
