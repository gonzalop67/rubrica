
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_promedio_parcial`
--

DROP TABLE IF EXISTS `sw_estudiante_promedio_parcial`;
CREATE TABLE `sw_estudiante_promedio_parcial` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `ep_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_estudiante_promedio_parcial`
--

INSERT INTO `sw_estudiante_promedio_parcial` (`id`, `id_paralelo`, `id_estudiante`, `id_aporte_evaluacion`, `ep_promedio`) VALUES
(3928, 165, 1889, 128, 8.56923),
(3929, 165, 3019, 128, 0),
(3930, 165, 2608, 128, 0),
(3931, 165, 530, 128, 8.11731),
(3932, 165, 32, 128, 6.67038),
(3933, 165, 2498, 128, 8.98538),
(3934, 165, 3039, 128, 9.41346),
(3935, 165, 2610, 128, 0),
(3936, 165, 1398, 128, 0),
(3937, 165, 1361, 128, 8.91038);
