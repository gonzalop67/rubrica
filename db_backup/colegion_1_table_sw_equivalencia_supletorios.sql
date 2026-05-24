
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_equivalencia_supletorios`
--

DROP TABLE IF EXISTS `sw_equivalencia_supletorios`;
CREATE TABLE `sw_equivalencia_supletorios` (
  `id_equivalencia_supletorios` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) NOT NULL,
  `rango_desde` float NOT NULL,
  `rango_hasta` float NOT NULL,
  `nombre_examen` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_equivalencia_supletorios`
--

INSERT INTO `sw_equivalencia_supletorios` (`id_equivalencia_supletorios`, `id_periodo_lectivo`, `id_tipo_periodo`, `rango_desde`, `rango_hasta`, `nombre_examen`) VALUES
(1, 1, 2, 5, 6.99, 'SUPLETORIO'),
(2, 1, 3, 0.01, 4.99, 'REMEDIAL'),
(3, 2, 2, 5, 6.99, 'SUPLETORIO'),
(4, 2, 3, 0.01, 4.99, 'REMEDIAL'),
(5, 3, 2, 5, 6.99, 'SUPLETORIO'),
(6, 3, 3, 0.01, 4.99, 'REMEDIAL'),
(7, 4, 2, 5, 6.99, 'SUPLETORIO'),
(8, 4, 3, 0.01, 4.99, 'REMEDIAL'),
(9, 5, 2, 5, 6.99, 'SUPLETORIO'),
(10, 5, 3, 0.01, 4.99, 'REMEDIAL'),
(11, 6, 2, 5, 6.99, 'SUPLETORIO'),
(12, 6, 3, 0.01, 4.99, 'REMEDIAL'),
(13, 7, 2, 5, 6.99, 'SUPLETORIO'),
(14, 7, 3, 0.01, 4.99, 'REMEDIAL'),
(15, 8, 2, 5, 6.99, 'SUPLETORIO'),
(16, 8, 3, 0.01, 4.99, 'REMEDIAL'),
(17, 9, 2, 5, 6.99, 'SUPLETORIO'),
(18, 9, 3, 0.01, 4.99, 'REMEDIAL'),
(19, 13, 2, 5, 6.99, 'SUPLETORIO'),
(20, 13, 3, 0.01, 4.99, 'REMEDIAL'),
(21, 17, 2, 4.01, 6.99, 'SUPLETORIO'),
(22, 10, 2, 5, 6.99, 'SUPLETORIO'),
(23, 10, 3, 0.01, 4.99, 'REMEDIAL'),
(24, 14, 2, 5, 6.99, 'SUPLETORIO'),
(25, 14, 3, 0.01, 4.99, 'REMEDIAL'),
(26, 18, 2, 4.01, 6.99, 'SUPLETORIO'),
(27, 11, 2, 5, 6.99, 'SUPLETORIO'),
(28, 11, 3, 0.01, 4.99, 'REMEDIAL'),
(29, 12, 2, 5, 6.99, 'SUPLETORIO'),
(30, 12, 3, 0.01, 4.99, 'REMEDIAL'),
(31, 15, 2, 5, 6.99, 'SUPLETORIO'),
(32, 15, 3, 0.01, 4.99, 'REMEDIAL'),
(33, 16, 2, 5, 6.99, 'SUPLETORIO'),
(34, 16, 3, 0.01, 4.99, 'REMEDIAL'),
(35, 19, 2, 4.01, 6.99, 'SUPLETORIO'),
(36, 20, 2, 4.01, 6.99, 'SUPLETORIO'),
(37, 21, 2, 4.01, 6.99, 'SUPLETORIO'),
(38, 26, 2, 4.01, 6.99, 'SUPLETORIO'),
(39, 25, 2, 4.01, 6.99, 'SUPLETORIO'),
(40, 24, 2, 4.01, 6.99, 'SUPLETORIO');
