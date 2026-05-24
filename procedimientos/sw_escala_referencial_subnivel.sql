-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2025 a las 21:50:42
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
-- Estructura de tabla para la tabla `sw_escala_referencial_subnivel`
--

CREATE TABLE `sw_escala_referencial_subnivel` (
  `escala_referencial_id` int(11) NOT NULL,
  `sub_nivel_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_escala_referencial_subnivel`
--
ALTER TABLE `sw_escala_referencial_subnivel`
  ADD KEY `escala_referencial_id` (`escala_referencial_id`),
  ADD KEY `sub_nivel_id` (`sub_nivel_id`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_escala_referencial_subnivel`
--
ALTER TABLE `sw_escala_referencial_subnivel`
  ADD CONSTRAINT `sw_escala_referencial_subnivel_ibfk_1` FOREIGN KEY (`escala_referencial_id`) REFERENCES `sw_escala_referencial` (`id_escala_referencial`),
  ADD CONSTRAINT `sw_escala_referencial_subnivel_ibfk_2` FOREIGN KEY (`sub_nivel_id`) REFERENCES `sw_sub_nivel_educacion` (`id_nivel_educacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
