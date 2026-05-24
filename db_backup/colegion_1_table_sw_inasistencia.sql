
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_inasistencia`
--

DROP TABLE IF EXISTS `sw_inasistencia`;
CREATE TABLE `sw_inasistencia` (
  `id_inasistencia` int(11) NOT NULL,
  `in_nombre` varchar(32) NOT NULL,
  `in_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_inasistencia`
--

INSERT INTO `sw_inasistencia` (`id_inasistencia`, `in_nombre`, `in_abreviatura`) VALUES
(1, 'Asiste', 'A'),
(2, 'Falta Injustificada', 'I'),
(3, 'Falta Justificada', 'J'),
(4, 'Tardanza', 'T');
