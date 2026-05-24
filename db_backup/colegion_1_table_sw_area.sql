
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_area`
--

DROP TABLE IF EXISTS `sw_area`;
CREATE TABLE `sw_area` (
  `id_area` int(11) NOT NULL,
  `ar_nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `sw_area`
--

INSERT INTO `sw_area` (`id_area`, `ar_nombre`) VALUES
(1, 'LENGUA Y LITERATURA'),
(2, 'MATEMATICA'),
(3, 'CIENCIAS SOCIALES'),
(4, 'CIENCIAS NATURALES'),
(5, 'EDUCACION CULTURAL Y ARTISTICA'),
(6, 'EDUCACION FISICA'),
(7, 'LENGUA EXTRANJERA'),
(8, 'PROYECTOS ESCOLARES'),
(9, 'MODULO INTER-AREAS'),
(10, 'CONTABILIDAD'),
(11, 'ADMINISTRACION DE SISTEMAS'),
(12, 'INFORMATICA');
