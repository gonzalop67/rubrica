
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario_examen`
--

DROP TABLE IF EXISTS `sw_horario_examen`;
CREATE TABLE `sw_horario_examen` (
  `id_horario_examen` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_examen` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `he_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
