
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_cuestionario_paralelo`
--

DROP TABLE IF EXISTS `sw_cuestionario_paralelo`;
CREATE TABLE `sw_cuestionario_paralelo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_category` int(5) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_cuestionario_paralelo`
--

INSERT INTO `sw_cuestionario_paralelo` (`id`, `id_usuario`, `id_category`, `id_paralelo`, `id_asignatura`) VALUES
(6, 587, 3, 231, 9);
