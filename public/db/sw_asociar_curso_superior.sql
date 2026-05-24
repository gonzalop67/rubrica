-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2022 a las 14:48:01
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
-- Estructura de tabla para la tabla `sw_asociar_curso_superior`
--

CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso_inferior` int(11) UNSIGNED NOT NULL,
  `id_curso_superior` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD PRIMARY KEY (`id_asociar_curso_superior`),
  ADD KEY `sw_asociar_curso_superior_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_asociar_curso_superior_id_curso_inferior_foreign` (`id_curso_inferior`),
  ADD KEY `sw_asociar_curso_superior_id_curso_superior_foreign` (`id_curso_superior`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  MODIFY `id_asociar_curso_superior` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD CONSTRAINT `sw_asociar_curso_superior_id_curso_inferior_foreign` FOREIGN KEY (`id_curso_inferior`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_asociar_curso_superior_id_curso_superior_foreign` FOREIGN KEY (`id_curso_superior`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_asociar_curso_superior_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
