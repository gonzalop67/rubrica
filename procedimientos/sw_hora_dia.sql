-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 07-11-2024 a las 13:58:06
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
-- Estructura de tabla para la tabla `sw_hora_dia`
--

CREATE TABLE `sw_hora_dia` (
  `id_hora_dia` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD PRIMARY KEY (`id_hora_dia`),
  ADD KEY `sw_hora_dia_ibfk_1` (`id_dia_semana`),
  ADD KEY `sw_hora_dia_ibfk_2` (`id_hora_clase`),
  ADD KEY `id_horario_def` (`id_horario_def`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  MODIFY `id_hora_dia` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD CONSTRAINT `sw_hora_dia_ibfk_1` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_2` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_3` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
