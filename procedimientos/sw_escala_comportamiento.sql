-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-11-2024 a las 17:06:51
-- Versión del servidor: 10.6.19-MariaDB-cll-lve
-- Versión de PHP: 7.3.33

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
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) NOT NULL,
  `ec_cualitativa` varchar(164) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_escala_comportamiento`
--

INSERT INTO `sw_escala_comportamiento` (`id_escala_comportamiento`, `ec_relacion`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_equivalencia`, `ec_correlativa`) VALUES
(1, 'Muy satisfactorio', 'Lidera el cumplimiento de los compromisos establecidos para la sana convivencia social.', '9 - 10', 9, 10, 'A', 5),
(2, 'Satisfactorio', 'Cumple con los compromisos establecidos para la sana convivencia social.', '7 - 8.99', 7, 8.999, 'B', 4),
(3, 'Poco satisfactorio', 'Falla ocasionalmente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '6 - 6.99', 6, 6.99, 'C', 3),
(4, 'Mejorable', 'Falla reiteradamente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '4 - 5.99', 4, 5.99, 'D', 2),
(5, 'Insatisfactorio', 'No cumple con los compromisos establecidos para la sana convivencia social.', '< 4', 0.01, 3.99, 'E', 1),
(6, 'Sin notas', 'Sin notas.', '0', 0, 0, 'S/N', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  ADD PRIMARY KEY (`id_escala_comportamiento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  MODIFY `id_escala_comportamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
