-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2024 a las 21:07:33
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
-- Estructura de tabla para la tabla `sw_modalidad`
--

CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) UNSIGNED NOT NULL,
  `mo_nombre` varchar(64) NOT NULL,
  `mo_activo` tinyint(1) UNSIGNED NOT NULL,
  `mo_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `sw_modalidad`
--

INSERT INTO `sw_modalidad` (`id_modalidad`, `mo_nombre`, `mo_activo`, `mo_orden`) VALUES
(1, 'SEMIPRESENCIAL', 1, 1),
(2, 'BGU INTENSIVO', 1, 3),
(3, 'EGB SUPERIOR INTENSIVA', 1, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
