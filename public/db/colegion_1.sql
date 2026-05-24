-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2022 a las 18:09:55
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
-- Base de datos: `colegion_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_evaluacion`
--

CREATE TABLE `sw_aporte_evaluacion` (
  `id_aporte_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_tipo_aporte` int(11) UNSIGNED NOT NULL,
  `ap_nombre` varchar(24) NOT NULL,
  `ap_shortname` varchar(45) NOT NULL,
  `ap_abreviatura` varchar(8) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_aporte_evaluacion`
--

INSERT INTO `sw_aporte_evaluacion` (`id_aporte_evaluacion`, `id_periodo_evaluacion`, `id_tipo_aporte`, `ap_nombre`, `ap_shortname`, `ap_abreviatura`, `ap_fecha_apertura`, `ap_fecha_cierre`) VALUES
(1, 1, 1, 'PRIMER PARCIAL', '', '1ER.P.', '2021-09-01', '2021-11-05'),
(2, 1, 1, 'SEGUNDO PARCIAL', '', '2DO.P.', '2021-11-08', '2022-01-14'),
(3, 1, 2, 'PROYECTO QUIMESTRAL', '', 'PROY.', '2022-01-17', '2022-01-28'),
(4, 2, 1, 'TERCER PARCIAL', '', '3ER.P.', '2022-02-03', '2022-04-08'),
(5, 2, 1, 'CUARTO PARCIAL', '', '4TO.P.', '2022-04-11', '2022-06-17'),
(6, 2, 2, 'PROYECTO QUIMESTRAL', '', 'PROY.', '2022-06-20', '2022-07-01'),
(7, 3, 3, 'EXAMEN SUPLETORIO', '', 'SUP.', '2022-07-18', '2022-07-22'),
(8, 4, 3, 'EXAMEN REMEDIAL', '', 'REM.', '2022-08-22', '2022-08-26'),
(9, 5, 3, 'EXAMEN DE GRACIA', '', 'GRA.', '2022-08-29', '2022-08-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_paralelo_cierre`
--

CREATE TABLE `sw_aporte_paralelo_cierre` (
  `id_aporte_paralelo_cierre` int(11) UNSIGNED NOT NULL,
  `id_aporte_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_estado` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_aporte_paralelo_cierre`
--

INSERT INTO `sw_aporte_paralelo_cierre` (`id_aporte_paralelo_cierre`, `id_aporte_evaluacion`, `id_paralelo`, `ap_fecha_apertura`, `ap_fecha_cierre`, `ap_estado`) VALUES
(1, 1, 1, '2021-09-01', '2022-08-01', 'A'),
(2, 1, 2, '2021-09-01', '2022-08-01', 'A'),
(3, 1, 3, '2021-09-01', '2022-08-01', 'A'),
(4, 1, 4, '2021-09-01', '2022-08-01', 'A'),
(5, 1, 5, '2021-09-01', '2022-08-01', 'A'),
(6, 1, 6, '2021-09-01', '2022-08-01', 'A'),
(7, 1, 7, '2021-09-01', '2022-08-01', 'A'),
(8, 1, 8, '2021-09-01', '2022-08-01', 'A'),
(9, 1, 9, '2021-09-01', '2022-08-01', 'A'),
(10, 1, 10, '2021-09-01', '2022-08-01', 'A'),
(11, 1, 11, '2021-09-01', '2022-08-01', 'A'),
(12, 1, 12, '2021-09-01', '2022-08-01', 'A'),
(13, 1, 13, '2021-09-01', '2022-08-01', 'A'),
(14, 1, 14, '2021-09-01', '2022-08-01', 'A'),
(15, 1, 15, '2021-09-01', '2022-08-01', 'A'),
(16, 2, 1, '2021-11-08', '2022-08-01', 'A'),
(17, 2, 2, '2021-11-08', '2022-08-01', 'A'),
(18, 2, 3, '2021-11-08', '2022-08-01', 'A'),
(19, 2, 4, '2021-11-08', '2022-08-01', 'A'),
(20, 2, 5, '2021-11-08', '2022-08-01', 'A'),
(21, 2, 6, '2021-11-08', '2022-08-01', 'A'),
(22, 2, 7, '2021-11-08', '2022-08-01', 'A'),
(23, 2, 8, '2021-11-08', '2022-08-01', 'A'),
(24, 2, 9, '2021-11-08', '2022-08-01', 'A'),
(25, 2, 10, '2021-11-08', '2022-08-01', 'A'),
(26, 2, 11, '2021-11-08', '2022-08-01', 'A'),
(27, 2, 12, '2021-11-08', '2022-08-01', 'A'),
(28, 2, 13, '2021-11-08', '2022-08-01', 'A'),
(29, 2, 14, '2021-11-08', '2022-08-01', 'A'),
(30, 2, 15, '2021-11-08', '2022-08-01', 'A'),
(31, 3, 1, '2022-01-17', '2022-01-28', 'C'),
(32, 3, 2, '2022-01-17', '2022-01-28', 'C'),
(33, 3, 3, '2022-01-17', '2022-01-28', 'C'),
(34, 3, 4, '2022-01-17', '2022-01-28', 'C'),
(35, 3, 5, '2022-01-17', '2022-01-28', 'C'),
(36, 3, 6, '2022-01-17', '2022-01-28', 'C'),
(37, 3, 7, '2022-01-17', '2022-01-28', 'C'),
(38, 3, 8, '2022-01-17', '2022-01-28', 'C'),
(39, 3, 9, '2022-01-17', '2022-01-28', 'C'),
(40, 3, 10, '2022-01-17', '2022-01-28', 'C'),
(41, 3, 11, '2022-01-17', '2022-01-28', 'C'),
(42, 3, 12, '2022-01-17', '2022-01-28', 'C'),
(43, 3, 13, '2022-01-17', '2022-01-28', 'C'),
(44, 3, 14, '2022-01-17', '2022-01-28', 'C'),
(45, 3, 15, '2022-01-17', '2022-01-28', 'C'),
(46, 4, 1, '2022-02-03', '2022-08-01', 'A'),
(47, 4, 2, '2022-02-03', '2022-08-01', 'A'),
(48, 4, 3, '2022-02-03', '2022-08-01', 'A'),
(49, 4, 4, '2022-02-03', '2022-08-01', 'A'),
(50, 4, 5, '2022-02-03', '2022-08-01', 'A'),
(51, 4, 6, '2022-02-03', '2022-08-01', 'A'),
(52, 4, 7, '2022-02-03', '2022-08-01', 'A'),
(53, 4, 8, '2022-02-03', '2022-08-01', 'A'),
(54, 4, 9, '2022-02-03', '2022-08-01', 'A'),
(55, 4, 10, '2022-02-03', '2022-08-01', 'A'),
(56, 4, 11, '2022-02-03', '2022-08-01', 'A'),
(57, 4, 12, '2022-02-03', '2022-08-01', 'A'),
(58, 4, 13, '2022-02-03', '2022-08-01', 'A'),
(59, 4, 14, '2022-02-03', '2022-08-01', 'A'),
(60, 4, 15, '2022-02-03', '2022-08-01', 'A'),
(61, 5, 1, '2022-04-11', '2022-08-01', 'A'),
(62, 5, 2, '2022-04-11', '2022-08-01', 'A'),
(63, 5, 3, '2022-04-11', '2022-08-01', 'A'),
(64, 5, 4, '2022-04-11', '2022-08-01', 'A'),
(65, 5, 5, '2022-04-11', '2022-08-01', 'A'),
(66, 5, 6, '2022-04-11', '2022-08-01', 'A'),
(67, 5, 7, '2022-04-11', '2022-08-01', 'A'),
(68, 5, 8, '2022-04-11', '2022-08-01', 'A'),
(69, 5, 9, '2022-04-11', '2022-08-01', 'A'),
(70, 5, 10, '2022-04-11', '2022-08-01', 'A'),
(71, 5, 11, '2022-04-11', '2022-08-01', 'A'),
(72, 5, 12, '2022-04-11', '2022-08-01', 'A'),
(73, 5, 13, '2022-04-11', '2022-08-01', 'A'),
(74, 5, 14, '2022-04-11', '2022-08-01', 'A'),
(75, 5, 15, '2022-04-11', '2022-08-01', 'A'),
(76, 6, 1, '2022-06-20', '2022-07-01', 'C'),
(77, 6, 2, '2022-06-20', '2022-07-01', 'C'),
(78, 6, 3, '2022-06-20', '2022-07-01', 'C'),
(79, 6, 4, '2022-06-20', '2022-07-01', 'C'),
(80, 6, 5, '2022-06-20', '2022-07-01', 'C'),
(81, 6, 6, '2022-06-20', '2022-07-01', 'C'),
(82, 6, 7, '2022-06-20', '2022-07-01', 'C'),
(83, 6, 8, '2022-06-20', '2022-07-01', 'A'),
(84, 6, 9, '2022-06-20', '2022-07-01', 'A'),
(85, 6, 10, '2022-06-20', '2022-07-01', 'C'),
(86, 6, 11, '2022-06-20', '2022-07-01', 'C'),
(87, 6, 12, '2022-06-20', '2022-07-01', 'C'),
(88, 6, 13, '2022-06-20', '2022-07-01', 'C'),
(89, 6, 14, '2022-06-20', '2022-07-01', 'C'),
(90, 6, 15, '2022-06-20', '2022-07-01', 'C'),
(91, 7, 1, '2022-07-20', '2022-07-22', 'C'),
(92, 7, 2, '2022-07-20', '2022-07-22', 'C'),
(93, 7, 3, '2022-07-20', '2022-07-22', 'A'),
(94, 7, 4, '2022-07-20', '2022-07-22', 'A'),
(95, 7, 5, '2022-07-20', '2022-07-22', 'C'),
(96, 7, 6, '2022-07-20', '2022-07-22', 'C'),
(97, 7, 7, '2022-07-20', '2022-07-22', 'C'),
(98, 7, 8, '2022-07-06', '2022-07-09', 'C'),
(99, 7, 9, '2022-07-06', '2022-07-09', 'C'),
(100, 7, 10, '2022-07-20', '2022-07-22', 'C'),
(101, 7, 11, '2022-07-20', '2022-07-22', 'C'),
(102, 7, 12, '2022-07-06', '2022-07-09', 'A'),
(103, 7, 13, '2022-07-20', '2022-07-22', 'C'),
(104, 7, 14, '2022-07-20', '2022-07-22', 'C'),
(105, 7, 15, '2022-07-06', '2022-07-09', 'A'),
(106, 8, 1, '2022-08-22', '2022-08-26', 'C'),
(107, 8, 2, '2022-08-22', '2022-08-26', 'C'),
(108, 8, 3, '2022-08-22', '2022-08-26', 'C'),
(109, 8, 4, '2022-08-22', '2022-08-26', 'C'),
(110, 8, 5, '2022-08-22', '2022-08-26', 'C'),
(111, 8, 6, '2022-08-22', '2022-08-26', 'C'),
(112, 8, 7, '2022-08-22', '2022-08-26', 'C'),
(113, 8, 8, '2022-08-22', '2022-08-26', 'C'),
(114, 8, 9, '2022-08-22', '2022-08-26', 'C'),
(115, 8, 10, '2022-08-22', '2022-08-26', 'C'),
(116, 8, 11, '2022-08-22', '2022-08-26', 'C'),
(117, 8, 12, '2022-08-22', '2022-08-26', 'C'),
(118, 8, 13, '2022-08-22', '2022-08-26', 'C'),
(119, 8, 14, '2022-08-22', '2022-08-26', 'C'),
(120, 8, 15, '2022-08-22', '2022-08-26', 'C'),
(121, 9, 1, '2022-08-29', '2022-08-31', 'C'),
(122, 9, 2, '2022-08-29', '2022-08-31', 'C'),
(123, 9, 3, '2022-08-29', '2022-08-31', 'C'),
(124, 9, 4, '2022-08-29', '2022-08-31', 'C'),
(125, 9, 5, '2022-08-29', '2022-08-31', 'C'),
(126, 9, 6, '2022-08-29', '2022-08-31', 'C'),
(127, 9, 7, '2022-08-29', '2022-08-31', 'C'),
(128, 9, 8, '2022-08-29', '2022-08-31', 'C'),
(129, 9, 9, '2022-08-29', '2022-08-31', 'C'),
(130, 9, 10, '2022-08-29', '2022-08-31', 'C'),
(131, 9, 11, '2022-08-29', '2022-08-31', 'C'),
(132, 9, 12, '2022-08-29', '2022-08-31', 'C'),
(133, 9, 13, '2022-08-29', '2022-08-31', 'C'),
(134, 9, 14, '2022-08-29', '2022-08-31', 'C'),
(135, 9, 15, '2022-08-29', '2022-08-31', 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_area`
--

CREATE TABLE `sw_area` (
  `id_area` int(11) UNSIGNED NOT NULL,
  `ar_nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_area`
--

INSERT INTO `sw_area` (`id_area`, `ar_nombre`) VALUES
(1, 'CIENCIAS NATURALES'),
(2, 'CIENCIAS SOCIALES'),
(3, 'EDUCACION CULTURAL Y ARTISTICA'),
(4, 'EDUCACION FISICA'),
(5, 'LENGUA EXTRANJERA'),
(6, 'LENGUA Y LITERATURA'),
(7, 'MATEMATICA'),
(8, 'MODULO INTER-ÁREAS'),
(10, 'CONTABILIDAD'),
(11, 'INFORMATICA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura`
--

CREATE TABLE `sw_asignatura` (
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `id_area` int(11) UNSIGNED NOT NULL,
  `id_tipo_asignatura` int(11) UNSIGNED NOT NULL,
  `as_nombre` varchar(84) NOT NULL,
  `as_abreviatura` varchar(12) NOT NULL,
  `as_shortname` varchar(45) NOT NULL,
  `as_curricular` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sw_asignatura`
--

INSERT INTO `sw_asignatura` (`id_asignatura`, `id_area`, `id_tipo_asignatura`, `as_nombre`, `as_abreviatura`, `as_shortname`, `as_curricular`) VALUES
(1, 1, 1, 'BIOLOGIA', 'BIO', '', 1),
(2, 1, 1, 'CIENCIAS NATURALES', 'CCNN', '', 1),
(3, 1, 1, 'FISICA', 'FIS', '', 1),
(4, 1, 1, 'QUIMICA', 'QUIM', '', 1),
(5, 2, 1, 'EDUCACION PARA LA CIUDADANIA', 'EDU.C.', '', 1),
(6, 2, 1, 'ESTUDIOS SOCIALES', 'EESS', '', 1),
(7, 2, 1, 'FILOSOFIA', 'FILO', '', 1),
(8, 2, 1, 'HISTORIA', 'HIST', '', 1),
(9, 10, 1, 'CONTABILIDAD DE COSTOS', 'COSTOS', '', 1),
(10, 10, 1, 'CONTABILIDAD GENERAL', 'CONTA', '', 1),
(11, 10, 1, 'GESTION DEL TALENTO HUMANO', 'G.TAL.H.', '', 1),
(12, 10, 1, 'PAQUETES CONTABLES Y TRIBUTARIOS', 'PAQ.CON.', '', 1),
(13, 10, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', '', 1),
(14, 10, 1, 'TRIBUTACION', 'TRIB.', '', 1),
(15, 10, 1, 'CONTABILIDAD BANCARIA', 'CON.BAN.', '', 1),
(16, 3, 1, 'EDUCACION CULTURAL Y ARTISTICA', 'ECA', '', 1),
(17, 11, 1, 'APLICACIONES OFIMATICAS LOCALES Y EN LINEA', 'APL.OF.', '', 1),
(18, 11, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', '', 1),
(19, 11, 1, 'PROGRAMACION Y BASES DE DATOS', 'PROG.', '', 1),
(20, 11, 1, 'SISTEMAS OPERATIVOS Y REDES', 'SIS.OP.', '', 1),
(21, 11, 1, 'SOPORTE TECNICO', 'SOP.TEC.', '', 1),
(22, 6, 1, 'LENGUA Y LITERATURA', 'LENGUA', '', 1),
(23, 7, 1, 'MATEMATICA', 'MATE', '', 1),
(24, 8, 1, 'EMPRENDIMIENTO Y GESTION', 'EMPRE', '', 1),
(25, 5, 1, 'INGLES', 'ING', '', 1),
(26, 11, 1, 'DISEÑO Y DESARROLLO WEB', 'DIS.WEB', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura_curso`
--

CREATE TABLE `sw_asignatura_curso` (
  `id_asignatura_curso` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `ac_orden` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_asignatura_curso`
--

INSERT INTO `sw_asignatura_curso` (`id_asignatura_curso`, `id_periodo_lectivo`, `id_curso`, `id_asignatura`, `ac_orden`) VALUES
(1, 1, 1, 22, 1),
(2, 1, 1, 23, 2),
(3, 1, 1, 6, 3),
(4, 1, 1, 2, 4),
(5, 1, 1, 16, 5),
(6, 1, 1, 25, 6),
(7, 1, 2, 2, 4),
(8, 1, 2, 6, 3),
(9, 1, 2, 16, 5),
(10, 1, 2, 25, 6),
(12, 1, 2, 23, 2),
(13, 1, 2, 22, 1),
(14, 1, 3, 22, 1),
(15, 1, 3, 23, 2),
(16, 1, 3, 6, 3),
(17, 1, 3, 2, 4),
(18, 1, 3, 16, 5),
(19, 1, 3, 25, 6),
(20, 1, 4, 23, 1),
(21, 1, 4, 3, 2),
(22, 1, 4, 4, 3),
(23, 1, 4, 1, 4),
(24, 1, 4, 8, 5),
(25, 1, 4, 5, 6),
(26, 1, 4, 7, 7),
(27, 1, 4, 22, 8),
(28, 1, 4, 25, 9),
(29, 1, 4, 24, 10),
(30, 1, 5, 23, 1),
(31, 1, 5, 3, 2),
(32, 1, 5, 4, 3),
(33, 1, 5, 1, 4),
(34, 1, 5, 8, 5),
(35, 1, 5, 5, 6),
(36, 1, 5, 7, 7),
(37, 1, 5, 22, 8),
(39, 1, 5, 25, 9),
(40, 1, 5, 24, 10),
(41, 1, 6, 23, 1),
(42, 1, 6, 3, 2),
(43, 1, 6, 4, 3),
(44, 1, 6, 1, 4),
(45, 1, 6, 8, 5),
(46, 1, 6, 22, 6),
(47, 1, 6, 25, 7),
(48, 1, 6, 24, 8),
(49, 1, 7, 23, 1),
(50, 1, 7, 3, 2),
(51, 1, 7, 4, 3),
(52, 1, 7, 1, 4),
(53, 1, 7, 8, 5),
(54, 1, 7, 5, 6),
(55, 1, 7, 7, 7),
(56, 1, 7, 22, 8),
(57, 1, 7, 25, 9),
(58, 1, 7, 24, 10),
(59, 1, 7, 10, 11),
(60, 1, 7, 14, 12),
(61, 1, 7, 12, 13),
(62, 1, 8, 23, 1),
(63, 1, 8, 3, 2),
(64, 1, 8, 4, 3),
(65, 1, 8, 1, 4),
(66, 1, 8, 8, 5),
(67, 1, 8, 5, 6),
(68, 1, 8, 7, 7),
(69, 1, 8, 22, 8),
(70, 1, 8, 25, 9),
(71, 1, 8, 24, 10),
(72, 1, 8, 10, 11),
(73, 1, 8, 14, 12),
(74, 1, 8, 12, 13),
(75, 1, 9, 23, 1),
(76, 1, 9, 3, 2),
(77, 1, 9, 4, 3),
(78, 1, 9, 1, 4),
(79, 1, 9, 8, 5),
(80, 1, 9, 22, 6),
(81, 1, 9, 25, 7),
(82, 1, 9, 24, 8),
(83, 1, 9, 10, 9),
(84, 1, 9, 9, 10),
(85, 1, 9, 15, 11),
(86, 1, 9, 11, 12),
(87, 1, 9, 12, 13),
(88, 1, 9, 13, 14),
(89, 1, 10, 23, 1),
(90, 1, 10, 3, 2),
(91, 1, 10, 4, 3),
(92, 1, 10, 1, 4),
(93, 1, 10, 8, 5),
(94, 1, 10, 5, 6),
(95, 1, 10, 7, 7),
(96, 1, 10, 22, 8),
(97, 1, 10, 25, 9),
(98, 1, 10, 24, 10),
(99, 1, 10, 17, 11),
(100, 1, 10, 20, 12),
(101, 1, 10, 19, 13),
(102, 1, 10, 21, 14),
(103, 1, 11, 23, 1),
(104, 1, 11, 3, 2),
(105, 1, 11, 4, 3),
(106, 1, 11, 1, 4),
(107, 1, 11, 8, 5),
(108, 1, 11, 5, 6),
(109, 1, 11, 7, 7),
(110, 1, 11, 22, 8),
(111, 1, 11, 25, 9),
(112, 1, 11, 24, 10),
(113, 1, 11, 20, 11),
(114, 1, 11, 19, 12),
(115, 1, 11, 21, 13),
(116, 1, 11, 26, 14),
(117, 1, 12, 23, 1),
(118, 1, 12, 3, 2),
(119, 1, 12, 4, 3),
(120, 1, 12, 1, 4),
(121, 1, 12, 8, 5),
(122, 1, 12, 22, 6),
(123, 1, 12, 25, 7),
(124, 1, 12, 24, 8),
(125, 1, 12, 17, 9),
(126, 1, 12, 20, 10),
(127, 1, 12, 19, 11),
(128, 1, 12, 21, 12),
(129, 1, 12, 26, 13),
(130, 1, 12, 18, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asociar_curso_superior`
--

CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso_inferior` int(11) UNSIGNED NOT NULL,
  `id_curso_superior` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_asociar_curso_superior`
--

INSERT INTO `sw_asociar_curso_superior` (`id_asociar_curso_superior`, `id_periodo_lectivo`, `id_curso_inferior`, `id_curso_superior`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 3),
(3, 1, 3, 4),
(4, 1, 4, 5),
(5, 1, 5, 6),
(6, 1, 7, 5),
(7, 1, 8, 6),
(8, 1, 10, 5),
(9, 1, 11, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comentario`
--

CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_comentario_padre` int(11) NOT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) NOT NULL,
  `co_nombre` varchar(64) NOT NULL,
  `co_texto` varchar(250) NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_criterio_estudiante`
--

CREATE TABLE `sw_criterio_estudiante` (
  `id_criterio_estudiante` int(11) NOT NULL,
  `id_rubrica_estudiante` int(11) NOT NULL,
  `id_criterio_personalizado` int(11) NOT NULL,
  `ce_calificacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_criterio_evaluacion`
--

CREATE TABLE `sw_criterio_evaluacion` (
  `id_criterio_evaluacion` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `cr_descripcion` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cr_ponderacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_criterio_evaluacion`
--

INSERT INTO `sw_criterio_evaluacion` (`id_criterio_evaluacion`, `id_rubrica_evaluacion`, `cr_descripcion`, `cr_ponderacion`) VALUES
(1, 1, 'PRESENTACION OPORTUNA', 0.25),
(2, 1, 'CONTENIDO DEL TRABAJO', 0.25),
(3, 1, 'ORDEN', 0.25),
(4, 1, 'MATERIAL UTILIZADO', 0.25),
(5, 2, 'ENFOQUE', 0.25),
(7, 2, 'ORGANIZACION', 0.25),
(8, 2, 'CUADROS', 0.25),
(9, 2, 'CREATIVIDAD', 0.25),
(10, 3, 'PRESENTACION', 0.25),
(11, 3, 'CONTENIDO DEL TRABAJO', 0.25),
(12, 3, 'ORGANIZACION', 0.25),
(13, 3, 'MATERIAL UTILIZADO', 0.25),
(14, 4, 'PRESENTACION', 0.2),
(15, 4, 'CONTENIDOS', 0.4),
(16, 4, 'ENTONACION', 0.2),
(17, 4, 'USO DE PAUSAS', 0.2),
(18, 5, 'CONOCIMIENTOS', 1),
(19, 6, 'PRESENTACION OPORTUNA', 0.25),
(20, 6, 'CONTENIDO DEL TRABAJO', 0.25),
(21, 6, 'ORDEN', 0.25),
(22, 6, 'MATERIAL UTILIZADO', 0.25),
(23, 7, 'ENFOQUE', 0.25),
(25, 7, 'ORGANIZACION', 0.25),
(26, 7, 'CUADROS', 0.25),
(27, 7, 'CREATIVIDAD', 0.25),
(28, 8, 'PRESENTACION', 0.25),
(29, 8, 'CONTENIDO DEL TRABAJO', 0.25),
(30, 8, 'ORGANIZACION', 0.25),
(31, 8, 'MATERIAL UTILIZADO', 0.25),
(32, 9, 'PRESENTACION', 0.2),
(33, 9, 'CONTENIDOS', 0.4),
(34, 9, 'ENTONACION', 0.2),
(35, 9, 'USO DE PAUSAS', 0.2),
(36, 10, 'CONOCIMIENTOS', 1),
(37, 11, 'PRESENTACION OPORTUNA', 0.25),
(38, 11, 'CONTENIDO DEL TRABAJO', 0.25),
(39, 11, 'ORDEN', 0.25),
(40, 11, 'MATERIAL UTILIZADO', 0.25),
(41, 12, 'ENFOQUE', 0.25),
(43, 12, 'ORGANIZACION', 0.25),
(44, 12, 'CUADROS', 0.25),
(45, 12, 'CREATIVIDAD', 0.25),
(46, 13, 'PRESENTACION', 0.25),
(47, 13, 'CONTENIDO DEL TRABAJO', 0.25),
(48, 13, 'ORGANIZACION', 0.25),
(49, 13, 'MATERIAL UTILIZADO', 0.25),
(50, 14, 'PRESENTACION', 0.2),
(51, 14, 'CONTENIDOS', 0.4),
(52, 14, 'ENTONACION', 0.2),
(53, 14, 'USO DE PAUSAS', 0.2),
(54, 15, 'CONOCIMIENTOS', 1),
(55, 16, 'CONOCIMIENTOS', 1),
(56, 17, 'PRESENTACION OPORTUNA', 0.25),
(57, 17, 'CONTENIDO DEL TRABAJO', 0.25),
(58, 17, 'ORDEN', 0.25),
(59, 17, 'MATERIAL UTILIZADO', 0.25),
(60, 18, 'ENFOQUE', 0.25),
(62, 18, 'ORGANIZACION', 0.25),
(63, 18, 'CUADROS', 0.25),
(64, 18, 'CREATIVIDAD', 0.25),
(65, 19, 'PRESENTACION', 0.25),
(66, 19, 'CONTENIDO DEL TRABAJO', 0.25),
(67, 19, 'ORGANIZACION', 0.25),
(68, 19, 'MATERIAL UTILIZADO', 0.25),
(69, 20, 'PRESENTACION', 0.2),
(70, 20, 'CONTENIDOS', 0.4),
(71, 20, 'ENTONACION', 0.2),
(72, 20, 'USO DE PAUSAS', 0.2),
(73, 21, 'CONOCIMIENTOS', 1),
(74, 22, 'PRESENTACION OPORTUNA', 0.25),
(75, 22, 'CONTENIDO DEL TRABAJO', 0.25),
(76, 22, 'ORDEN', 0.25),
(77, 22, 'MATERIAL UTILIZADO', 0.25),
(78, 23, 'ENFOQUE', 0.25),
(80, 23, 'ORGANIZACION', 0.25),
(81, 23, 'CUADROS', 0.25),
(82, 23, 'CREATIVIDAD', 0.25),
(83, 24, 'PRESENTACION', 0.25),
(84, 24, 'CONTENIDO DEL TRABAJO', 0.25),
(85, 24, 'ORGANIZACION', 0.25),
(86, 24, 'MATERIAL UTILIZADO', 0.25),
(87, 25, 'PRESENTACION', 0.2),
(88, 25, 'CONTENIDOS', 0.4),
(89, 25, 'ENTONACION', 0.2),
(90, 25, 'USO DE PAUSAS', 0.2),
(91, 26, 'CONOCIMIENTOS', 1),
(92, 27, 'PRESENTACION OPORTUNA', 0.25),
(93, 27, 'CONTENIDO DEL TRABAJO', 0.25),
(94, 27, 'ORDEN', 0.25),
(95, 27, 'MATERIAL UTILIZADO', 0.25),
(96, 28, 'ENFOQUE', 0.25),
(98, 28, 'ORGANIZACION', 0.25),
(99, 28, 'CUADROS', 0.25),
(100, 28, 'CREATIVIDAD', 0.25),
(101, 29, 'PRESENTACION', 0.25),
(102, 29, 'CONTENIDO DEL TRABAJO', 0.25),
(103, 29, 'ORGANIZACION', 0.25),
(104, 29, 'MATERIAL UTILIZADO', 0.25),
(105, 30, 'PRESENTACION', 0.2),
(106, 30, 'CONTENIDOS', 0.4),
(107, 30, 'ENTONACION', 0.2),
(108, 30, 'USO DE PAUSAS', 0.2),
(109, 31, 'CONOCIMIENTOS', 1),
(110, 32, 'CONOCIMIENTOS', 1),
(111, 33, 'CONOCIMIENTOS', 1),
(112, 34, 'CONOCIMIENTOS', 1),
(147, 35, 'CONOCIMIENTOS', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_criterio_personalizado`
--

CREATE TABLE `sw_criterio_personalizado` (
  `id_criterio_personalizado` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL,
  `cp_descripcion` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cp_ponderacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_criterio_personalizado`
--

INSERT INTO `sw_criterio_personalizado` (`id_criterio_personalizado`, `id_rubrica_personalizada`, `cp_descripcion`, `cp_ponderacion`) VALUES
(1, 1, 'PRESENTACION OPORTUNA', 0.25),
(2, 1, 'CONTENIDO DEL TRABAJO', 0.25),
(3, 1, 'ORDEN', 0.25),
(4, 1, 'MATERIAL UTILIZADO', 0.25),
(5, 2, 'ENFOQUE', 0.25),
(6, 2, 'ORGANIZACION', 0.25),
(7, 2, 'CUADROS', 0.25),
(8, 2, 'CREATIVIDAD', 0.25),
(9, 3, 'PRESENTACION', 0.25),
(10, 3, 'CONTENIDO DEL TRABAJO', 0.25),
(11, 3, 'ORGANIZACION', 0.25),
(12, 3, 'MATERIAL UTILIZADO', 0.25),
(13, 4, 'PRESENTACION', 0.2),
(14, 4, 'CONTENIDOS', 0.4),
(15, 4, 'ENTONACION', 0.2),
(16, 4, 'USO DE PAUSAS', 0.2),
(17, 5, 'CONOCIMIENTOS', 1),
(18, 1, 'PRESENTACION OPORTUNA', 0.25),
(19, 1, 'CONTENIDO DEL TRABAJO', 0.25),
(20, 1, 'ORDEN', 0.25),
(21, 1, 'MATERIAL UTILIZADO', 0.25),
(22, 2, 'ENFOQUE', 0.25),
(23, 2, 'ORGANIZACION', 0.25),
(24, 2, 'CUADROS', 0.25),
(25, 2, 'CREATIVIDAD', 0.25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso`
--

CREATE TABLE `sw_curso` (
  `id_curso` int(11) UNSIGNED NOT NULL,
  `id_especialidad` int(11) UNSIGNED NOT NULL,
  `cu_nombre` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cu_shortname` varchar(45) NOT NULL,
  `cu_abreviatura` varchar(5) NOT NULL,
  `cu_orden` tinyint(4) NOT NULL,
  `quien_inserta_comp` tinyint(4) NOT NULL,
  `es_bach_tecnico` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_curso`
--

INSERT INTO `sw_curso` (`id_curso`, `id_especialidad`, `cu_nombre`, `cu_shortname`, `cu_abreviatura`, `cu_orden`, `quien_inserta_comp`, `es_bach_tecnico`) VALUES
(1, 1, 'OCTAVO', 'OCTAVO', '8vo.', 1, 0, 0),
(2, 1, 'NOVENO', 'NOVENO', '9no.', 2, 0, 0),
(3, 1, 'DECIMO', 'DECIMO', '10mo.', 3, 0, 0),
(4, 2, 'PRIMER CURSO', 'PRIMERO CIENCIAS', '1ero.', 4, 0, 0),
(5, 2, 'SEGUNDO CURSO', 'SEGUNDO CIENCIAS', '2do.', 5, 0, 0),
(6, 2, 'TERCER CURSO', 'TERCERO CIENCIAS', '3ro.', 6, 0, 0),
(7, 3, 'PRIMER CURSO', 'PRIMERO CONTABILIDAD', '1ro.', 7, 0, 1),
(8, 3, 'SEGUNDO CURSO', 'SEGUNDO CONTABILIDAD', '2do.', 8, 0, 1),
(9, 3, 'TERCER CURSO', 'TERCERO CONTABILIDAD', '3ro.', 9, 0, 1),
(10, 4, 'PRIMER CURSO', 'PRIMERO INFORMATICA', '1ro.', 10, 0, 0),
(11, 4, 'SEGUNDO CURSO', 'SEGUNDO INFORMATICA', '2do.', 11, 0, 0),
(12, 4, 'TERCER CURSO', 'TERCERO INFORMATICA', '3ro.', 12, 0, 0);

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
(7, 'SEGUNDO CURSO DE BACHILLERATO GENERAL UNIFICADO.'),
(8, 'TERCER CURSO DE BACHILLERATO GENERAL UNIFICADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_def_genero`
--

CREATE TABLE `sw_def_genero` (
  `id_def_genero` int(11) UNSIGNED NOT NULL,
  `dg_nombre` varchar(50) NOT NULL,
  `dg_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_def_genero`
--

INSERT INTO `sw_def_genero` (`id_def_genero`, `dg_nombre`, `dg_abreviatura`) VALUES
(1, 'Femenino', 'F'),
(2, 'Masculino', 'M');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_def_nacionalidad`
--

CREATE TABLE `sw_def_nacionalidad` (
  `id_def_nacionalidad` int(11) UNSIGNED NOT NULL,
  `dn_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_def_nacionalidad`
--

INSERT INTO `sw_def_nacionalidad` (`id_def_nacionalidad`, `dn_nombre`) VALUES
(1, 'Ecuatoriana'),
(2, 'Colombiana'),
(3, 'Venezolana'),
(4, 'Haitiana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_dia_semana`
--

CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `ds_nombre` varchar(10) NOT NULL,
  `ds_ordinal` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_dia_semana`
--

INSERT INTO `sw_dia_semana` (`id_dia_semana`, `id_periodo_lectivo`, `ds_nombre`, `ds_ordinal`) VALUES
(1, 1, 'LUNES', 1),
(2, 1, 'MARTES', 2),
(3, 1, 'MIERCOLES', 3),
(4, 1, 'JUEVES', 4),
(5, 1, 'VIERNES', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_distributivo`
--

CREATE TABLE `sw_distributivo` (
  `id_distributivo` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_malla_curricular` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_distributivo`
--

INSERT INTO `sw_distributivo` (`id_distributivo`, `id_periodo_lectivo`, `id_malla_curricular`, `id_paralelo`, `id_asignatura`, `id_usuario`) VALUES
(1, 1, 14, 4, 23, 4),
(2, 1, 48, 10, 3, 4),
(3, 1, 61, 11, 3, 4),
(4, 1, 74, 12, 3, 4),
(5, 1, 84, 12, 11, 4),
(6, 1, 86, 12, 13, 4),
(7, 1, 88, 13, 3, 4),
(8, 1, 102, 14, 3, 4),
(9, 1, 116, 15, 3, 4),
(10, 1, 128, 15, 18, 4),
(11, 1, 20, 5, 3, 4),
(12, 1, 30, 6, 3, 4),
(13, 1, 30, 7, 3, 4),
(14, 1, 40, 8, 3, 4),
(15, 1, 40, 9, 3, 4),
(17, 1, 97, 13, 17, 2),
(18, 1, 98, 13, 20, 2),
(19, 1, 99, 13, 19, 2),
(20, 1, 100, 13, 21, 2),
(21, 1, 111, 14, 20, 2),
(22, 1, 112, 14, 19, 2),
(23, 1, 113, 14, 21, 2),
(24, 1, 114, 14, 26, 2),
(25, 1, 123, 15, 17, 2),
(26, 1, 124, 15, 20, 2),
(27, 1, 125, 15, 19, 2),
(28, 1, 126, 15, 21, 2),
(29, 1, 127, 15, 26, 2),
(30, 1, 49, 10, 4, 5),
(31, 1, 50, 10, 1, 5),
(32, 1, 62, 11, 4, 5),
(33, 1, 75, 12, 4, 5),
(34, 1, 76, 12, 1, 5),
(35, 1, 89, 13, 4, 5),
(36, 1, 90, 13, 1, 5),
(37, 1, 103, 14, 4, 5),
(38, 1, 117, 15, 4, 5),
(39, 1, 118, 15, 1, 5),
(40, 1, 21, 5, 4, 5),
(41, 1, 22, 5, 1, 5),
(42, 1, 31, 6, 4, 5),
(43, 1, 31, 7, 4, 5),
(44, 1, 41, 8, 4, 5),
(45, 1, 42, 8, 1, 5),
(46, 1, 41, 9, 4, 5),
(47, 1, 42, 9, 1, 5),
(48, 1, 9, 2, 6, 6),
(49, 1, 15, 3, 6, 6),
(50, 1, 15, 4, 6, 6),
(51, 1, 23, 5, 8, 6),
(52, 1, 25, 5, 7, 6),
(53, 1, 28, 5, 24, 6),
(54, 1, 35, 6, 7, 6),
(55, 1, 35, 7, 7, 6),
(56, 1, 51, 10, 8, 6),
(57, 1, 53, 10, 7, 6),
(58, 1, 66, 11, 7, 6),
(59, 1, 91, 13, 8, 6),
(60, 1, 93, 13, 7, 6),
(61, 1, 96, 13, 24, 6),
(62, 1, 107, 14, 7, 6),
(63, 1, 56, 10, 24, 6),
(64, 1, 3, 1, 6, 8),
(65, 1, 52, 10, 5, 8),
(66, 1, 64, 11, 8, 8),
(67, 1, 65, 11, 5, 8),
(68, 1, 77, 12, 8, 8),
(69, 1, 92, 13, 5, 8),
(70, 1, 105, 14, 8, 8),
(71, 1, 106, 14, 5, 8),
(72, 1, 119, 15, 8, 8),
(73, 1, 24, 5, 5, 8),
(74, 1, 33, 6, 8, 8),
(75, 1, 34, 6, 5, 8),
(76, 1, 33, 7, 8, 8),
(77, 1, 34, 7, 5, 8),
(78, 1, 43, 8, 8, 8),
(79, 1, 43, 9, 8, 8),
(80, 1, 5, 1, 16, 9),
(81, 1, 10, 2, 2, 9),
(82, 1, 11, 2, 16, 9),
(83, 1, 16, 3, 2, 9),
(84, 1, 17, 3, 16, 9),
(85, 1, 32, 6, 1, 9),
(86, 1, 32, 7, 1, 9),
(87, 1, 63, 11, 1, 9),
(88, 1, 104, 14, 1, 9),
(89, 1, 16, 4, 2, 9),
(90, 1, 17, 4, 16, 9),
(91, 1, 2, 1, 23, 10),
(92, 1, 8, 2, 23, 10),
(93, 1, 19, 5, 23, 10),
(94, 1, 29, 6, 23, 10),
(95, 1, 29, 7, 23, 10),
(96, 1, 39, 8, 23, 10),
(97, 1, 39, 9, 23, 10),
(98, 1, 47, 10, 23, 10),
(99, 1, 60, 11, 23, 10),
(100, 1, 73, 12, 23, 10),
(101, 1, 87, 13, 23, 10),
(102, 1, 101, 14, 23, 10),
(103, 1, 115, 15, 23, 10),
(104, 1, 14, 3, 23, 11),
(105, 1, 6, 1, 25, 13),
(106, 1, 12, 2, 25, 13),
(107, 1, 27, 5, 25, 13),
(108, 1, 37, 6, 25, 13),
(109, 1, 37, 7, 25, 13),
(110, 1, 45, 8, 25, 13),
(111, 1, 45, 9, 25, 13),
(112, 1, 55, 10, 25, 13),
(113, 1, 68, 11, 25, 13),
(114, 1, 79, 12, 25, 13),
(115, 1, 95, 13, 25, 13),
(116, 1, 109, 14, 25, 13),
(117, 1, 121, 15, 25, 13),
(118, 1, 1, 1, 22, 14),
(119, 1, 7, 2, 22, 14),
(120, 1, 13, 3, 22, 14),
(121, 1, 13, 4, 22, 14),
(122, 1, 26, 5, 22, 14),
(123, 1, 36, 6, 22, 14),
(124, 1, 36, 7, 22, 14),
(125, 1, 44, 8, 22, 14),
(126, 1, 44, 9, 22, 14),
(127, 1, 54, 10, 22, 14),
(128, 1, 67, 11, 22, 14),
(129, 1, 78, 12, 22, 14),
(130, 1, 94, 13, 22, 14),
(131, 1, 108, 14, 22, 14),
(132, 1, 120, 15, 22, 14),
(133, 1, 18, 3, 25, 15),
(134, 1, 18, 4, 25, 15),
(135, 1, 57, 10, 10, 16),
(136, 1, 58, 10, 14, 16),
(137, 1, 59, 10, 12, 16),
(138, 1, 70, 11, 10, 16),
(139, 1, 71, 11, 14, 16),
(140, 1, 72, 11, 12, 16),
(141, 1, 81, 12, 10, 16),
(142, 1, 82, 12, 9, 16),
(143, 1, 83, 12, 15, 16),
(144, 1, 85, 12, 12, 16),
(145, 1, 4, 1, 2, 17),
(146, 1, 38, 6, 24, 17),
(147, 1, 38, 7, 24, 17),
(148, 1, 46, 8, 24, 17),
(149, 1, 46, 9, 24, 17),
(150, 1, 69, 11, 24, 17),
(151, 1, 80, 12, 24, 17),
(152, 1, 110, 14, 24, 17),
(153, 1, 122, 15, 24, 17);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_especialidad`
--

CREATE TABLE `sw_especialidad` (
  `id_especialidad` int(11) UNSIGNED NOT NULL,
  `id_tipo_educacion` int(11) UNSIGNED NOT NULL,
  `es_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_figura` varchar(50) NOT NULL,
  `es_abreviatura` varchar(15) NOT NULL,
  `es_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_especialidad`
--

INSERT INTO `sw_especialidad` (`id_especialidad`, `id_tipo_educacion`, `es_nombre`, `es_figura`, `es_abreviatura`, `es_orden`) VALUES
(1, 1, 'EDUCACION GENERAL BASICA SUPERIOR', 'EDUCACION GENERAL BASICA SUPERIOR', 'EGBS', 1),
(2, 2, 'BACHILLERATO GENERAL UNIFICADO', 'BACHILLERATO GENERAL UNIFICADO', 'BGU', 2),
(3, 3, 'AREA TECNICA DE SERVICIOS', 'CONTABILIDAD', 'CONTA', 3),
(4, 3, 'AREA TECNICA DE SERVICIOS', 'INFORMATICA', 'INFO', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) UNSIGNED NOT NULL,
  `id_tipo_documento` int(11) UNSIGNED NOT NULL,
  `id_def_genero` int(11) UNSIGNED NOT NULL,
  `id_def_nacionalidad` int(11) UNSIGNED NOT NULL,
  `es_apellidos` varchar(32) NOT NULL,
  `es_nombres` varchar(32) NOT NULL,
  `es_nombre_completo` varchar(64) NOT NULL,
  `es_cedula` varchar(10) NOT NULL,
  `es_email` varchar(64) NOT NULL,
  `es_sector` varchar(36) NOT NULL,
  `es_direccion` varchar(64) NOT NULL,
  `es_telefono` varchar(32) NOT NULL,
  `es_fec_nacim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_periodo_lectivo`
--

CREATE TABLE `sw_estudiante_periodo_lectivo` (
  `id_estudiante_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_estudiante` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `es_estado` char(1) NOT NULL,
  `es_retirado` varchar(1) NOT NULL,
  `nro_matricula` int(11) UNSIGNED NOT NULL,
  `activo` int(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario`
--

CREATE TABLE `sw_horario` (
  `id_horario` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_dia_semana` int(11) UNSIGNED NOT NULL,
  `id_hora_clase` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_horario`
--

INSERT INTO `sw_horario` (`id_horario`, `id_asignatura`, `id_paralelo`, `id_dia_semana`, `id_hora_clase`, `id_usuario`) VALUES
(1, 23, 1, 1, 1, 10),
(2, 2, 1, 1, 2, 17),
(3, 6, 1, 1, 3, 8),
(4, 16, 1, 1, 4, 9),
(10, 2, 1, 3, 1, 17),
(11, 23, 1, 3, 2, 10),
(12, 6, 1, 3, 3, 8),
(13, 25, 1, 3, 4, 13),
(14, 23, 1, 5, 1, 10),
(15, 6, 1, 5, 2, 8),
(16, 22, 1, 5, 3, 14),
(17, 25, 1, 5, 4, 13),
(18, 16, 2, 1, 1, 9),
(19, 2, 2, 1, 2, 9),
(20, 23, 2, 1, 3, 10),
(21, 6, 2, 1, 4, 6),
(22, 6, 2, 1, 5, 6),
(23, 25, 2, 3, 1, 13),
(24, 2, 2, 3, 2, 9),
(25, 22, 2, 3, 3, 14),
(26, 23, 2, 3, 4, 10),
(27, 25, 2, 5, 1, 13),
(28, 23, 2, 5, 2, 10),
(29, 6, 2, 5, 3, 6),
(30, 22, 2, 5, 4, 14),
(31, 23, 3, 1, 1, 11),
(32, 23, 3, 1, 2, 11),
(33, 25, 3, 1, 3, 15),
(34, 25, 3, 1, 4, 15),
(35, 16, 3, 3, 1, 9),
(36, 22, 3, 3, 2, 14),
(37, 6, 3, 3, 3, 6),
(38, 6, 3, 3, 4, 6),
(39, 23, 3, 5, 1, 11),
(40, 22, 3, 5, 2, 14),
(41, 2, 3, 5, 3, 9),
(42, 2, 3, 5, 4, 9),
(43, 6, 3, 5, 5, 6),
(44, 25, 4, 1, 1, 15),
(45, 25, 4, 1, 2, 15),
(46, 6, 4, 1, 3, 6),
(47, 23, 4, 1, 4, 4),
(48, 22, 4, 3, 1, 14),
(49, 6, 4, 3, 2, 6),
(50, 23, 4, 3, 3, 4),
(51, 16, 4, 3, 4, 9),
(52, 2, 4, 5, 1, 9),
(53, 2, 4, 5, 2, 9),
(54, 23, 4, 5, 3, 4),
(55, 6, 4, 5, 4, 6),
(56, 22, 4, 5, 5, 14),
(57, 8, 5, 1, 1, 6),
(58, 23, 5, 1, 2, 10),
(59, 1, 5, 1, 3, 5),
(60, 25, 5, 1, 4, 13),
(61, 22, 5, 1, 5, 14),
(62, 25, 5, 2, 1, 13),
(63, 3, 5, 2, 2, 4),
(64, 7, 5, 2, 3, 6),
(65, 5, 5, 2, 4, 8),
(66, 22, 5, 2, 5, 14),
(67, 3, 5, 4, 1, 4),
(68, 24, 5, 4, 2, 6),
(69, 23, 5, 4, 3, 10),
(70, 4, 5, 4, 4, 5),
(71, 4, 5, 4, 5, 5),
(72, 25, 6, 1, 1, 13),
(73, 22, 6, 1, 2, 14),
(74, 1, 6, 1, 3, 9),
(75, 4, 6, 1, 4, 5),
(76, 24, 6, 1, 5, 17),
(77, 3, 6, 2, 1, 4),
(78, 23, 6, 2, 2, 10),
(79, 4, 6, 2, 3, 5),
(80, 7, 6, 2, 4, 6),
(81, 8, 6, 2, 5, 8),
(82, 5, 6, 4, 1, 8),
(83, 22, 6, 4, 2, 14),
(84, 25, 6, 4, 3, 13),
(85, 23, 6, 4, 4, 10),
(86, 3, 6, 4, 5, 4),
(87, 24, 7, 1, 1, 17),
(88, 3, 7, 1, 2, 4),
(89, 1, 7, 1, 3, 9),
(90, 4, 7, 1, 4, 5),
(91, 23, 7, 1, 5, 10),
(92, 22, 7, 2, 1, 14),
(93, 8, 7, 2, 2, 8),
(94, 4, 7, 2, 3, 5),
(95, 23, 7, 2, 4, 10),
(96, 25, 7, 2, 5, 13),
(97, 7, 7, 4, 1, 6),
(98, 25, 7, 4, 2, 13),
(99, 5, 7, 4, 3, 8),
(100, 3, 7, 4, 4, 4),
(101, 22, 7, 4, 5, 14),
(102, 3, 8, 1, 1, 4),
(103, 8, 8, 1, 2, 8),
(104, 22, 8, 1, 3, 14),
(105, 24, 8, 1, 4, 17),
(106, 1, 8, 1, 5, 5),
(107, 4, 8, 2, 1, 5),
(108, 4, 8, 2, 2, 5),
(109, 25, 8, 2, 3, 13),
(110, 22, 8, 2, 4, 14),
(111, 23, 8, 2, 5, 10),
(112, 23, 8, 4, 1, 10),
(113, 8, 8, 4, 2, 8),
(114, 3, 8, 4, 3, 4),
(115, 25, 8, 4, 4, 13),
(116, 4, 9, 1, 1, 5),
(117, 4, 9, 1, 2, 5),
(118, 24, 9, 1, 3, 17),
(119, 22, 9, 1, 4, 14),
(120, 25, 9, 1, 5, 13),
(121, 8, 9, 2, 1, 8),
(122, 25, 9, 2, 2, 13),
(123, 23, 9, 2, 3, 10),
(124, 3, 9, 2, 4, 4),
(125, 1, 9, 3, 1, 5),
(126, 8, 9, 3, 2, 8),
(127, 23, 9, 3, 3, 10),
(128, 3, 9, 3, 4, 4),
(129, 22, 9, 3, 5, 14),
(130, 22, 10, 1, 1, 14),
(131, 8, 10, 1, 2, 6),
(132, 25, 10, 1, 3, 13),
(133, 5, 10, 1, 4, 8),
(134, 3, 10, 1, 5, 4),
(135, 23, 10, 2, 1, 10),
(136, 7, 10, 2, 2, 6),
(137, 10, 10, 2, 3, 16),
(138, 10, 10, 2, 4, 16),
(139, 1, 10, 2, 5, 5),
(140, 3, 10, 3, 1, 4),
(141, 25, 10, 3, 2, 13),
(142, 12, 10, 3, 3, 16),
(143, 12, 10, 3, 4, 16),
(144, 23, 10, 3, 5, 10),
(145, 14, 10, 4, 1, 16),
(146, 14, 10, 4, 2, 16),
(147, 22, 10, 4, 3, 14),
(148, 24, 10, 4, 4, 6),
(149, 10, 10, 5, 1, 16),
(150, 10, 10, 5, 2, 16),
(151, 4, 10, 5, 3, 5),
(152, 4, 10, 5, 4, 5),
(153, 8, 11, 1, 1, 8),
(154, 25, 11, 1, 2, 13),
(155, 3, 11, 1, 3, 4),
(156, 23, 11, 1, 4, 10),
(157, 10, 11, 1, 5, 16),
(158, 7, 11, 2, 1, 6),
(159, 22, 11, 2, 2, 14),
(160, 8, 11, 2, 3, 8),
(161, 1, 11, 2, 4, 9),
(162, 12, 11, 3, 1, 16),
(163, 12, 11, 3, 2, 16),
(164, 4, 11, 3, 3, 5),
(165, 24, 11, 3, 4, 17),
(166, 10, 11, 3, 5, 16),
(167, 22, 11, 4, 1, 14),
(168, 23, 11, 4, 2, 10),
(169, 4, 11, 4, 3, 5),
(170, 10, 11, 4, 4, 16),
(171, 10, 11, 4, 5, 16),
(172, 3, 11, 5, 1, 4),
(173, 25, 11, 5, 2, 13),
(174, 12, 11, 5, 3, 16),
(175, 12, 11, 5, 4, 16),
(176, 10, 11, 5, 5, 16),
(177, 9, 12, 1, 1, 16),
(178, 9, 12, 1, 2, 16),
(179, 10, 12, 1, 3, 16),
(180, 10, 12, 1, 4, 16),
(181, 8, 12, 1, 5, 8),
(182, 15, 12, 2, 1, 16),
(183, 15, 12, 2, 2, 16),
(184, 22, 12, 2, 3, 14),
(185, 25, 12, 2, 4, 13),
(186, 12, 12, 2, 5, 16),
(187, 8, 12, 3, 1, 8),
(188, 3, 12, 3, 2, 4),
(189, 24, 12, 3, 3, 17),
(190, 1, 12, 3, 4, 5),
(191, 11, 12, 3, 5, 4),
(192, 25, 12, 4, 1, 13),
(193, 3, 12, 4, 2, 4),
(195, 10, 12, 4, 3, 16),
(196, 22, 12, 4, 4, 14),
(197, 23, 12, 4, 5, 10),
(198, 4, 12, 5, 1, 5),
(199, 4, 12, 5, 2, 5),
(200, 23, 12, 5, 3, 10),
(201, 13, 12, 5, 4, 4),
(202, 22, 13, 1, 1, 14),
(203, 8, 13, 1, 2, 6),
(204, 25, 13, 1, 3, 13),
(205, 5, 13, 1, 4, 8),
(206, 3, 13, 1, 5, 4),
(207, 23, 13, 2, 1, 10),
(208, 7, 13, 2, 2, 6),
(209, 20, 13, 2, 3, 2),
(210, 20, 13, 2, 4, 2),
(211, 1, 13, 2, 5, 5),
(212, 3, 13, 3, 1, 4),
(213, 25, 13, 3, 2, 13),
(214, 19, 13, 3, 3, 2),
(215, 19, 13, 3, 4, 2),
(216, 23, 13, 3, 5, 10),
(217, 21, 13, 4, 1, 2),
(218, 21, 13, 4, 2, 2),
(219, 22, 13, 4, 3, 14),
(220, 24, 13, 4, 4, 6),
(221, 17, 13, 5, 1, 2),
(222, 17, 13, 5, 2, 2),
(223, 4, 13, 5, 3, 5),
(224, 4, 13, 5, 4, 5),
(225, 8, 14, 1, 1, 8),
(226, 25, 14, 1, 2, 13),
(227, 3, 14, 1, 3, 4),
(228, 23, 14, 1, 4, 10),
(229, 26, 14, 1, 5, 2),
(230, 7, 14, 2, 1, 6),
(231, 22, 14, 2, 2, 14),
(232, 5, 14, 2, 3, 8),
(233, 1, 14, 2, 4, 9),
(234, 26, 14, 2, 5, 2),
(235, 20, 14, 3, 1, 2),
(236, 20, 14, 3, 2, 2),
(237, 4, 14, 3, 3, 5),
(238, 24, 14, 3, 4, 17),
(239, 22, 14, 4, 1, 14),
(240, 23, 14, 4, 2, 10),
(241, 4, 14, 4, 3, 5),
(242, 19, 14, 4, 4, 2),
(243, 19, 14, 4, 5, 2),
(244, 3, 14, 5, 1, 4),
(245, 25, 14, 5, 2, 13),
(246, 21, 14, 5, 3, 2),
(247, 21, 14, 5, 4, 2),
(248, 19, 15, 1, 1, 2),
(249, 19, 15, 1, 2, 2),
(254, 3, 15, 2, 3, 4),
(255, 1, 15, 2, 4, 5),
(256, 18, 15, 2, 5, 4),
(257, 23, 15, 3, 1, 10),
(258, 24, 15, 3, 2, 17),
(259, 25, 15, 3, 3, 13),
(260, 22, 15, 3, 4, 14),
(262, 4, 15, 4, 1, 5),
(263, 4, 15, 4, 2, 5),
(265, 8, 15, 4, 4, 8),
(266, 25, 15, 4, 5, 13),
(267, 22, 15, 5, 1, 14),
(268, 3, 15, 5, 2, 4),
(269, 8, 15, 5, 3, 8),
(270, 23, 15, 5, 4, 10),
(272, 17, 15, 1, 3, 2),
(273, 17, 15, 1, 4, 2),
(274, 26, 15, 2, 1, 2),
(275, 26, 15, 2, 2, 2),
(276, 20, 15, 3, 5, 2),
(277, 21, 15, 4, 3, 2),
(278, 20, 15, 5, 5, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_clase`
--

CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `hc_nombre` varchar(12) NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_ordinal` int(11) UNSIGNED NOT NULL,
  `hc_tipo` char(1) NOT NULL DEFAULT 'C'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_hora_clase`
--

INSERT INTO `sw_hora_clase` (`id_hora_clase`, `id_periodo_lectivo`, `hc_nombre`, `hc_hora_inicio`, `hc_hora_fin`, `hc_ordinal`, `hc_tipo`) VALUES
(1, 1, '1a.', '18:40:00', '19:20:00', 1, 'C'),
(2, 1, '2a.', '19:20:00', '20:00:00', 2, 'C'),
(3, 1, '3a.', '20:00:00', '20:40:00', 3, 'C'),
(4, 1, '4a.', '20:50:00', '21:30:00', 4, 'C'),
(5, 1, '5a.', '21:30:00', '22:10:00', 5, 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_dia`
--

CREATE TABLE `sw_hora_dia` (
  `id_hora_dia` int(11) UNSIGNED NOT NULL,
  `id_dia_semana` int(11) UNSIGNED NOT NULL,
  `id_hora_clase` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_hora_dia`
--

INSERT INTO `sw_hora_dia` (`id_hora_dia`, `id_dia_semana`, `id_hora_clase`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 2, 1),
(7, 2, 2),
(8, 2, 3),
(9, 2, 4),
(10, 2, 5),
(11, 3, 1),
(12, 3, 2),
(13, 3, 3),
(14, 3, 4),
(15, 3, 5),
(16, 4, 1),
(17, 4, 2),
(18, 4, 3),
(19, 4, 4),
(20, 4, 5),
(21, 5, 1),
(22, 5, 2),
(23, 5, 3),
(24, 5, 4),
(25, 5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) UNSIGNED NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(64) NOT NULL,
  `in_telefono` varchar(64) NOT NULL,
  `in_nom_rector` varchar(45) NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL,
  `in_url` varchar(64) NOT NULL,
  `in_logo` varchar(64) NOT NULL,
  `in_amie` varchar(16) NOT NULL,
  `in_ciudad` varchar(64) NOT NULL,
  `in_copiar_y_pegar` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono`, `in_nom_rector`, `in_nom_vicerrector`, `in_nom_secretario`, `in_url`, `in_logo`, `in_amie`, `in_ciudad`, `in_copiar_y_pegar`) VALUES
(1, 'UNIDAD EDUCATIVA PCEI FISCAL SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256311/2254818', 'MSc. Wilson Proaño', 'Lic. Rómulo Mejía', 'MSc. Verónica Sanmartín', 'http://colegionocturnosalamanca.com', '286390959.gif', '17H00215', 'Quito D.M.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_jornada`
--

CREATE TABLE `sw_jornada` (
  `id_jornada` int(11) NOT NULL,
  `jo_nombre` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sw_jornada`
--

INSERT INTO `sw_jornada` (`id_jornada`, `jo_nombre`) VALUES
(1, 'MATUTINA'),
(2, 'VESPERTINA'),
(3, 'NOCTURNA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_malla_curricular`
--

CREATE TABLE `sw_malla_curricular` (
  `id_malla_curricular` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_curso` int(11) UNSIGNED NOT NULL,
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `ma_horas_presenciales` int(11) UNSIGNED NOT NULL,
  `ma_horas_autonomas` int(11) UNSIGNED NOT NULL,
  `ma_horas_tutorias` int(11) UNSIGNED NOT NULL,
  `ma_subtotal` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_malla_curricular`
--

INSERT INTO `sw_malla_curricular` (`id_malla_curricular`, `id_periodo_lectivo`, `id_curso`, `id_asignatura`, `ma_horas_presenciales`, `ma_horas_autonomas`, `ma_horas_tutorias`, `ma_subtotal`) VALUES
(1, 1, 1, 22, 3, 2, 1, 6),
(2, 1, 1, 23, 3, 2, 1, 6),
(3, 1, 1, 6, 3, 2, 2, 7),
(4, 1, 1, 2, 2, 2, 1, 5),
(5, 1, 1, 16, 1, 1, 0, 2),
(6, 1, 1, 25, 2, 1, 1, 4),
(7, 1, 2, 22, 3, 2, 1, 6),
(8, 1, 2, 23, 3, 2, 1, 6),
(9, 1, 2, 6, 3, 2, 2, 7),
(10, 1, 2, 2, 2, 2, 1, 5),
(11, 1, 2, 16, 1, 1, 0, 2),
(12, 1, 2, 25, 2, 1, 1, 4),
(13, 1, 3, 22, 3, 2, 1, 6),
(14, 1, 3, 23, 3, 2, 1, 6),
(15, 1, 3, 6, 3, 2, 2, 7),
(16, 1, 3, 2, 2, 2, 1, 5),
(17, 1, 3, 16, 1, 1, 0, 2),
(18, 1, 3, 25, 2, 1, 1, 4),
(19, 1, 4, 23, 2, 1, 1, 4),
(20, 1, 4, 3, 2, 1, 0, 3),
(21, 1, 4, 4, 2, 1, 1, 4),
(22, 1, 4, 1, 1, 1, 1, 3),
(23, 1, 4, 8, 1, 1, 1, 3),
(24, 1, 4, 5, 1, 1, 0, 2),
(25, 1, 4, 7, 1, 1, 0, 2),
(26, 1, 4, 22, 2, 1, 1, 4),
(27, 1, 4, 25, 2, 1, 0, 3),
(28, 1, 4, 24, 1, 1, 0, 2),
(29, 1, 5, 23, 2, 1, 1, 4),
(30, 1, 5, 3, 2, 1, 0, 3),
(31, 1, 5, 4, 2, 1, 1, 4),
(32, 1, 5, 1, 1, 1, 1, 3),
(33, 1, 5, 8, 1, 1, 1, 3),
(34, 1, 5, 5, 1, 1, 0, 2),
(35, 1, 5, 7, 1, 1, 0, 2),
(36, 1, 5, 22, 2, 1, 1, 4),
(37, 1, 5, 25, 2, 1, 0, 3),
(38, 1, 5, 24, 1, 1, 0, 2),
(39, 1, 6, 23, 2, 2, 1, 5),
(40, 1, 6, 3, 2, 2, 1, 5),
(41, 1, 6, 4, 2, 1, 0, 3),
(42, 1, 6, 1, 2, 2, 1, 5),
(43, 1, 6, 8, 2, 1, 1, 4),
(44, 1, 6, 22, 2, 2, 1, 5),
(45, 1, 6, 25, 2, 0, 0, 2),
(46, 1, 6, 24, 1, 0, 0, 1),
(47, 1, 7, 23, 2, 1, 1, 4),
(48, 1, 7, 3, 2, 1, 0, 3),
(49, 1, 7, 4, 2, 1, 1, 4),
(50, 1, 7, 1, 1, 1, 1, 3),
(51, 1, 7, 8, 1, 1, 1, 3),
(52, 1, 7, 5, 1, 1, 0, 2),
(53, 1, 7, 7, 1, 1, 0, 2),
(54, 1, 7, 22, 2, 1, 1, 4),
(55, 1, 7, 25, 2, 1, 0, 3),
(56, 1, 7, 24, 1, 1, 0, 2),
(57, 1, 7, 10, 6, 0, 0, 6),
(58, 1, 7, 14, 2, 0, 0, 2),
(59, 1, 7, 12, 2, 0, 0, 2),
(60, 1, 8, 23, 2, 1, 1, 4),
(61, 1, 8, 3, 2, 1, 0, 3),
(62, 1, 8, 4, 2, 1, 1, 4),
(63, 1, 8, 1, 1, 1, 1, 3),
(64, 1, 8, 8, 1, 1, 1, 3),
(65, 1, 8, 5, 1, 1, 0, 2),
(66, 1, 8, 7, 1, 1, 0, 2),
(67, 1, 8, 22, 2, 1, 1, 4),
(68, 1, 8, 25, 2, 1, 0, 3),
(69, 1, 8, 24, 1, 1, 0, 2),
(70, 1, 8, 10, 6, 0, 0, 6),
(71, 1, 8, 14, 2, 0, 0, 2),
(72, 1, 8, 12, 2, 0, 0, 2),
(73, 1, 9, 23, 2, 2, 1, 5),
(74, 1, 9, 3, 2, 2, 1, 5),
(75, 1, 9, 4, 2, 1, 0, 3),
(76, 1, 9, 1, 2, 2, 1, 5),
(77, 1, 9, 8, 2, 1, 1, 4),
(78, 1, 9, 22, 2, 2, 1, 5),
(79, 1, 9, 25, 2, 0, 0, 2),
(80, 1, 9, 24, 1, 0, 0, 1),
(81, 1, 9, 10, 3, 0, 0, 3),
(82, 1, 9, 9, 2, 0, 0, 2),
(83, 1, 9, 15, 2, 0, 0, 2),
(84, 1, 9, 11, 1, 0, 0, 1),
(85, 1, 9, 12, 1, 0, 0, 1),
(86, 1, 9, 13, 1, 0, 0, 1),
(87, 1, 10, 23, 2, 1, 1, 4),
(88, 1, 10, 3, 2, 1, 0, 3),
(89, 1, 10, 4, 2, 1, 1, 4),
(90, 1, 10, 1, 1, 1, 1, 3),
(91, 1, 10, 8, 1, 1, 1, 3),
(92, 1, 10, 5, 1, 1, 0, 2),
(93, 1, 10, 7, 1, 1, 0, 2),
(94, 1, 10, 22, 2, 1, 1, 4),
(95, 1, 10, 25, 2, 1, 0, 3),
(96, 1, 10, 24, 1, 1, 0, 2),
(97, 1, 10, 17, 2, 0, 0, 2),
(98, 1, 10, 20, 2, 0, 0, 2),
(99, 1, 10, 19, 4, 0, 0, 4),
(100, 1, 10, 21, 2, 0, 0, 2),
(101, 1, 11, 23, 2, 1, 1, 4),
(102, 1, 11, 3, 2, 1, 0, 3),
(103, 1, 11, 4, 2, 1, 1, 4),
(104, 1, 11, 1, 1, 1, 1, 3),
(105, 1, 11, 8, 1, 1, 1, 3),
(106, 1, 11, 5, 1, 1, 0, 2),
(107, 1, 11, 7, 1, 1, 0, 2),
(108, 1, 11, 22, 2, 1, 1, 4),
(109, 1, 11, 25, 2, 1, 0, 3),
(110, 1, 11, 24, 1, 1, 0, 2),
(111, 1, 11, 20, 2, 0, 0, 2),
(112, 1, 11, 19, 4, 0, 0, 4),
(113, 1, 11, 21, 2, 0, 0, 2),
(114, 1, 11, 26, 2, 0, 0, 2),
(115, 1, 12, 23, 2, 2, 1, 5),
(116, 1, 12, 3, 2, 2, 1, 5),
(117, 1, 12, 4, 2, 1, 0, 3),
(118, 1, 12, 1, 2, 2, 1, 5),
(119, 1, 12, 8, 2, 1, 1, 4),
(120, 1, 12, 22, 2, 2, 1, 5),
(121, 1, 12, 25, 2, 0, 0, 2),
(122, 1, 12, 24, 1, 0, 0, 1),
(123, 1, 12, 17, 2, 0, 0, 2),
(124, 1, 12, 20, 2, 0, 0, 2),
(125, 1, 12, 19, 2, 0, 0, 2),
(126, 1, 12, 21, 1, 0, 0, 1),
(127, 1, 12, 26, 2, 0, 0, 2),
(128, 1, 12, 18, 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu`
--

CREATE TABLE `sw_menu` (
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `mnu_texto` varchar(32) CHARACTER SET latin1 NOT NULL,
  `mnu_enlace` varchar(64) CHARACTER SET latin1 NOT NULL,
  `mnu_link` varchar(64) CHARACTER SET latin1 NOT NULL,
  `mnu_nivel` int(11) NOT NULL,
  `mnu_orden` int(11) NOT NULL,
  `mnu_padre` int(11) NOT NULL,
  `mnu_publicado` int(11) NOT NULL,
  `mnu_icono` varchar(25) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `sw_menu`
--

INSERT INTO `sw_menu` (`id_menu`, `id_perfil`, `mnu_texto`, `mnu_enlace`, `mnu_link`, `mnu_nivel`, `mnu_orden`, `mnu_padre`, `mnu_publicado`, `mnu_icono`) VALUES
(2, 1, 'Menus', 'menu/index2.php', 'menus', 2, 7, 146, 1, 'fa fa-circle-o'),
(12, 1, 'Perfiles', 'perfil/view_index.php', 'perfiles', 2, 6, 146, 1, 'fa fa-circle-o'),
(13, 2, 'Calificaciones', '#', '', 1, 11, 0, 1, ''),
(18, 1, 'Usuarios', 'usuario/view_index.php', 'usuarios', 2, 8, 146, 1, 'fa fa-circle-o'),
(21, 3, 'Matriculación', '#', '#', 1, 22, 0, 1, 'fa fa-share-alt'),
(26, 5, 'Comportamiento', '#', '', 1, 19, 0, 1, ''),
(31, 6, 'Definiciones', '#', '', 1, 6, 0, 1, ''),
(32, 3, 'Libretación', '#', '', 1, 23, 0, 1, ''),
(37, 3, 'Reporte', '#', '', 1, 24, 0, 1, ''),
(38, 2, 'Reportes', '#', '', 1, 14, 0, 1, ''),
(39, 3, 'A Excel', '#', '', 1, 25, 0, 1, ''),
(40, 3, 'Promoción', 'promocion/index.php', '', 1, 26, 0, 1, ''),
(43, 1, 'Procesos', '#', '#', 1, 5, 0, 1, 'fa fa-lock'),
(45, 2, 'Informes', '#', '', 1, 13, 0, 1, ''),
(48, 2, 'Listas', '#', '', 1, 15, 0, 1, ''),
(52, 7, 'Reportes', '#', '', 1, 30, 0, 1, ''),
(56, 2, 'Cuestionarios', 'cuestionarios/index.php', '', 1, 16, 0, 0, ''),
(58, 7, 'Comportamiento', '#', '', 1, 27, 0, 1, ''),
(67, 2, 'Supletorios', 'calificaciones/supletorios.php', '', 2, 3, 13, 1, ''),
(68, 2, 'Remediales', 'calificaciones/remediales.php', '', 2, 4, 13, 1, ''),
(69, 2, 'De Gracia', 'calificaciones/de_gracia.php', '', 2, 5, 13, 1, ''),
(71, 2, 'Parciales', 'calificaciones/informe_parciales2.php', '', 2, 1, 45, 1, ''),
(73, 2, 'Anuales', 'calificaciones/informe_anual.php', '', 2, 2, 45, 1, ''),
(74, 2, 'Quimestrales', 'reportes/por_periodo.php', '', 2, 1, 38, 1, ''),
(75, 2, 'Anuales', 'calificaciones/reporte_anual.php', '', 2, 2, 38, 1, ''),
(76, 2, 'Por Parcial', 'listas_estudiantes/por_parcial.php', '', 2, 1, 48, 1, ''),
(77, 2, 'Por Quimestre', 'listas_estudiantes/por_quimestre.php', '', 2, 2, 48, 1, ''),
(78, 5, 'Quimestral', 'inspeccion/comportamiento.php', '', 2, 2, 26, 1, ''),
(79, 5, 'Anual', 'inspeccion/comportamiento_anual.php', '', 2, 3, 26, 1, ''),
(97, 3, 'Validar calificaciones', 'calificaciones/validar_calificaciones.php', '', 2, 1, 32, 1, ''),
(98, 3, 'Libretación', 'reportes/libretacion.php', '', 2, 2, 32, 1, ''),
(99, 3, 'Por Asignatura', 'calificaciones/por_asignaturas.php', '', 2, 2, 37, 1, ''),
(100, 3, 'Parciales', 'calificaciones/reporte_parciales.php', '', 2, 3, 37, 1, ''),
(101, 3, 'Quimestral', 'calificaciones/procesar_promedios.php', '', 2, 4, 37, 1, ''),
(102, 3, 'Anual', 'calificaciones/promedios_anuales.php', '', 2, 5, 37, 1, ''),
(103, 3, 'De Supletorios', 'calificaciones/reporte_supletorios.php', '', 2, 6, 37, 1, ''),
(104, 3, 'De Remediales', 'calificaciones/reporte_remediales.php', '', 2, 7, 37, 1, ''),
(105, 3, 'De Exámenes de Gracia', 'calificaciones/reporte_de_gracia.php', '', 2, 8, 37, 1, ''),
(107, 3, 'Quimestrales', 'php_excel/quimestrales.php', '', 2, 2, 39, 1, ''),
(108, 3, 'Anuales', 'php_excel/anuales.php', '', 2, 3, 39, 1, ''),
(109, 3, 'Cuadro Final', 'php_excel/cuadro_final.php', '', 2, 4, 39, 1, ''),
(110, 7, 'Parciales', 'tutores/parciales2.php', '', 2, 1, 52, 1, ''),
(111, 7, 'Quimestrales', 'tutores/quimestrales2.php', '', 2, 2, 52, 1, ''),
(112, 7, 'Anuales', 'tutores/anuales2.php', '', 2, 3, 52, 1, ''),
(113, 7, 'Supletorios', 'tutores/supletorios.php', '', 2, 4, 52, 1, ''),
(114, 7, 'Remediales', 'tutores/remediales.php', '', 2, 5, 52, 1, ''),
(116, 7, 'De Parciales', 'tutores/comp_parciales2.php', '', 2, 1, 58, 1, ''),
(117, 7, 'De Quimestrales', 'tutores/comportamiento2.php', '', 2, 2, 58, 1, ''),
(119, 2, 'Proyectos', 'listas_estudiantes/proyectos.php', '', 2, 3, 48, 1, ''),
(120, 1, 'Cerrar Periodos', 'aportes_evaluacion/cerrar_periodos.php', 'cierre_periodos', 2, 1, 43, 1, 'fa fa-circle-o'),
(123, 3, 'Paralelos', 'matriculacion/index.php', 'matriculacion', 2, 1, 21, 1, 'fa fa-university'),
(124, 3, 'Proyectos Escolares', 'matriculacion/clubes.php', 'secretaria/proyectos_escolares', 2, 2, 21, 1, ''),
(127, 6, 'Horarios', '#', '', 1, 7, 0, 1, ''),
(128, 6, 'Definir Días de la Semana', 'dias_semana/index.php', 'dias_semana', 2, 1, 127, 1, ''),
(129, 6, 'Definir Horas Clase', 'hora_clase/index.php', 'horas_clase', 2, 2, 127, 1, ''),
(130, 6, 'Definir Horario Semanal', 'horarios/view_horario_semanal.php', 'horarios', 2, 4, 127, 1, ''),
(132, 6, 'Reportes', '#', '', 1, 8, 0, 1, ''),
(134, 6, 'Quimestral', 'calificaciones/reporte_quimestral_autoridad.php', '', 2, 2, 132, 1, ''),
(135, 6, 'Anual', 'calificaciones/promedios_anuales_autoridad.php', '', 2, 3, 132, 1, ''),
(138, 2, 'Asistencia', '#', '', 1, 12, 0, 1, ''),
(140, 3, 'Cuadro Remediales', 'php_excel/cuadro_remediales.php', '', 2, 5, 39, 1, ''),
(141, 3, 'Cuadro De Gracia', 'php_excel/cuadro_de_gracia.php', '', 2, 6, 39, 1, ''),
(143, 7, 'De Gracia', 'tutores/de_gracia.php', '', 2, 6, 52, 1, ''),
(144, 5, 'Parciales', 'inspeccion/comportamiento_parciales.php', '', 2, 1, 26, 1, ''),
(146, 1, 'Administración', '#', '#', 1, 1, 0, 1, 'fa fa-gear'),
(159, 1, 'Definiciones', '#', '#', 1, 2, 0, 1, 'fa fa-cogs'),
(160, 1, 'Niveles de Educación', 'tipo_educacion/index2.php', 'tipos_educacion', 2, 2, 159, 1, 'fa fa-university'),
(161, 1, 'Especialidades', 'especialidades/index2.php', 'especialidades', 2, 3, 159, 1, 'fa fa-university'),
(162, 1, 'Cursos', 'cursos/index2.php', 'cursos', 2, 4, 159, 1, 'fa fa-university'),
(163, 1, 'Paralelos', 'paralelos/index2.php', 'paralelos', 2, 5, 159, 1, 'fa fa-university'),
(164, 1, 'Areas', 'areas/index.php', 'areas', 2, 6, 159, 1, 'fa fa-university'),
(165, 1, 'Asignaturas', 'asignaturas/index2.php', 'asignaturas', 2, 7, 159, 1, 'fa fa-university'),
(166, 1, 'Asociar', '#', '#', 1, 4, 0, 1, 'fa fa-share-alt'),
(167, 1, 'Asignaturas Cursos', 'asignaturas_cursos/index2.php', 'asignaturas_cursos', 2, 1, 166, 1, 'fa fa-circle-o'),
(171, 1, 'Paralelos Tutores', 'paralelos_tutores/index2.php', 'paralelos_tutores', 2, 3, 166, 1, 'fa fa-circle-o'),
(176, 1, 'Por Hacer', 'por_hacer/index.php', '#', 2, 2, 146, 0, 'fa fa-list-alt'),
(179, 13, 'Parciales', 'calificaciones/reporte_parciales.php', 'parciales', 2, 1, 189, 1, ''),
(180, 13, 'Quimestrales', 'calificaciones/procesar_promedios.php', 'quimestrales', 2, 2, 189, 1, ''),
(181, 13, 'Anuales', 'calificaciones/promedios_anuales.php', 'anuales', 2, 3, 189, 1, ''),
(189, 13, 'Reportes', '#', '', 1, 1, 0, 1, ''),
(195, 6, 'Definir Inasistencias', 'inasistencias/index.php', '', 2, 5, 127, 1, ''),
(197, 2, 'Leccionario', 'horarios/view_asistencia.php', '', 2, 1, 138, 1, ''),
(198, 2, 'Ver Horario', 'horarios/ver_horario_docente.php', '', 2, 2, 138, 1, ''),
(200, 1, 'Períodos Lectivos', 'periodos_lectivos/index2.php', 'periodos_lectivos', 2, 5, 146, 1, ''),
(201, 6, 'Malla Curricular', 'malla_curricular/index.php', 'mallas_curriculares', 2, 1, 31, 1, ''),
(202, 6, 'Distributivo', 'distributivo/index.php', 'distributivos', 2, 2, 31, 1, ''),
(203, 6, 'Asociar Dia-Hora', 'asociar_dia_hora/index.php', 'horas_dia', 2, 3, 127, 1, ''),
(204, 5, 'Horarios', '#', '', 1, 20, 0, 1, ''),
(205, 5, 'Leccionario', 'horarios/leccionario.php', '', 2, 1, 204, 1, ''),
(206, 6, 'Estadísticas', '#', '', 1, 10, 0, 1, ''),
(207, 6, 'Aprobados por Paralelo', 'estadisticas/aprobados_paralelo.php', '', 2, 1, 206, 1, ''),
(208, 3, 'Padrón Electoral', 'padron_electoral/index.php', '', 2, 1, 39, 1, ''),
(210, 7, 'Consultas', '#', '', 1, 29, 0, 1, ''),
(211, 7, 'Lista de Docentes', 'tutores/lista_docentes.php', '', 2, 1, 210, 1, ''),
(212, 7, 'Horario de Clases', 'tutores/horario_clases.php', '', 2, 2, 210, 1, ''),
(213, 6, 'Consultas', '#', '', 1, 9, 0, 1, ''),
(215, 6, 'Horarios de clase', 'autoridad/horario_clases.php', 'horarios_clases', 2, 2, 213, 1, ''),
(216, 5, 'Docentes', 'inspeccion/horario_docentes.php', '', 2, 2, 204, 1, ''),
(217, 5, 'Definiciones', '#', '', 1, 17, 0, 1, ''),
(218, 5, 'Valor del mes', 'valor_del_mes/index.php', '', 2, 1, 217, 1, ''),
(219, 5, 'Consultas', '#', '', 1, 21, 0, 1, ''),
(220, 5, 'Lista de docentes', 'autoridad/lista_docentes.php', '', 2, 1, 219, 1, ''),
(221, 5, 'Horarios de clase', 'autoridad/horario_clases.php', '', 2, 2, 219, 1, ''),
(222, 5, 'Feriados', 'feriados/index.php', '', 2, 2, 217, 1, ''),
(225, 5, 'Asistencia', '#', '', 1, 18, 0, 1, ''),
(228, 7, 'Asistencia', '#', '', 1, 28, 0, 1, ''),
(229, 7, 'Justificar Faltas', 'tutores/view_justificar.php', '', 2, 1, 228, 1, ''),
(230, 3, 'Nómina de Matriculados', 'php_excel/nomina_matriculados.php', '', 2, 1, 37, 1, ''),
(231, 1, 'Institución', 'institucion/index.php', 'instituciones', 2, 3, 146, 1, ''),
(232, 1, 'Modalidades', 'modalidades/view_index.php', 'modalidades', 2, 4, 146, 1, ''),
(233, 1, 'Curso Superior', 'asociar_curso_superior/index.php', 'cursos_superiores', 2, 2, 166, 1, ''),
(235, 1, 'Educación Inicial', 'educacion_inicial/index.php', '', 2, 1, 159, 0, ''),
(236, 2, 'Tareas', 'tareas/index.php', '', 2, 1, 13, 0, ''),
(239, 2, 'Parciales', 'calificaciones/index.php', '', 2, 2, 13, 1, ''),
(240, 1, 'Paralelos Inspectores', 'paralelos_inspectores/index2.php', 'paralelos_inspectores', 2, 4, 166, 1, ''),
(241, 1, 'Especificaciones', '#', '#', 1, 3, 0, 1, ''),
(242, 1, 'Períodos de Evaluación', 'periodos_evaluacion/index2.php', 'periodos_evaluacion', 2, 1, 241, 1, ''),
(243, 1, 'Aportes de Evaluación', 'aportes_evaluacion/index2.php', 'aportes_evaluacion', 2, 2, 241, 1, ''),
(244, 1, 'Insumos de Evaluación', 'rubricas_evaluacion/index2.php', 'insumos_evaluacion', 2, 3, 241, 1, ''),
(245, 1, 'Escalas de Calificaciones', 'escalas/index2.php', 'escalas_calificaciones', 2, 4, 241, 1, ''),
(246, 6, 'Parciales', 'calificaciones/reporte_parciales_autoridad.php', '', 2, 1, 132, 1, ''),
(247, 6, 'Lista de docentes', 'autoridad/lista_docentes.php', 'lista_docentes', 2, 1, 213, 1, ''),
(250, 1, 'Promoción Paralelos', 'promocion_paralelos/index.php', '', 2, 5, 166, 1, ''),
(251, 1, 'Promoción Automática', 'promocion_automatica/index.php', '', 2, 2, 43, 1, ''),
(252, 2, 'Asistencia', 'reportes/asistencia_docente.php', '', 2, 3, 38, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu_perfil`
--

CREATE TABLE `sw_menu_perfil` (
  `id_perfil` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_menu_perfil`
--

INSERT INTO `sw_menu_perfil` (`id_perfil`, `id_menu`) VALUES
(1, 2),
(1, 12),
(1, 18),
(1, 43),
(1, 120),
(1, 146),
(1, 159),
(1, 160),
(1, 161),
(1, 162),
(1, 163),
(1, 164),
(1, 165),
(1, 166),
(1, 167),
(1, 171),
(1, 176),
(1, 200),
(1, 231),
(1, 232),
(1, 233),
(1, 235),
(2, 13),
(2, 38),
(2, 45),
(2, 48),
(2, 56),
(2, 67),
(2, 68),
(2, 69),
(2, 71),
(2, 73),
(2, 74),
(2, 75),
(2, 76),
(2, 77),
(2, 119),
(2, 138),
(2, 197),
(2, 198),
(2, 236),
(3, 21),
(3, 32),
(3, 37),
(3, 39),
(3, 40),
(3, 97),
(3, 98),
(3, 99),
(3, 100),
(3, 101),
(3, 102),
(3, 103),
(3, 104),
(3, 105),
(3, 107),
(3, 108),
(3, 109),
(3, 123),
(3, 124),
(3, 140),
(3, 141),
(3, 208),
(3, 230),
(5, 26),
(5, 78),
(5, 79),
(5, 144),
(5, 204),
(5, 205),
(5, 216),
(5, 217),
(5, 218),
(5, 219),
(5, 220),
(5, 221),
(5, 222),
(5, 225),
(6, 31),
(6, 127),
(6, 128),
(6, 129),
(6, 130),
(6, 132),
(6, 134),
(6, 135),
(6, 195),
(6, 201),
(6, 202),
(6, 203),
(6, 206),
(6, 207),
(6, 213),
(6, 215),
(7, 52),
(7, 58),
(7, 110),
(7, 111),
(7, 112),
(7, 113),
(7, 114),
(7, 116),
(7, 117),
(7, 143),
(7, 210),
(7, 211),
(7, 212),
(7, 228),
(7, 229),
(13, 179),
(13, 180),
(13, 181),
(13, 189),
(1, 241);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_modalidad`
--

CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) UNSIGNED NOT NULL,
  `mo_nombre` varchar(64) NOT NULL,
  `mo_activo` tinyint(1) UNSIGNED NOT NULL,
  `mo_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_modalidad`
--

INSERT INTO `sw_modalidad` (`id_modalidad`, `mo_nombre`, `mo_activo`, `mo_orden`) VALUES
(1, 'SEMIPRESENCIAL', 1, 1),
(2, 'BGU INTENSIVO', 1, 3),
(3, 'EGB SUPERIOR INTENSIVA', 1, 2),
(5, 'EBGS VIRTUAL', 0, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_notificacion`
--

CREATE TABLE `sw_notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `notificacion` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sw_notificacion`
--

INSERT INTO `sw_notificacion` (`id_notificacion`, `notificacion`, `timestamp`) VALUES
(1, 'NOTIFICACION DE PRUEBA', '2022-06-30 12:23:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo`
--

CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `pa_nombre` varchar(5) NOT NULL,
  `pa_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sw_paralelo`
--

INSERT INTO `sw_paralelo` (`id_paralelo`, `id_periodo_lectivo`, `id_curso`, `id_jornada`, `pa_nombre`, `pa_orden`) VALUES
(1, 1, 1, 3, 'A', 1),
(2, 1, 2, 3, 'A', 2),
(3, 1, 3, 3, 'A', 3),
(4, 1, 3, 3, 'B', 4),
(5, 1, 4, 3, 'A', 5),
(6, 1, 5, 3, 'A', 6),
(7, 1, 5, 3, 'B', 7),
(8, 1, 6, 3, 'A', 8),
(9, 1, 6, 3, 'B', 9),
(10, 1, 7, 3, 'A', 10),
(11, 1, 8, 3, 'A', 11),
(12, 1, 9, 3, 'A', 12),
(13, 1, 10, 3, 'A', 13),
(14, 1, 11, 3, 'A', 14),
(15, 1, 12, 3, 'A', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_asignatura`
--

CREATE TABLE `sw_paralelo_asignatura` (
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_inspector`
--

CREATE TABLE `sw_paralelo_inspector` (
  `id_paralelo_inspector` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_paralelo_inspector`
--

INSERT INTO `sw_paralelo_inspector` (`id_paralelo_inspector`, `id_periodo_lectivo`, `id_paralelo`, `id_usuario`) VALUES
(2, 1, 2, 5),
(3, 1, 3, 5),
(4, 1, 6, 5),
(5, 1, 7, 5),
(6, 1, 8, 5),
(7, 1, 9, 5),
(8, 1, 10, 5),
(9, 1, 11, 5),
(10, 1, 12, 5),
(11, 1, 13, 5),
(12, 1, 14, 5),
(13, 1, 15, 5),
(14, 1, 16, 5),
(16, 1, 17, 5),
(17, 1, 1, 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_tutor`
--

CREATE TABLE `sw_paralelo_tutor` (
  `id_paralelo_tutor` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_paralelo_tutor`
--

INSERT INTO `sw_paralelo_tutor` (`id_paralelo_tutor`, `id_periodo_lectivo`, `id_paralelo`, `id_usuario`) VALUES
(1, 1, 2, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil`
--

CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) UNSIGNED NOT NULL,
  `pe_nombre` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_nivel_acceso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_perfil`
--

INSERT INTO `sw_perfil` (`id_perfil`, `pe_nombre`, `pe_nivel_acceso`) VALUES
(1, 'Administrador', 3),
(2, 'Docente', 2),
(3, 'Secretaría', 2),
(5, 'Inspección', 2),
(6, 'Autoridad', 3),
(7, 'Tutor', 2),
(8, 'DECE', 2),
(10, 'Estudiante', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_estado`
--

CREATE TABLE `sw_periodo_estado` (
  `id_periodo_estado` int(11) UNSIGNED NOT NULL,
  `pe_descripcion` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_periodo_estado`
--

INSERT INTO `sw_periodo_estado` (`id_periodo_estado`, `pe_descripcion`) VALUES
(1, 'ACTUAL'),
(2, 'TERMINADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_evaluacion`
--

CREATE TABLE `sw_periodo_evaluacion` (
  `id_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) UNSIGNED NOT NULL,
  `pe_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_abreviatura` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_periodo_evaluacion`
--

INSERT INTO `sw_periodo_evaluacion` (`id_periodo_evaluacion`, `id_periodo_lectivo`, `id_tipo_periodo`, `pe_nombre`, `pe_abreviatura`) VALUES
(1, 1, 1, 'PRIMER QUIMESTRE', '1ER.Q.'),
(2, 1, 1, 'SEGUNDO QUIMESTRE', '2DO.Q.'),
(3, 1, 2, 'SUPLETORIO', 'SUP.'),
(4, 1, 3, 'REMEDIAL', 'REM.'),
(5, 1, 4, 'DE GRACIA', 'GRA.');

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
  `pe_fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sw_periodo_lectivo`
--

INSERT INTO `sw_periodo_lectivo` (`id_periodo_lectivo`, `id_periodo_estado`, `id_modalidad`, `pe_anio_inicio`, `pe_anio_fin`, `pe_fecha_inicio`, `pe_fecha_fin`) VALUES
(1, 1, 1, 2021, 2022, '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_docente`
--

CREATE TABLE `sw_rubrica_docente` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_criterio_personalizado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_rubrica_docente`
--

INSERT INTO `sw_rubrica_docente` (`id_rubrica_evaluacion`, `id_criterio_personalizado`, `id_usuario`, `id_asignatura`, `id_paralelo`) VALUES
(1, 1, 5, 14, 10),
(1, 2, 5, 14, 10),
(1, 3, 5, 14, 10),
(1, 4, 5, 14, 10),
(2, 5, 5, 14, 10),
(2, 6, 5, 14, 10),
(2, 7, 5, 14, 10),
(2, 8, 5, 14, 10),
(3, 9, 5, 14, 10),
(3, 10, 5, 14, 10),
(3, 11, 5, 14, 10),
(3, 12, 5, 14, 10),
(4, 13, 5, 14, 10),
(4, 14, 5, 14, 10),
(4, 15, 5, 14, 10),
(4, 16, 5, 14, 10),
(5, 17, 5, 14, 10),
(1, 18, 5, 15, 10),
(1, 19, 5, 15, 10),
(1, 20, 5, 15, 10),
(1, 21, 5, 15, 10),
(2, 22, 5, 15, 10),
(2, 23, 5, 15, 10),
(2, 24, 5, 15, 10),
(2, 25, 5, 15, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_estudiante`
--

CREATE TABLE `sw_rubrica_estudiante` (
  `id_rubrica_estudiante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL,
  `re_calificacion` float NOT NULL,
  `re_fec_entrega` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_evaluacion`
--

CREATE TABLE `sw_rubrica_evaluacion` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_tipo_asignatura` int(11) UNSIGNED NOT NULL,
  `ru_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ru_abreviatura` varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_rubrica_evaluacion`
--

INSERT INTO `sw_rubrica_evaluacion` (`id_rubrica_evaluacion`, `id_aporte_evaluacion`, `id_tipo_asignatura`, `ru_nombre`, `ru_abreviatura`) VALUES
(1, 1, 1, 'TRABAJOS', 'TR'),
(2, 1, 1, 'SUMATIVA', 'SU'),
(3, 2, 1, 'TRABAJOS', 'TR'),
(4, 2, 1, 'SUMATIVA', 'SU'),
(5, 3, 1, 'PROYECTO QUIMESTRAL', 'PROY'),
(6, 4, 1, 'TRABAJOS', 'TR'),
(7, 4, 1, 'SUMATIVA', 'SU'),
(8, 5, 1, 'TRABAJOS', 'TR'),
(9, 6, 1, 'PROYECTO QUIMESTRAL', 'PROY'),
(10, 7, 1, 'EXAMEN SUPLETORIO', 'SUP'),
(11, 8, 1, 'EXAMEN REMEDIAL', 'REM'),
(12, 9, 1, 'EXAMEN DE GRACIA', 'GRA'),
(14, 5, 1, 'SUMATIVA', 'SU');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_personalizada`
--

CREATE TABLE `sw_rubrica_personalizada` (
  `id_rubrica_personalizada` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) UNSIGNED NOT NULL,
  `rp_tema` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `rp_fec_envio` date NOT NULL,
  `rp_fec_evaluacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_rubrica_personalizada`
--

INSERT INTO `sw_rubrica_personalizada` (`id_rubrica_personalizada`, `id_rubrica_evaluacion`, `id_usuario`, `id_asignatura`, `id_paralelo`, `rp_tema`, `rp_fec_envio`, `rp_fec_evaluacion`) VALUES
(1, 1, 5, 14, 10, 'ALGORITMOS Y PROGRAMAS', '2013-09-10', '2013-09-17'),
(2, 2, 5, 14, 10, 'ALGORITMOS DE LA VIDA DIARIA', '2013-09-18', '2013-09-25'),
(3, 3, 5, 14, 10, 'FASES DE LA PROGRAMACION', '2013-10-02', '2013-10-09'),
(4, 4, 5, 14, 10, 'ALGORITMOS Y FASES DE PROGRAMACION', '2013-10-16', '2013-10-23'),
(5, 5, 5, 14, 10, 'UT1: ALGORITMOS Y PROGRAMAS', '2013-10-16', '2013-10-16'),
(8, 1, 5, 15, 10, 'DIAGRAMA DE LA PLACA BASE', '2013-09-09', '2013-09-16'),
(9, 2, 5, 15, 10, 'FORMATOS DE LA PLACA BASE', '2013-09-23', '2013-09-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_aporte`
--

CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) UNSIGNED NOT NULL,
  `ta_descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_aporte`
--

INSERT INTO `sw_tipo_aporte` (`id_tipo_aporte`, `ta_descripcion`) VALUES
(1, 'PARCIAL'),
(2, 'EXAMEN QUIMESTRAL'),
(3, 'SUPLETORIO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_asignatura`
--

CREATE TABLE `sw_tipo_asignatura` (
  `id_tipo_asignatura` int(11) UNSIGNED NOT NULL,
  `ta_descripcion` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_asignatura`
--

INSERT INTO `sw_tipo_asignatura` (`id_tipo_asignatura`, `ta_descripcion`) VALUES
(1, 'CUANTITATIVA'),
(2, 'CUALITATIVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_documento`
--

CREATE TABLE `sw_tipo_documento` (
  `id_tipo_documento` int(11) UNSIGNED NOT NULL,
  `td_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_documento`
--

INSERT INTO `sw_tipo_documento` (`id_tipo_documento`, `td_nombre`) VALUES
(1, 'Cédula de Identidad'),
(2, 'Pasaporte'),
(3, 'Carnet de refugiado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_educacion`
--

CREATE TABLE `sw_tipo_educacion` (
  `id_tipo_educacion` int(11) UNSIGNED NOT NULL,
  `id_periodo_lectivo` int(11) UNSIGNED NOT NULL,
  `te_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `te_bachillerato` int(11) NOT NULL,
  `te_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_tipo_educacion`
--

INSERT INTO `sw_tipo_educacion` (`id_tipo_educacion`, `id_periodo_lectivo`, `te_nombre`, `te_bachillerato`, `te_orden`) VALUES
(1, 1, 'EDUCACION BASICA SUPERIOR', 0, 1),
(2, 1, 'BACHILLERATO GENERAL UNIFICADO', 1, 2),
(3, 1, 'BACHILLERATO TECNICO', 1, 3),
(4, 1, 'elimíname', 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) UNSIGNED NOT NULL,
  `tp_descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_periodo`
--

INSERT INTO `sw_tipo_periodo` (`id_tipo_periodo`, `tp_descripcion`) VALUES
(1, 'QUIMESTRE'),
(2, 'SUPLETORIO'),
(3, 'REMEDIAL'),
(4, 'DE GRACIA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `us_titulo` varchar(8) NOT NULL,
  `us_apellidos` varchar(32) NOT NULL,
  `us_nombres` varchar(32) NOT NULL,
  `us_shortname` varchar(45) NOT NULL,
  `us_fullname` varchar(64) NOT NULL,
  `us_login` varchar(24) NOT NULL,
  `us_password` varchar(64) NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_genero` varchar(1) NOT NULL,
  `us_activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sw_usuario`
--

INSERT INTO `sw_usuario` (`id_usuario`, `us_titulo`, `us_apellidos`, `us_nombres`, `us_shortname`, `us_fullname`, `us_login`, `us_password`, `us_foto`, `us_genero`, `us_activo`) VALUES
(1, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'Administrador', 'eDuVVtAzH2Qf', '855136358.jpg', 'M', 1),
(2, 'Tlgo.', 'Cabascango Herrera', 'Milton Fabián', 'Tlgo. Milton Cabascango', 'Cabascango Herrera Milton Fabián', 'miltonc', 'UiLAAKw4HDM=', '1920377357.jpg', 'M', 1),
(3, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'gonzalop', 'eDuVVtAzH2Qf', '990894859.png', 'M', 1),
(4, 'Ing.', 'Benavides Ortiz', 'German Gustavo', 'Ing. German Benavides', 'Benavides Ortiz German Gustavo', 'germanb', 'WC7RDPxvSTMLzBDf', '183631556.jpg', 'M', 1),
(5, 'Lic.', 'Calero Navarrete', 'Elmo Eduardo', 'Lic. Elmo Calero', 'Calero Navarrete Elmo Eduardo', 'elmoc', 'WifODvNuX2BI', '1332781677.jpg', 'M', 1),
(6, 'Lic.', 'Cedeño Zambrano', 'Edith Monserrate', 'Lic. Edith Cedeño', 'Cedeño Zambrano Edith Monserrate', 'edithc', 'ciTNEvhzWWBPmxbG', '1130818136.jpg', 'F', 1),
(7, 'Dr.', 'Enríquez Martínez', 'Carlos Alberto', 'Dr. Carlos Enríquez', 'Enríquez Martínez Carlos Alberto', 'CARLOSE', 'XCrRDfJyTjkLxg==', '1133505079.png', 'M', 1),
(8, 'Dr.', 'Guamán Calderón', 'Luis Alfredo', 'Dr. Luis Guamán', 'Guamán Calderón Luis Alfredo', 'luisg', 'WS7RD/xvT24=', '1945146505.png', 'M', 1),
(9, 'Lic.', 'Mejía Segarra', 'Rómulo Oswaldo', 'Lic. Rómulo Mejía', 'Mejía Segarra Rómulo Oswaldo', 'romulom', 'TSTOFPFuRg==', '43729064.jpg', 'M', 1),
(10, 'Lic.', 'Montenegro Yépez', 'Jaime Efrén', 'Lic. Jaime Montenegro', 'Montenegro Yépez Jaime Efrén', 'efrenm', 'Wi3RBPMw', '2117906686.jpg', 'M', 1),
(11, 'MSc.', 'Proaño Estrella', 'Wilson Eduardo', 'MSc. Wilson Proaño', 'Proaño Estrella Wilson Eduardo', 'wilsonp', 'Wi/WAO9lRHEJzhLEvA==', '1926558213.jpg', 'M', 1),
(12, 'MSc.', 'Rosero Medina', 'Roberto Hernán', 'MSc. Roberto Rosero', 'Rosero Medina Roberto Hernán', 'robertos', 'TSTBBO91RHIJzhLExTw=', '1180374851.jpg', 'M', 1),
(13, 'Lic.', 'Salazar Ordoñez', 'Carmen Alicia', 'Lic. Carmen Salazar', 'Salazar Ordoñez Carmen Alicia', 'alicias', 'XibBAO8=', '1071121085.jpg', 'F', 1),
(14, 'Lic.', 'Salgado Araujo', 'María del Rosario', 'Lic. María Salgado', 'Salgado Araujo María del Rosario', 'rosarios', 'TSrFAPhtSg==', '139409125.png', 'F', 1),
(15, 'MSc.', 'Sanmartín Vásquez', 'Sandra Verónica', 'MSc. Sandra Sanmartín', 'Sanmartín Vásquez Sandra Verónica', 'veronicas', 'SS7RDvNoSGBIzBDHp0X8', '1849519297.jpg', 'F', 1),
(16, 'Lic.', 'Trujillo Realpe', 'William Oswaldo', 'Lic. William Trujillo', 'Trujillo Realpe William Oswaldo', 'williamt', 'cDjUAPFlRDACyRTY', '576934223.jpg', 'M', 1),
(17, 'Lic.', 'Zambrano Cedeño', 'Walter Adbón', 'Lic. Walter Zambrano', 'Zambrano Cedeño Walter Adbón', 'walterz', 'cirbCOluGTEJzg==', '205265583.jpg', 'M', 1),
(22, 'Msc.', 'PEÑA ALMACHE', 'Jaime Enrique', 'Msc. Jaime PEÑA', 'PEÑA ALMACHE Jaime Enrique', 'jaimep', 'VSrKDPhxGTEJz3Pf', '1243027087.png', 'M', 1),
(23, 'Lic.', 'Quevedo Barrezueta', 'Alfonso Miguel', 'Lic. Alfonso Quevedo', 'Quevedo Barrezueta Alfonso Miguel', 'alfonsoq', 'XifFDvNyRHA=', '1683669507.jpg', 'M', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario_perfil`
--

CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_perfil` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_usuario_perfil`
--

INSERT INTO `sw_usuario_perfil` (`id_usuario`, `id_perfil`) VALUES
(1, 1),
(2, 2),
(2, 7),
(3, 2),
(3, 7),
(4, 2),
(4, 7),
(5, 2),
(5, 5),
(5, 7),
(6, 2),
(6, 7),
(7, 2),
(7, 7),
(8, 2),
(8, 7),
(9, 2),
(9, 6),
(9, 7),
(10, 2),
(10, 7),
(11, 2),
(11, 6),
(12, 2),
(12, 7),
(13, 2),
(13, 7),
(14, 2),
(14, 7),
(15, 2),
(15, 3),
(15, 7),
(16, 2),
(16, 7),
(17, 2),
(17, 7),
(22, 7),
(22, 8),
(23, 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD PRIMARY KEY (`id_aporte_evaluacion`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`),
  ADD KEY `id_tipo_aporte` (`id_tipo_aporte`);

--
-- Indices de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD PRIMARY KEY (`id_aporte_paralelo_cierre`),
  ADD KEY `sw_aporte_paralelo_cierre_id_aporte_evaluacion_foreign` (`id_aporte_evaluacion`),
  ADD KEY `sw_aporte_paralelo_cierre_id_paralelo_foreign` (`id_paralelo`);

--
-- Indices de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD KEY `id_area` (`id_area`),
  ADD KEY `id_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  ADD PRIMARY KEY (`id_asignatura_curso`),
  ADD KEY `sw_asignatura_curso_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_asignatura_curso_id_curso_foreign` (`id_curso`),
  ADD KEY `sw_asignatura_curso_id_asignatura_foreign` (`id_asignatura`);

--
-- Indices de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD PRIMARY KEY (`id_asociar_curso_superior`),
  ADD KEY `sw_asociar_curso_superior_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_asociar_curso_superior_id_curso_inferior_foreign` (`id_curso_inferior`),
  ADD KEY `sw_asociar_curso_superior_id_curso_superior_foreign` (`id_curso_superior`);

--
-- Indices de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  ADD PRIMARY KEY (`id_comentario`);

--
-- Indices de la tabla `sw_criterio_estudiante`
--
ALTER TABLE `sw_criterio_estudiante`
  ADD PRIMARY KEY (`id_criterio_estudiante`),
  ADD KEY `id_rubrica_estudiante` (`id_rubrica_estudiante`),
  ADD KEY `id_criterio_personalizado` (`id_criterio_personalizado`);

--
-- Indices de la tabla `sw_criterio_evaluacion`
--
ALTER TABLE `sw_criterio_evaluacion`
  ADD PRIMARY KEY (`id_criterio_evaluacion`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`);

--
-- Indices de la tabla `sw_criterio_personalizado`
--
ALTER TABLE `sw_criterio_personalizado`
  ADD PRIMARY KEY (`id_criterio_personalizado`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_personalizada`),
  ADD KEY `id_rubrica_personalizada` (`id_rubrica_personalizada`);

--
-- Indices de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_especialidad` (`id_especialidad`);

--
-- Indices de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  ADD PRIMARY KEY (`id_curso_superior`);

--
-- Indices de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  ADD PRIMARY KEY (`id_def_genero`);

--
-- Indices de la tabla `sw_def_nacionalidad`
--
ALTER TABLE `sw_def_nacionalidad`
  ADD PRIMARY KEY (`id_def_nacionalidad`);

--
-- Indices de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD PRIMARY KEY (`id_dia_semana`),
  ADD KEY `sw_dia_semana_id_periodo_lectivo_foreign` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD PRIMARY KEY (`id_distributivo`),
  ADD KEY `sw_distributivo_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_distributivo_id_malla_curricular_foreign` (`id_malla_curricular`),
  ADD KEY `sw_distributivo_id_paralelo_foreign` (`id_paralelo`),
  ADD KEY `sw_distributivo_id_asignatura_foreign` (`id_asignatura`),
  ADD KEY `sw_distributivo_id_usuario_foreign` (`id_usuario`);

--
-- Indices de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD PRIMARY KEY (`id_escala_calificaciones`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `sw_estudiante_id_tipo_documento_foreign` (`id_tipo_documento`),
  ADD KEY `sw_estudiante_id_def_genero_foreign` (`id_def_genero`),
  ADD KEY `sw_estudiante_id_def_nacionalidad_foreign` (`id_def_nacionalidad`);

--
-- Indices de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD PRIMARY KEY (`id_estudiante_periodo_lectivo`),
  ADD KEY `sw_estudiante_periodo_lectivo_id_estudiante_foreign` (`id_estudiante`),
  ADD KEY `sw_estudiante_periodo_lectivo_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_estudiante_periodo_lectivo_id_paralelo_foreign` (`id_paralelo`);

--
-- Indices de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `sw_horario_id_asignatura_foreign` (`id_asignatura`),
  ADD KEY `sw_horario_id_paralelo_foreign` (`id_paralelo`),
  ADD KEY `sw_horario_id_dia_semana_foreign` (`id_dia_semana`),
  ADD KEY `sw_horario_id_hora_clase_foreign` (`id_hora_clase`),
  ADD KEY `sw_horario_id_usuario_foreign` (`id_usuario`);

--
-- Indices de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD PRIMARY KEY (`id_hora_clase`),
  ADD KEY `sw_hora_clase_id_periodo_lectivo_foreign` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD PRIMARY KEY (`id_hora_dia`),
  ADD KEY `sw_hora_dia_id_dia_semana_foreign` (`id_dia_semana`),
  ADD KEY `sw_hora_dia_id_hora_clase_foreign` (`id_hora_clase`);

--
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- Indices de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD PRIMARY KEY (`id_malla_curricular`),
  ADD KEY `sw_malla_curricular_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_malla_curricular_id_curso_foreign` (`id_curso`),
  ADD KEY `sw_malla_curricular_id_asignatura_foreign` (`id_asignatura`);

--
-- Indices de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD KEY `fk_menu_perfil_menu` (`id_menu`),
  ADD KEY `fk_menu_perfil_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `sw_notificacion`
--
ALTER TABLE `sw_notificacion`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `sw_paralelo_ibfk_2` (`id_jornada`);

--
-- Indices de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  ADD PRIMARY KEY (`id_paralelo_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`,`id_asignatura`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD PRIMARY KEY (`id_paralelo_inspector`),
  ADD KEY `sw_paralelo_inspector_id_periodo_lectivo_foreign` (`id_periodo_lectivo`),
  ADD KEY `sw_paralelo_inspector_id_paralelo_foreign` (`id_paralelo`),
  ADD KEY `sw_paralelo_inspector_id_usuario_foreign` (`id_usuario`);

--
-- Indices de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD PRIMARY KEY (`id_paralelo_tutor`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  ADD PRIMARY KEY (`id_periodo_estado`);

--
-- Indices de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD PRIMARY KEY (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_tipo_periodo` (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD PRIMARY KEY (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_rubrica_docente`
--
ALTER TABLE `sw_rubrica_docente`
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_criterio_personalizado`,`id_usuario`),
  ADD KEY `id_criterio_evaluacion` (`id_criterio_personalizado`),
  ADD KEY `id_docente` (`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  ADD PRIMARY KEY (`id_rubrica_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_rubrica_personalizada` (`id_rubrica_personalizada`);

--
-- Indices de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD PRIMARY KEY (`id_rubrica_evaluacion`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  ADD PRIMARY KEY (`id_rubrica_personalizada`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  ADD PRIMARY KEY (`id_tipo_aporte`);

--
-- Indices de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  ADD PRIMARY KEY (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`);

--
-- Indices de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD PRIMARY KEY (`id_tipo_educacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD PRIMARY KEY (`id_usuario`,`id_perfil`),
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  MODIFY `id_aporte_evaluacion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  MODIFY `id_aporte_paralelo_cierre` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  MODIFY `id_asignatura_curso` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  MODIFY `id_asociar_curso_superior` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_criterio_estudiante`
--
ALTER TABLE `sw_criterio_estudiante`
  MODIFY `id_criterio_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_criterio_evaluacion`
--
ALTER TABLE `sw_criterio_evaluacion`
  MODIFY `id_criterio_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT de la tabla `sw_criterio_personalizado`
--
ALTER TABLE `sw_criterio_personalizado`
  MODIFY `id_criterio_personalizado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  MODIFY `id_curso` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  MODIFY `id_def_genero` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_def_nacionalidad`
--
ALTER TABLE `sw_def_nacionalidad`
  MODIFY `id_def_nacionalidad` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  MODIFY `id_distributivo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  MODIFY `id_especialidad` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  MODIFY `id_estudiante_periodo_lectivo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  MODIFY `id_hora_clase` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  MODIFY `id_hora_dia` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  MODIFY `id_malla_curricular` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_notificacion`
--
ALTER TABLE `sw_notificacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  MODIFY `id_paralelo_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  MODIFY `id_paralelo_inspector` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  MODIFY `id_paralelo_tutor` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  MODIFY `id_perfil` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  MODIFY `id_periodo_estado` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  MODIFY `id_periodo_evaluacion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  MODIFY `id_rubrica_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  MODIFY `id_rubrica_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  MODIFY `id_rubrica_personalizada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  MODIFY `id_tipo_aporte` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  MODIFY `id_tipo_asignatura` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  MODIFY `id_tipo_documento` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  MODIFY `id_tipo_educacion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD CONSTRAINT `sw_aporte_evaluacion_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`),
  ADD CONSTRAINT `sw_aporte_evaluacion_ibfk_2` FOREIGN KEY (`id_tipo_aporte`) REFERENCES `sw_tipo_aporte` (`id_tipo_aporte`);

--
-- Filtros para la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_id_aporte_evaluacion_foreign` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD CONSTRAINT `sw_asignatura_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `sw_area` (`id_area`),
  ADD CONSTRAINT `sw_asignatura_ibfk_2` FOREIGN KEY (`id_tipo_asignatura`) REFERENCES `sw_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Filtros para la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  ADD CONSTRAINT `sw_asignatura_curso_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_asignatura_curso_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_asignatura_curso_ibfk_3` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`);

--
-- Filtros para la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD CONSTRAINT `sw_asociar_curso_superior_id_curso_inferior_foreign` FOREIGN KEY (`id_curso_inferior`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_asociar_curso_superior_id_curso_superior_foreign` FOREIGN KEY (`id_curso_superior`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_asociar_curso_superior_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_criterio_estudiante`
--
ALTER TABLE `sw_criterio_estudiante`
  ADD CONSTRAINT `sw_criterio_estudiante_ibfk_1` FOREIGN KEY (`id_rubrica_estudiante`) REFERENCES `sw_rubrica_estudiante` (`id_rubrica_estudiante`),
  ADD CONSTRAINT `sw_criterio_estudiante_ibfk_2` FOREIGN KEY (`id_criterio_personalizado`) REFERENCES `sw_criterio_personalizado` (`id_criterio_personalizado`);

--
-- Filtros para la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD CONSTRAINT `sw_curso_ibfk_1` FOREIGN KEY (`id_especialidad`) REFERENCES `sw_especialidad` (`id_especialidad`);

--
-- Filtros para la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD CONSTRAINT `sw_dia_semana_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD CONSTRAINT `sw_distributivo_id_asignatura_foreign` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_distributivo_id_malla_curricular_foreign` FOREIGN KEY (`id_malla_curricular`) REFERENCES `sw_malla_curricular` (`id_malla_curricular`),
  ADD CONSTRAINT `sw_distributivo_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_distributivo_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_distributivo_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD CONSTRAINT `sw_escala_calificaciones_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD CONSTRAINT `sw_especialidad_ibfk_1` FOREIGN KEY (`id_tipo_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);

--
-- Filtros para la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD CONSTRAINT `sw_estudiante_id_def_genero_foreign` FOREIGN KEY (`id_def_genero`) REFERENCES `sw_def_genero` (`id_def_genero`),
  ADD CONSTRAINT `sw_estudiante_id_def_nacionalidad_foreign` FOREIGN KEY (`id_def_nacionalidad`) REFERENCES `sw_def_nacionalidad` (`id_def_nacionalidad`),
  ADD CONSTRAINT `sw_estudiante_id_tipo_documento_foreign` FOREIGN KEY (`id_tipo_documento`) REFERENCES `sw_tipo_documento` (`id_tipo_documento`);

--
-- Filtros para la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD CONSTRAINT `sw_horario_id_asignatura_foreign` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_horario_id_dia_semana_foreign` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`),
  ADD CONSTRAINT `sw_horario_id_hora_clase_foreign` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_horario_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_horario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD CONSTRAINT `sw_hora_clase_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD CONSTRAINT `sw_hora_dia_id_dia_semana_foreign` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`),
  ADD CONSTRAINT `sw_hora_dia_id_hora_clase_foreign` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`);

--
-- Filtros para la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD CONSTRAINT `sw_malla_curricular_id_asignatura_foreign` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_malla_curricular_id_curso_foreign` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_malla_curricular_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD CONSTRAINT `sw_menu_perfil_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `sw_menu` (`id_menu`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD CONSTRAINT `sw_paralelo_inspector_id_paralelo_foreign` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_paralelo_inspector_id_periodo_lectivo_foreign` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_paralelo_inspector_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD CONSTRAINT `sw_periodo_evaluacion_ibfk_1` FOREIGN KEY (`id_tipo_periodo`) REFERENCES `sw_tipo_periodo` (`id_tipo_periodo`);

--
-- Filtros para la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_2` FOREIGN KEY (`id_tipo_asignatura`) REFERENCES `sw_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Filtros para la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD CONSTRAINT `sw_tipo_educacion_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD CONSTRAINT `sw_usuario_perfil_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_usuario_perfil_ibfk_2` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
