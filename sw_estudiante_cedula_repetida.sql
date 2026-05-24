-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 20-06-2019 a las 20:16:26
-- Versión del servidor: 10.1.38-MariaDB-cll-lve
-- Versión de PHP: 7.2.7

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
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `es_nro_matricula` int(11) NOT NULL,
  `es_apellidos` varchar(32) NOT NULL,
  `es_nombres` varchar(32) NOT NULL,
  `es_nombre_completo` varchar(64) NOT NULL,
  `es_cedula` varchar(10) NOT NULL,
  `es_genero` varchar(1) NOT NULL,
  `es_email` varchar(64) NOT NULL,
  `es_sector` varchar(36) NOT NULL,
  `es_direccion` varchar(64) NOT NULL,
  `es_telefono` varchar(16) NOT NULL,
  `es_fec_nacim` date NOT NULL,
  `es_cod_uni_elec_nac` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_estudiante`
--

INSERT INTO `sw_estudiante` (`id_estudiante`, `es_cedula`, `COUNT(*)`) VALUES
(1066, '0605134113', 2),  --OJO UZCHA!!!--
(445, '0605256460', 2),
(1393, '0803831437', 2),
(597, '1004143093', 2),
(943, '1004143234', 2),
(554, '1004153639', 2),
(295, '1004791388', 3),
(604, '1050162831', 2),
(1284, '1105608473', 2),
(759, '1309312484', 2),
(1120, '1713449575', 2),
(651, '1718366436', 2),
(216, '1720357209', 2),
(1269, '1721608675', 2),
(1042, '1721812582', 2),
(815, '1722527262', 2),
(484, '1722715081', 2),
(490, '1722821319', 2),
(182, '1723135321', 2),
(593, '1723300107', 2),
(601, '1723536056', 2),
(1147, '1724178544', 2),
(222, '1724191224', 2),
(608, '1724241243', 2),
(199, '1724325251', 2),
(483, '1724641020', 2),
(51, '1724989320', 2),
(864, '1725270035', 2),
(1298, '1725370850', 2),
(1119, '1725654535', 2),
(709, '1725887366', 2),
(571, '1726388273', 2),
(584, '1726972043', 2),
(227, '1727144857', 2),
(444, '1727558247', 2),
(560, '1750153015', 2),
(1095, '1750307694', 2),
(1240, '1750315796', 2),
(274, '1750827832', 2),
(598, '1751167055', 2),
(941, '1751201045', 2),
(1831, '1751484161', 2),
(826, '1751669514', 2),
(835, '1752220739', 2),
(253, '1752838514', 2),
(830, '1753858792', 2),
(1419, '1753863628', 2),
(659, '1753887031', 2),
(700, '1755397492', 2),
(1378, '2200013445', 2),
(1277, '2300573678', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1868;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
