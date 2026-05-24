-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-06-2022 a las 18:48:06
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
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) UNSIGNED NOT NULL,
  `tp_descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_periodo`
--

INSERT INTO `sw_tipo_periodo` (`id_tipo_periodo`, `tp_descripcion`) VALUES
(1, 'QUIMESTRE'),
(2, 'SUPLETORIO'),
(3, 'REMEDIAL'),
(4, 'DE GRACIA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
