
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

DROP TABLE IF EXISTS `sw_escala_comportamiento`;
CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) NOT NULL,
  `ec_cualitativa` varchar(164) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_escala_comportamiento`
--

INSERT INTO `sw_escala_comportamiento` (`id_escala_comportamiento`, `ec_relacion`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_equivalencia`, `ec_correlativa`) VALUES
(1, 'Muy satisfactorio', 'Lidera el cumplimiento de los compromisos establecidos para la sana convivencia social.', '9 - 10', 9, 10, 'A', 5),
(2, 'Satisfactorio', 'Cumple con los compromisos establecidos para la sana convivencia social.', '7 - 8.99', 7, 8.999, 'B', 4),
(3, 'Poco satisfactorio', 'Falla ocasionalmente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '6 - 6.99', 6, 6.99, 'C', 3),
(4, 'Mejorable', 'Falla reiteradamente en el cumplimiento de los compromisos establecidos para la sana convivencia social.', '4 - 5.99', 4, 5.99, 'D', 2),
(5, 'Insatisfactorio', 'No cumple con los compromisos establecidos para la sana convivencia social.', '< 4', 0.01, 3.99, 'E', 1),
(6, 'Sin notas', 'Sin notas.', '0', 0, 0, 'S/N', 0);
