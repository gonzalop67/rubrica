-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 01-09-2025 a las 11:57:43
-- Versión del servidor: 10.11.13-MariaDB-cll-lve
-- Versión de PHP: 8.3.19

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
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(45) NOT NULL,
  `in_telefono1` varchar(64) NOT NULL,
  `in_regimen` varchar(45) NOT NULL,
  `in_nom_rector` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `in_genero_rector` varchar(1) NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_genero_vicerrector` varchar(1) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL,
  `in_genero_secretario` varchar(1) NOT NULL,
  `in_url` varchar(64) NOT NULL,
  `in_logo` varchar(64) NOT NULL,
  `in_amie` varchar(16) NOT NULL,
  `in_distrito` varchar(16) NOT NULL,
  `in_ciudad` varchar(64) NOT NULL,
  `in_copiar_y_pegar` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_regimen`, `in_nom_rector`, `in_genero_rector`, `in_nom_vicerrector`, `in_genero_vicerrector`, `in_nom_secretario`, `in_genero_secretario`, `in_url`, `in_logo`, `in_amie`, `in_distrito`, `in_ciudad`, `in_copiar_y_pegar`) VALUES
(1, 'UNIDAD EDUCATIVA SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256311/2254818', 'SIERRA', 'MSc. Wilson Proaño', 'M', 'Lic. Rómulo Mejía', 'M', 'MSc. Irene Chicaiza', 'F', 'http://colegionocturnosalamanca.com', '2142456495.jpg', '17H00215', '', 'Quito D.M.', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
