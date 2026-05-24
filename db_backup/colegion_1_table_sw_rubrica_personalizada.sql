
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_personalizada`
--

DROP TABLE IF EXISTS `sw_rubrica_personalizada`;
CREATE TABLE `sw_rubrica_personalizada` (
  `id_rubrica_personalizada` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rp_tema` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `rp_fec_envio` date NOT NULL,
  `rp_fec_evaluacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
