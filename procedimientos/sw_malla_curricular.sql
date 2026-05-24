-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2024 a las 18:49:24
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
-- Estructura de tabla para la tabla `sw_malla_curricular`
--

CREATE TABLE `sw_malla_curricular` (
  `id_malla_curricular` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `ma_horas_presenciales` int(11) UNSIGNED NOT NULL,
  `ma_horas_autonomas` int(11) UNSIGNED NOT NULL,
  `ma_horas_tutorias` int(11) UNSIGNED NOT NULL,
  `ma_subtotal` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD PRIMARY KEY (`id_malla_curricular`),
  ADD KEY `sw_malla_curricular_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_malla_curricular_id_curso_foreign` (`id_curso`),
  ADD KEY `sw_malla_curricular_id_asignatura_foreign` (`id_asignatura`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  MODIFY `id_malla_curricular` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD CONSTRAINT `sw_malla_curricular_id_asignatura_foreign` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_malla_curricular_id_curso_foreign` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_malla_curricular_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
