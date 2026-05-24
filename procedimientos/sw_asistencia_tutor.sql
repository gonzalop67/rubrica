-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-12-2019 a las 12:08:57
-- Versión del servidor: 10.1.41-MariaDB-cll-lve
-- Versión de PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
-- Estructura de tabla para la tabla `sw_asistencia_tutor`
--

CREATE TABLE `sw_asistencia_tutor` (
  `id_asistencia_tutor` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_inasistencia` int(11) NOT NULL,
  `at_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD PRIMARY KEY (`id_asistencia_tutor`),
  ADD KEY `fk_asistencia_tutor_estudiante` (`id_estudiante`),
  ADD KEY `fk_asistencia_tutor_paralelo` (`id_paralelo`),
  ADD KEY `id_asistencia_tutor_inasistencia` (`id_inasistencia`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  MODIFY `id_asistencia_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD CONSTRAINT `fk_asistencia_tutor_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_asistencia_tutor_paralelo` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `id_asistencia_tutor_inasistencia` FOREIGN KEY (`id_inasistencia`) REFERENCES `sw_inasistencia` (`id_inasistencia`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
