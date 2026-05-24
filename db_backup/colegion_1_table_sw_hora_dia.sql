
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_dia`
--

DROP TABLE IF EXISTS `sw_hora_dia`;
CREATE TABLE `sw_hora_dia` (
  `id_hora_dia` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
