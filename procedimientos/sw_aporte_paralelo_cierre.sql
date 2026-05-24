-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2024 a las 15:18:44
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
-- Estructura de tabla para la tabla `sw_aporte_paralelo_cierre`
--

CREATE TABLE `sw_aporte_paralelo_cierre` (
  `id_aporte_paralelo_cierre` int(11) UNSIGNED NOT NULL,
  `id_aporte_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_estado` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD PRIMARY KEY (`id_aporte_paralelo_cierre`),
  ADD KEY `sw_aporte_paralelo_cierre_id_aporte_evaluacion_foreign` (`id_aporte_evaluacion`),
  ADD KEY `sw_aporte_paralelo_cierre_id_paralelo_foreign` (`id_paralelo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  MODIFY `id_aporte_paralelo_cierre` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_id_aporte_evaluacion_foreign` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
