-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2024 a las 11:24:46
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
-- Estructura de tabla para la tabla `sw_area`
--

CREATE TABLE `sw_area` (
  `id_area` int(11) UNSIGNED NOT NULL,
  `ar_nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `sw_area`
--

INSERT INTO `sw_area` (`id_area`, `ar_nombre`) VALUES
(1, 'CIENCIAS NATURALES'),
(2, 'CIENCIAS SOCIALES'),
(3, 'EDUCACION CULTURAL Y ARTISTICA'),
(4, 'EDUCACION FISICA'),
(5, 'LENGUA EXTRANJERA'),
(6, 'LENGUA Y LITERATURA'),
(7, 'MATEMATICA'),
(8, 'MODULO INTER-ÁREAS'),
(10, 'CONTABILIDAD'),
(11, 'INFORMATICA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  ADD PRIMARY KEY (`id_area`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
