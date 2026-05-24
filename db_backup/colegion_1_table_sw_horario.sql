
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario`
--

DROP TABLE IF EXISTS `sw_horario`;
CREATE TABLE `sw_horario` (
  `id_horario` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
