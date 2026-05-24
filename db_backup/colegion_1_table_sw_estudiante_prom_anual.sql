
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_prom_anual`
--

DROP TABLE IF EXISTS `sw_estudiante_prom_anual`;
CREATE TABLE `sw_estudiante_prom_anual` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ea_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_estudiante_prom_anual`
--

INSERT INTO `sw_estudiante_prom_anual` (`id`, `id_paralelo`, `id_estudiante`, `id_periodo_lectivo`, `ea_promedio`) VALUES
(7902, 189, 32, 17, 8.36767),
(7903, 189, 3332, 17, 0.7748),
(7904, 189, 3338, 17, 9.57792),
(7905, 189, 3095, 17, 8.5868);
