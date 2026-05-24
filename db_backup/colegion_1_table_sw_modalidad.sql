
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_modalidad`
--

DROP TABLE IF EXISTS `sw_modalidad`;
CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) NOT NULL,
  `mo_nombre` varchar(32) NOT NULL,
  `mo_activo` tinyint(1) NOT NULL,
  `mo_orden` int(11) NOT NULL,
  `mo_intensivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_modalidad`
--

INSERT INTO `sw_modalidad` (`id_modalidad`, `mo_nombre`, `mo_activo`, `mo_orden`, `mo_intensivo`) VALUES
(1, 'SEMIPRESENCIAL', 1, 1, 0),
(2, 'BGU INTENSIVO', 1, 3, 1),
(3, 'EGBS INTENSIVA', 1, 2, 2);
