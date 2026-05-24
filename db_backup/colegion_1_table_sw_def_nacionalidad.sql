
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_def_nacionalidad`
--

DROP TABLE IF EXISTS `sw_def_nacionalidad`;
CREATE TABLE `sw_def_nacionalidad` (
  `id_def_nacionalidad` int(11) NOT NULL,
  `dn_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_def_nacionalidad`
--

INSERT INTO `sw_def_nacionalidad` (`id_def_nacionalidad`, `dn_nombre`) VALUES
(1, 'Ecuatoriana'),
(2, 'Colombiana'),
(3, 'Venezolana'),
(4, 'Haitiana');
