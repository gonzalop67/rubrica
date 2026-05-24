
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_supletorios`
--

DROP TABLE IF EXISTS `sw_escala_supletorios`;
CREATE TABLE `sw_escala_supletorios` (
  `id_escala_supletorios` int(11) NOT NULL,
  `nota_minima` float NOT NULL,
  `nota_maxima` float NOT NULL,
  `nota_final` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_escala_supletorios`
--

INSERT INTO `sw_escala_supletorios` (`id_escala_supletorios`, `nota_minima`, `nota_maxima`, `nota_final`) VALUES
(1, 7, 7.5, 7),
(2, 7.51, 7.99, 7.25),
(3, 8, 8.5, 7.5),
(4, 8.51, 8.99, 7.75),
(5, 9, 10, 8);
