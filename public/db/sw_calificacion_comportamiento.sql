-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 29-07-2022 a las 14:35:34
-- Versión del servidor: 10.3.34-MariaDB-cll-lve
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
-- Estructura de tabla para la tabla `sw_calificacion_comportamiento`
--

CREATE TABLE `sw_calificacion_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `co_calificacion` int(11) NOT NULL,
  `co_cualitativa` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_calificacion_comportamiento`
--
ALTER TABLE `sw_calificacion_comportamiento`
  ADD KEY `id_paralelo` (`id_paralelo`,`id_estudiante`,`id_aporte_evaluacion`,`id_asignatura`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
