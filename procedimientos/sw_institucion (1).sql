-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 01-04-2023 a las 02:58:45
-- Versión del servidor: 10.3.35-MariaDB-cll-lve
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
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(45) NOT NULL,
  `in_telefono1` varchar(64) NOT NULL,
  `in_regimen` varchar(45) NOT NULL,
  `in_nom_rector` varchar(45) CHARACTER SET utf8 NOT NULL,
  `in_genero_rector` varchar(1) NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL,
  `in_genero_secretario` varchar(1) NOT NULL,
  `in_url` varchar(64) NOT NULL,
  `in_logo` varchar(64) NOT NULL,
  `in_amie` varchar(16) NOT NULL,
  `in_distrito` varchar(16) NOT NULL,
  `in_ciudad` varchar(64) NOT NULL,
  `in_copiar_y_pegar` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_regimen`, `in_nom_rector`, `in_genero_rector`, `in_nom_vicerrector`, `in_nom_secretario`, `in_genero_secretario`, `in_url`, `in_logo`, `in_amie`, `in_distrito`, `in_ciudad`, `in_copiar_y_pegar`) VALUES
(1, 'UNIDAD EDUCATIVA PCEI FISCAL SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256311/2254818', 'SIERRA', 'MSc. Wilson Proaño', 'M', 'Lic. Rómulo Mejía', 'MSc. Verónica Sanmartín', 'F', 'http://colegionocturnosalamanca.com', '1474709991.gif', '17H00215', '17D05', 'Quito D.M.', 0);

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
