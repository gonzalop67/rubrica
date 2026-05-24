
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_clase`
--

DROP TABLE IF EXISTS `sw_hora_clase`;
CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) NOT NULL,
  `hc_nombre` varchar(12) NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
