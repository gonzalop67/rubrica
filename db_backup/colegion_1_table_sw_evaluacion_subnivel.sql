
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_evaluacion_subnivel`
--

DROP TABLE IF EXISTS `sw_evaluacion_subnivel`;
CREATE TABLE `sw_evaluacion_subnivel` (
  `id_evaluacion_subnivel` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `calificacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
