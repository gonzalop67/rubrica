-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-10-2025 a las 13:27:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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

--
-- Volcado de datos para la tabla `sw_sub_periodo_evaluacion`
--

INSERT INTO `sw_sub_periodo_evaluacion` (`id_sub_periodo_evaluacion`, `id_tipo_periodo`, `pe_nombre`, `pe_abreviatura`, `pe_ponderacion`, `pe_orden`) VALUES
(1, 1, 'PRIMER TRIMESTRE', '1ER.T.', 0.33, 1),
(2, 1, 'SEGUNDO TRIMESTRE', '2DO.T.', 0.3, 2),
(3, 1, 'TERCER TRIMESTRE', '3ER.T.', 0.3, 3),
(4, 6, 'PROYECTO INTEGRADOR', 'PRI.', 0.1, 6),
(5, 2, 'EXAMEN SUPLETORIO', 'SUP.', 0, 7),
(6, 1, 'PRIMER BIMESTRE', '1ER. B.', 0, 4),
(7, 1, 'SEGUNDO BIMESTRE', '2DO. B.', 0, 5),
(8, 5, 'REFUERZO ACADEMICO', 'REF.', 0, 8);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
