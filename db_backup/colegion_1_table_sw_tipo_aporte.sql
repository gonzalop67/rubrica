
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_aporte`
--

DROP TABLE IF EXISTS `sw_tipo_aporte`;
CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) NOT NULL,
  `ta_descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `sw_tipo_aporte`
--

INSERT INTO `sw_tipo_aporte` (`id_tipo_aporte`, `ta_descripcion`) VALUES
(1, 'PARCIAL'),
(2, 'EXAMEN_SUB_PERIODO'),
(3, 'SUPLETORIO'),
(4, 'REFUERZO'),
(5, 'PROYECTO_FINAL'),
(6, 'FASE_PRI'),
(7, 'EVAL_SUBNIVEL');
