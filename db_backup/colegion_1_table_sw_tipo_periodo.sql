
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

DROP TABLE IF EXISTS `sw_tipo_periodo`;
CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) NOT NULL,
  `tp_descripcion` varchar(45) NOT NULL,
  `tipo_periodo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `sw_tipo_periodo`
--

INSERT INTO `sw_tipo_periodo` (`id_tipo_periodo`, `tp_descripcion`, `tipo_periodo`) VALUES
(1, 'PRINCIPAL', 1),
(2, 'SUPLETORIO', 2),
(3, 'REMEDIAL', 2),
(4, 'DE GRACIA', 2),
(6, 'REFUERZO', 3),
(7, 'PROYECTO_FINAL', 4),
(8, 'EVAL_SUBNIVEL', 4);
