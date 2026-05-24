
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_asignatura`
--

DROP TABLE IF EXISTS `sw_tipo_asignatura`;
CREATE TABLE `sw_tipo_asignatura` (
  `id_tipo_asignatura` int(11) NOT NULL,
  `ta_descripcion` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_tipo_asignatura`
--

INSERT INTO `sw_tipo_asignatura` (`id_tipo_asignatura`, `ta_descripcion`) VALUES
(1, 'CUANTITATIVA'),
(2, 'CUALITATIVA');
