-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-07-2026 a las 23:21:38
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
-- Base de datos: `siae_2026`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id` int(11) NOT NULL,
  `tipo_documento_id` int(11) NOT NULL DEFAULT 1,
  `nacionalidad_id` int(11) NOT NULL DEFAULT 1,
  `dni` varchar(20) NOT NULL,
  `primer_nombre` varchar(32) NOT NULL,
  `segundo_nombre` varchar(32) DEFAULT NULL,
  `primer_apellido` varchar(32) NOT NULL,
  `segundo_apellido` varchar(32) DEFAULT NULL,
  `nombre_corto` varchar(64) DEFAULT NULL,
  `nombre_completo` varchar(128) DEFAULT NULL,
  `titulo` varchar(16) DEFAULT NULL,
  `descripcion_titulo` varchar(96) DEFAULT NULL,
  `genero` enum('Femenino','Masculino','Otro') NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(32) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `sector` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `tipo_documento_id` (`tipo_documento_id`),
  ADD KEY `nacionalidad_id` (`nacionalidad_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `personas_ibfk_1` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento` (`id`),
  ADD CONSTRAINT `personas_ibfk_2` FOREIGN KEY (`nacionalidad_id`) REFERENCES `nacionalidades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
