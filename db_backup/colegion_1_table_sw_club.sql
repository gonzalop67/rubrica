
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club`
--

DROP TABLE IF EXISTS `sw_club`;
CREATE TABLE `sw_club` (
  `id_club` int(11) NOT NULL,
  `cl_nombre` varchar(32) NOT NULL,
  `cl_abreviatura` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_club`
--

INSERT INTO `sw_club` (`id_club`, `cl_nombre`, `cl_abreviatura`) VALUES
(1, 'CIENCIAS/ECOLOGIA', 'CLCIE.'),
(2, 'DISEÑO Y CINE', 'CINE.'),
(3, 'TECNOLOGIA', 'CLTEC.'),
(4, 'MATEMATICA', 'CLMAT.'),
(5, 'DEPORTES', 'CLDEP.');
