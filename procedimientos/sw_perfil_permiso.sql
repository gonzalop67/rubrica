-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-06-2026 a las 20:54:17
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
-- Estructura de tabla para la tabla `sw_perfil_permiso`
--

CREATE TABLE `sw_perfil_permiso` (
  `id_perfil` int(11) UNSIGNED NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_perfil_permiso`
--
ALTER TABLE `sw_perfil_permiso`
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_perfil_permiso`
--
ALTER TABLE `sw_perfil_permiso`
  ADD CONSTRAINT `sw_perfil_permiso_ibfk_1` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_perfil_permiso_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `sw_permiso` (`id_permiso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
