
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_leccionario`
--

DROP TABLE IF EXISTS `sw_leccionario`;
CREATE TABLE `sw_leccionario` (
  `id_leccionario` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `lec_fecha` date NOT NULL,
  `lec_unidad` int(11) NOT NULL,
  `lec_tema` varchar(64) NOT NULL,
  `lec_estrategia` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
