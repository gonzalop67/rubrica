-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2024 a las 15:05:55
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
-- Estructura de tabla para la tabla `sw_paralelo_tutor`
--

CREATE TABLE `sw_paralelo_tutor` (
  `id_paralelo_tutor` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD PRIMARY KEY (`id_paralelo_tutor`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  MODIFY `id_paralelo_tutor` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
