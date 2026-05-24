
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_supletorios_temp`
--

DROP TABLE IF EXISTS `sw_supletorios_temp`;
CREATE TABLE `sw_supletorios_temp` (
  `id` int(11) NOT NULL,
  `id_tipo_periodo` int(1) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_supletorios_temp`
--

INSERT INTO `sw_supletorios_temp` (`id`, `id_tipo_periodo`, `id_paralelo`, `id_estudiante`, `id_asignatura`, `id_rubrica_personalizada`) VALUES
(9, 2, 161, 2912, 12, 266),
(10, 2, 161, 2913, 12, 266),
(11, 2, 161, 2933, 12, 266),
(12, 2, 161, 2291, 9, 266),
(13, 2, 161, 2918, 9, 266),
(14, 2, 161, 2156, 9, 266),
(15, 2, 161, 2918, 1, 266),
(16, 2, 161, 2922, 1, 266),
(17, 2, 161, 2486, 1, 266),
(158, 2, 183, 3301, 9, 337),
(160, 2, 182, 3296, 9, 337),
(161, 3, 182, 3296, 9, 338),
(162, 3, 183, 3313, 9, 338),
(163, 3, 183, 3170, 9, 338),
(164, 3, 183, 3301, 9, 338),
(165, 3, 183, 1896, 9, 338),
(166, 3, 183, 3261, 9, 338),
(167, 3, 183, 3187, 9, 338),
(168, 3, 183, 3192, 9, 338),
(169, 3, 183, 862, 9, 338);
