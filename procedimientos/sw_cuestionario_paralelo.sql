-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2023 a las 14:39:55
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
-- Estructura de tabla para la tabla `sw_cuestionario_paralelo`
--

CREATE TABLE `sw_cuestionario_paralelo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_category` int(5) NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `sw_exam_category` (`id`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
