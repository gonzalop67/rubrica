
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario_def`
--

DROP TABLE IF EXISTS `sw_horario_def`;
CREATE TABLE `sw_horario_def` (
  `id_horario_def` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ho_titulo` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_inicial` date NOT NULL,
  `fecha_final` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: inactivo, 1: activo'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
