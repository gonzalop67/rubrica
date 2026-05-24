-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 25-10-2024 a las 14:16:54
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
-- Estructura de tabla para la tabla `sw_def_genero`
--

CREATE TABLE `sw_def_genero` (
  `id_def_genero` int(11) NOT NULL,
  `dg_nombre` varchar(50) NOT NULL,
  `dg_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_def_genero`
--

INSERT INTO `sw_def_genero` (`id_def_genero`, `dg_nombre`, `dg_abreviatura`) VALUES
(1, 'Femenino', 'F'),
(2, 'Masculino', 'M');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  ADD PRIMARY KEY (`id_def_genero`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  MODIFY `id_def_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
