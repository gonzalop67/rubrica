-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 18-06-2022 a las 13:39:40
-- Versión del servidor: 10.3.34-MariaDB-cll-lve
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
-- Estructura de tabla para la tabla `sw_curso_superior`
--

CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `cs_nombre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_curso_superior`
--

INSERT INTO `sw_curso_superior` (`id_curso_superior`, `cs_nombre`) VALUES
(2, 'NOVENO AÑO DE EDUCACION GENERAL BASICA'),
(3, 'DECIMO AÑO DE EDUCACION GENERAL BASICA'),
(4, 'PRIMER AÑO DE BACHILLERATO'),
(5, 'SEGUNDO AÑO DE BACHILLERATO'),
(6, 'TERCER AÑO DE BACHILLERATO'),
(7, 'SEGUNDO CURSO DE BACHILLERATO GENERAL UNIFICADO.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  ADD PRIMARY KEY (`id_curso_superior`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
