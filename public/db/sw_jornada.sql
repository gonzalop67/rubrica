-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2022 a las 13:58:08
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
-- Estructura de tabla para la tabla `sw_jornada`
--

CREATE TABLE `sw_jornada` (
  `id_jornada` int(11) UNSIGNED NOT NULL,
  `jo_nombre` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_jornada`
--

INSERT INTO `sw_jornada` (`id_jornada`, `jo_nombre`) VALUES
(1, 'MATUTINA'),
(2, 'VESPERTINA'),
(3, 'NOCTURNA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  MODIFY `id_jornada` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
