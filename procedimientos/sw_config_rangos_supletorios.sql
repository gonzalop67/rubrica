-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-05-2023 a las 04:02:18
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
-- Base de datos: `colegion_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_config_rangos_supletorios`
--

CREATE TABLE `sw_config_rangos_supletorios` (
  `id` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `nota_desde` float NOT NULL,
  `nota_hasta` float NOT NULL,
  `nota_aprobacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sw_config_rangos_supletorios_ibfk_1` (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  ADD CONSTRAINT `sw_config_rangos_supletorios_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`),
  ADD CONSTRAINT `sw_config_rangos_supletorios_ibfk_2` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
