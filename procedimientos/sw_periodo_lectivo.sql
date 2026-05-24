-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2024 a las 21:05:15
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
-- Estructura de tabla para la tabla `sw_periodo_lectivo`
--

CREATE TABLE `sw_periodo_lectivo` (
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_periodo_estado` int(11) UNSIGNED NOT NULL,
  `id_modalidad` int(11) UNSIGNED NOT NULL,
  `pe_anio_inicio` int(5) NOT NULL,
  `pe_anio_fin` int(5) NOT NULL,
  `pe_fecha_inicio` date NOT NULL,
  `pe_fecha_fin` date NOT NULL,
  `pe_estado` varchar(1) NOT NULL,
  `pe_nota_minima` float NOT NULL,
  `pe_nota_aprobacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_periodo_lectivo`
--

INSERT INTO `sw_periodo_lectivo` (`id_periodo_lectivo`, `id_periodo_estado`, `id_modalidad`, `pe_anio_inicio`, `pe_anio_fin`, `pe_fecha_inicio`, `pe_fecha_fin`, `pe_estado`, `pe_nota_minima`, `pe_nota_aprobacion`) VALUES
(1, 1, 1, 2021, 2022, '2021-09-01', '2022-07-31', 'A', 4.01, 7),
(2, 1, 3, 2021, 2022, '2021-10-25', '2022-08-31', 'A', 0.01, 7),
(3, 1, 2, 2021, 2022, '2021-10-25', '2022-03-25', 'A', 0.01, 7),
(7, 1, 1, 2022, 2023, '2022-09-01', '2023-07-13', 'A', 0.01, 7),
(8, 1, 3, 2022, 2023, '2023-09-01', '2024-02-09', 'A', 4.01, 7),
(9, 1, 2, 2021, 2022, '2022-04-26', '2022-09-09', 'A', 4.01, 7),
(11, 1, 1, 2023, 2024, '2023-09-01', '2024-07-09', '', 0, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD PRIMARY KEY (`id_periodo_lectivo`),
  ADD KEY `id_modalidad` (`id_modalidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_modalidad`) REFERENCES `sw_modalidad` (`id_modalidad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
