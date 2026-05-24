
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_dia_semana`
--

DROP TABLE IF EXISTS `sw_dia_semana`;
CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `ds_nombre` varchar(10) NOT NULL,
  `ds_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
