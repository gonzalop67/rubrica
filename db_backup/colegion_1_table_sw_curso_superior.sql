
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso_superior`
--

DROP TABLE IF EXISTS `sw_curso_superior`;
CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `cs_nombre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_curso_superior`
--

INSERT INTO `sw_curso_superior` (`id_curso_superior`, `cs_nombre`) VALUES
(2, 'NOVENO AÑO DE EDUCACION GENERAL BASICA'),
(3, 'DECIMO AÑO DE EDUCACION GENERAL BASICA'),
(4, 'PRIMER AÑO DE BACHILLERATO'),
(5, 'SEGUNDO AÑO DE BACHILLERATO'),
(6, 'TERCER AÑO DE BACHILLERATO'),
(7, 'SEGUNDO CURSO DE BACHILLERATO GENERAL UNIFICADO'),
(8, 'TERCER CURSO DE BACHILLERATO GENERAL UNIFICADO');
