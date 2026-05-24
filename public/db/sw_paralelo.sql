-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2022 a las 14:04:41
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.3

SET FOREIGN_KEY_CHECKS=0;
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
-- Estructura de tabla para la tabla `sw_paralelo`
--

DROP TABLE IF EXISTS `sw_paralelo`;
CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso` int(11) UNSIGNED NOT NULL,
  `id_jornada` int(11) UNSIGNED NOT NULL,
  `pa_nombre` varchar(5) NOT NULL,
  `pa_orden` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `sw_paralelo_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_paralelo_id_curso_foreign` (`id_curso`),
  ADD KEY `sw_paralelo_id_jornada_foreign` (`id_jornada`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD CONSTRAINT `sw_paralelo_id_curso_foreign` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_paralelo_id_jornada_foreign` FOREIGN KEY (`id_jornada`) REFERENCES `sw_jornada` (`id_jornada`),
  ADD CONSTRAINT `sw_paralelo_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
