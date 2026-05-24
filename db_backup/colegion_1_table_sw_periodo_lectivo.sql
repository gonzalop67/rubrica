
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_lectivo`
--

DROP TABLE IF EXISTS `sw_periodo_lectivo`;
CREATE TABLE `sw_periodo_lectivo` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_estado` int(11) NOT NULL,
  `id_modalidad` int(11) NOT NULL,
  `id_institucion` int(11) NOT NULL,
  `pe_anio_inicio` int(11) NOT NULL,
  `pe_anio_fin` int(11) NOT NULL,
  `pe_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_fecha_inicio` date NOT NULL,
  `pe_fecha_fin` date NOT NULL,
  `pe_nota_minima` float NOT NULL,
  `pe_nota_aprobacion` float NOT NULL,
  `quien_inserta_comp_id` int(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_periodo_lectivo`
--

INSERT INTO `sw_periodo_lectivo` (`id_periodo_lectivo`, `id_periodo_estado`, `id_modalidad`, `id_institucion`, `pe_anio_inicio`, `pe_anio_fin`, `pe_estado`, `pe_fecha_inicio`, `pe_fecha_fin`, `pe_nota_minima`, `pe_nota_aprobacion`, `quien_inserta_comp_id`) VALUES
(1, 2, 1, 1, 2013, 2014, 'T', '2013-09-02', '2014-07-31', 0.01, 7, 2),
(2, 2, 1, 1, 2014, 2015, 'T', '2014-09-01', '2015-07-31', 0.01, 7, 2),
(3, 2, 1, 1, 2015, 2016, 'T', '2015-09-01', '2016-07-29', 0.01, 7, 2),
(4, 2, 1, 1, 2016, 2017, 'T', '2016-09-01', '2017-07-28', 0.01, 7, 2),
(5, 2, 1, 1, 2017, 2018, 'T', '2017-09-04', '2018-07-31', 0.01, 7, 2),
(6, 2, 1, 1, 2018, 2019, 'T', '2018-09-03', '2019-07-31', 0.01, 7, 2),
(7, 2, 1, 1, 2019, 2020, 'T', '2019-09-02', '2020-07-30', 0.01, 7, 2),
(8, 2, 1, 1, 2020, 2021, 'T', '2020-09-01', '2021-07-31', 0.01, 7, 2),
(9, 2, 1, 1, 2021, 2022, 'T', '2021-09-01', '2022-07-31', 0.01, 7, 2),
(10, 2, 3, 1, 2021, 2022, 'T', '2021-10-25', '2022-08-31', 0.01, 7, 2),
(11, 2, 2, 1, 2021, 2022, 'T', '2021-10-25', '2022-03-25', 0.01, 7, 2),
(12, 2, 2, 1, 2021, 2022, 'T', '2022-04-26', '2022-09-09', 0.01, 7, 2),
(13, 2, 1, 1, 2022, 2023, 'T', '2022-09-01', '2023-07-13', 0.01, 7, 2),
(14, 2, 3, 1, 2022, 2023, 'T', '2022-09-12', '2023-07-12', 0.01, 7, 2),
(15, 2, 2, 1, 2022, 2023, 'T', '2022-09-12', '2023-03-31', 0.01, 7, 2),
(16, 2, 2, 1, 2022, 2023, 'T', '2023-02-16', '2023-07-31', 0.01, 7, 2),
(17, 2, 1, 1, 2023, 2024, 'T', '2023-09-01', '2024-07-09', 4.01, 7, 2),
(18, 2, 3, 1, 2023, 2024, 'T', '2023-09-01', '2024-02-29', 4.01, 7, 2),
(19, 2, 2, 1, 2023, 2024, 'T', '2023-09-01', '2024-02-02', 4.01, 7, 2),
(20, 2, 3, 1, 2023, 2024, 'T', '2024-02-14', '2024-07-19', 4.01, 7, 2),
(21, 2, 2, 1, 2023, 2024, 'T', '2024-02-14', '2024-07-19', 4.01, 7, 2),
(24, 1, 3, 1, 2024, 2025, 'A', '2024-09-02', '2025-01-06', 0.01, 7, 2),
(25, 1, 2, 1, 2024, 2025, 'A', '2024-09-02', '2025-01-06', 0.01, 7, 2),
(26, 1, 1, 1, 2024, 2025, 'A', '2024-09-02', '2025-07-08', 0.01, 7, 2);
