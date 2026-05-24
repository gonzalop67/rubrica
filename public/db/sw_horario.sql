-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-06-2022 a las 18:51:46
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
-- Estructura de tabla para la tabla `sw_horario`
--

CREATE TABLE `sw_horario` (
  `id_horario` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_dia_semana` int(11) UNSIGNED NOT NULL,
  `id_hora_clase` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `sw_horario_id_asignatura_foreign` (`id_asignatura`),
  ADD KEY `sw_horario_id_paralelo_foreign` (`id_paralelo`),
  ADD KEY `sw_horario_id_dia_semana_foreign` (`id_dia_semana`),
  ADD KEY `sw_horario_id_hora_clase_foreign` (`id_hora_clase`),
  ADD KEY `sw_horario_id_usuario_foreign` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD CONSTRAINT `sw_horario_id_asignatura_foreign` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_horario_id_dia_semana_foreign` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`),
  ADD CONSTRAINT `sw_horario_id_hora_clase_foreign` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_horario_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_horario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
