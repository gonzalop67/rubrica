-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-06-2022 a las 15:10:08
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
-- Estructura de tabla para la tabla `sw_escala_calificaciones`
--

CREATE TABLE `sw_escala_calificaciones` (
  `id_escala_calificaciones` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `ec_cualitativa` varchar(64) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` int(4) NOT NULL,
  `ec_equivalencia` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_escala_calificaciones`
--

INSERT INTO `sw_escala_calificaciones` (`id_escala_calificaciones`, `id_periodo_lectivo`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`) VALUES
(1, 1, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'DA'),
(2, 1, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'AA'),
(3, 1, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'EA'),
(4, 1, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'NA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD PRIMARY KEY (`id_escala_calificaciones`),
  ADD KEY `sw_escala_calificaciones_id_periodo_lectivo_foreign` (`id_periodo_lectivo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD CONSTRAINT `sw_escala_calificaciones_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
