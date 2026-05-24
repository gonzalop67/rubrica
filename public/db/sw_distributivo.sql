-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-06-2022 a las 18:44:34
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
-- Estructura de tabla para la tabla `sw_distributivo`
--

CREATE TABLE `sw_distributivo` (
  `id_distributivo` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_malla_curricular` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD PRIMARY KEY (`id_distributivo`),
  ADD KEY `sw_distributivo_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_distributivo_id_malla_curricular_foreign` (`id_malla_curricular`),
  ADD KEY `sw_distributivo_id_paralelo_foreign` (`id_paralelo`),
  ADD KEY `sw_distributivo_id_asignatura_foreign` (`id_asignatura`),
  ADD KEY `sw_distributivo_id_usuario_foreign` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  MODIFY `id_distributivo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD CONSTRAINT `sw_distributivo_id_asignatura_foreign` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_distributivo_id_malla_curricular_foreign` FOREIGN KEY (`id_malla_curricular`) REFERENCES `sw_malla_curricular` (`id_malla_curricular`),
  ADD CONSTRAINT `sw_distributivo_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_distributivo_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_distributivo_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
