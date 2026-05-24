
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_config_rangos_supletorios`
--

DROP TABLE IF EXISTS `sw_config_rangos_supletorios`;
CREATE TABLE `sw_config_rangos_supletorios` (
  `id` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `nota_desde` float NOT NULL,
  `nota_hasta` float NOT NULL,
  `nota_aprobacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_config_rangos_supletorios`
--

INSERT INTO `sw_config_rangos_supletorios` (`id`, `id_periodo_lectivo`, `id_periodo_evaluacion`, `nota_desde`, `nota_hasta`, `nota_aprobacion`) VALUES
(1, 1, 3, 5, 6.99, 7),
(2, 1, 4, 0.01, 4.99, 7),
(3, 2, 8, 5, 6.99, 7),
(4, 2, 9, 0.01, 4.99, 7),
(5, 3, 13, 5, 6.99, 7),
(6, 3, 14, 0.01, 4.99, 7),
(7, 4, 18, 5, 6.99, 7),
(8, 4, 19, 0.01, 4.99, 7),
(9, 5, 23, 5, 6.99, 7),
(10, 5, 24, 0.01, 4.99, 7),
(11, 6, 31, 5, 6.99, 7),
(12, 6, 32, 0.01, 4.99, 7),
(13, 7, 36, 5, 6.99, 7),
(14, 7, 37, 0.01, 4.99, 7),
(15, 8, 41, 5, 6.99, 7),
(16, 8, 42, 0.01, 4.99, 7),
(17, 9, 46, 5, 6.99, 7),
(18, 9, 47, 0.01, 4.99, 7),
(19, 10, 51, 5, 6.99, 7),
(20, 10, 52, 0.01, 4.99, 7),
(21, 11, 56, 5, 6.99, 7),
(22, 11, 57, 0.01, 4.99, 7),
(23, 12, 61, 5, 6.99, 7),
(24, 12, 62, 0.01, 4.99, 7),
(25, 13, 66, 5, 6.99, 7),
(26, 13, 67, 0.01, 4.99, 7),
(27, 14, 71, 5, 6.99, 7),
(28, 14, 72, 0.01, 4.99, 7),
(29, 15, 76, 5, 6.99, 7),
(30, 15, 77, 0.01, 4.99, 7),
(31, 16, 81, 5, 6.99, 7),
(32, 16, 82, 0.01, 4.99, 7);
