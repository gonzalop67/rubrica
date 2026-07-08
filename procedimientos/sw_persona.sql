-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-06-2026 a las 14:42:18
-- Versión del servidor: 10.11.15-MariaDB-cll-lve
-- Versión de PHP: 8.4.15

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
-- Estructura de tabla para la tabla `sw_persona`
--

CREATE TABLE `sw_persona` (
  `id_persona` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `dni` varchar(64) NOT NULL,
  `titulo` varchar(8) NOT NULL,
  `descripcion_titulo` varchar(96) NOT NULL,
  `primer_apellido` varchar(32) NOT NULL,
  `segundo_apellido` varchar(32) NOT NULL,
  `primer_nombre` varchar(32) NOT NULL,
  `segundo_nombre` varchar(32) NOT NULL,
  `nombre_corto` varchar(45) NOT NULL,
  `nombre_largo` varchar(64) NOT NULL,
  `genero` enum('Femenino','Masculino') NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_persona`
--
ALTER TABLE `sw_persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD KEY `id_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_persona`
--
ALTER TABLE `sw_persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_persona`
--
ALTER TABLE `sw_persona`
  ADD CONSTRAINT `sw_persona_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `sw_usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
