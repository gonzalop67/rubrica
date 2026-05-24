
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_promocion_automatica`
--

DROP TABLE IF EXISTS `sw_promocion_automatica`;
CREATE TABLE `sw_promocion_automatica` (
  `id_promocion_automatica` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo_actual` int(11) NOT NULL,
  `id_paralelo_anterior` int(11) NOT NULL,
  `pro_estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_promocion_automatica`
--

INSERT INTO `sw_promocion_automatica` (`id_promocion_automatica`, `id_periodo_lectivo`, `id_paralelo_actual`, `id_paralelo_anterior`, `pro_estado`) VALUES
(1, 9, 145, 124, 1),
(2, 9, 146, 125, 0),
(3, 9, 149, 135, 0),
(4, 9, 150, 136, 1),
(5, 9, 152, 138, 1),
(6, 9, 153, 139, 0),
(7, 9, 155, 129, 0),
(8, 9, 158, 141, 0),
(9, 9, 156, 130, 0),
(10, 9, 159, 131, 0),
(11, 13, 166, 148, 0),
(12, 19, 199, 186, 0),
(13, 20, 207, 204, 0),
(14, 20, 208, 205, 0),
(15, 21, 210, 195, 0),
(16, 21, 211, 196, 0),
(17, 21, 214, 197, 0),
(18, 21, 212, 199, 0),
(19, 21, 213, 203, 0),
(20, 24, 218, 207, 0),
(22, 24, 217, 206, 0);
