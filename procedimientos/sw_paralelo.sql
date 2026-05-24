-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-10-2024 a las 16:40:50
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
-- Estructura de tabla para la tabla `sw_paralelo`
--

CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso` int(11) UNSIGNED NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `pa_nombre` varchar(5) NOT NULL,
  `pa_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `sw_paralelo_curso` (`id_curso`),
  ADD KEY `sw_paralelo_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `sw_paralelo_jornada` (`id_jornada`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD CONSTRAINT `sw_paralelo_curso` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_paralelo_jornada` FOREIGN KEY (`id_jornada`) REFERENCES `sw_jornada` (`id_jornada`),
  ADD CONSTRAINT `sw_paralelo_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
