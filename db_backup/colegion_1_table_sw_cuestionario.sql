
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_cuestionario`
--

DROP TABLE IF EXISTS `sw_cuestionario`;
CREATE TABLE `sw_cuestionario` (
  `id_cuestionario` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `titulo` varchar(64) NOT NULL,
  `aciertos` int(11) NOT NULL DEFAULT 0,
  `errores` int(11) NOT NULL DEFAULT 0,
  `puntaje` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
