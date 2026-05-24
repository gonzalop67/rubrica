-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2022 a las 02:45:02
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
-- Estructura de tabla para la tabla `sw_tipo_documento`
--

CREATE TABLE `sw_tipo_documento` (
  `id_tipo_documento` int(11) UNSIGNED NOT NULL,
  `td_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_documento`
--

INSERT INTO `sw_tipo_documento` (`id_tipo_documento`, `td_nombre`) VALUES
(1, 'Cédula de Identidad'),
(2, 'Pasaporte'),
(3, 'Carnet de refugiado');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  MODIFY `id_tipo_documento` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
