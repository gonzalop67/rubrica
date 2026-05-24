
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_quien_inserta_comp`
--

DROP TABLE IF EXISTS `sw_quien_inserta_comp`;
CREATE TABLE `sw_quien_inserta_comp` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_quien_inserta_comp`
--

INSERT INTO `sw_quien_inserta_comp` (`id`, `nombre`) VALUES
(1, 'Tutor'),
(2, 'Docente');
