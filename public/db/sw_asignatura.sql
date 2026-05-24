-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2022 a las 17:21:34
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
-- Estructura de tabla para la tabla `sw_asignatura`
--

CREATE TABLE `sw_asignatura` (
  `id_asignatura` int(11) UNSIGNED NOT NULL,
  `id_area` int(11) UNSIGNED NOT NULL,
  `id_tipo_asignatura` int(11) UNSIGNED NOT NULL,
  `as_nombre` varchar(84) NOT NULL,
  `as_abreviatura` varchar(12) NOT NULL,
  `as_shortname` varchar(45) NOT NULL,
  `as_curricular` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD KEY `sw_asignatura_id_area_foreign` (`id_area`),
  ADD KEY `sw_asignatura_id_tipo_asignatura_foreign` (`id_tipo_asignatura`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD CONSTRAINT `sw_asignatura_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `sw_area` (`id_area`),
  ADD CONSTRAINT `sw_asignatura_id_tipo_asignatura_foreign` FOREIGN KEY (`id_tipo_asignatura`) REFERENCES `sw_tipo_asignatura` (`id_tipo_asignatura`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
