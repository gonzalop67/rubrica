-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-10-2025 a las 14:39:29
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
-- Estructura de tabla para la tabla `sw_sub_periodo_evaluacion`
--

CREATE TABLE `sw_sub_periodo_evaluacion` (
  `id_sub_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_tipo_periodo` int(11) UNSIGNED NOT NULL,
  `pe_nombre` varchar(48) NOT NULL,
  `pe_abreviatura` varchar(16) NOT NULL,
  `pe_ponderacion` float NOT NULL DEFAULT 0,
  `pe_orden` int(2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_sub_periodo_evaluacion`
--
ALTER TABLE `sw_sub_periodo_evaluacion`
  ADD PRIMARY KEY (`id_sub_periodo_evaluacion`),
  ADD KEY `sw_periodo_evaluacion_id_tipo_periodo_foreign` (`id_tipo_periodo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_sub_periodo_evaluacion`
--
ALTER TABLE `sw_sub_periodo_evaluacion`
  MODIFY `id_sub_periodo_evaluacion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
