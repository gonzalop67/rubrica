
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_jornada`
--

DROP TABLE IF EXISTS `sw_jornada`;
CREATE TABLE `sw_jornada` (
  `id_jornada` int(11) NOT NULL,
  `jo_nombre` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_jornada`
--

INSERT INTO `sw_jornada` (`id_jornada`, `jo_nombre`) VALUES
(1, 'MATUTINA'),
(2, 'VESPERTINA'),
(3, 'NOCTURNA');
