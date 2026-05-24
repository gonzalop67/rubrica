-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2022 a las 18:47:22
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
-- Estructura de tabla para la tabla `sw_paralelo_inspector`
--

CREATE TABLE `sw_paralelo_inspector` (
  `id_paralelo_inspector` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD PRIMARY KEY (`id_paralelo_inspector`),
  ADD KEY `sw_paralelo_inspector_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_paralelo_inspector_id_paralelo_foreign` (`id_paralelo`),
  ADD KEY `sw_paralelo_inspector_id_usuario_foreign` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  MODIFY `id_paralelo_inspector` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD CONSTRAINT `sw_paralelo_inspector_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_paralelo_inspector_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_paralelo_inspector_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
