
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_tutor`
--

DROP TABLE IF EXISTS `sw_comportamiento_tutor`;
CREATE TABLE `sw_comportamiento_tutor` (
  `id_comportamiento_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_comportamiento_tutor`
--

INSERT INTO `sw_comportamiento_tutor` (`id_comportamiento_tutor`, `id_paralelo`, `id_estudiante`, `id_aporte_evaluacion`, `id_escala_comportamiento`, `co_calificacion`) VALUES
(7, 106, 1870, 67, 3, 'B'),
(8, 106, 2055, 67, 3, 'C'),
(9, 106, 1873, 67, 2, 'B'),
(10, 106, 2056, 67, 3, 'C'),
(11, 106, 1874, 67, 2, 'B'),
(12, 106, 2057, 67, 3, 'C'),
(13, 106, 1876, 67, 3, 'C'),
(14, 106, 2103, 67, 4, 'D'),
(15, 106, 1877, 67, 2, 'B'),
(16, 106, 1871, 67, 2, 'B'),
(17, 106, 2104, 67, 2, 'B'),
(18, 106, 2105, 67, 5, 'E'),
(19, 106, 1878, 67, 1, 'A'),
(20, 106, 1462, 67, 2, 'B'),
(21, 106, 1872, 67, 2, 'B'),
(22, 106, 1875, 67, 3, 'C'),
(23, 109, 1886, 67, 1, 'A');
