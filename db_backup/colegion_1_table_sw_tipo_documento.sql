
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_documento`
--

DROP TABLE IF EXISTS `sw_tipo_documento`;
CREATE TABLE `sw_tipo_documento` (
  `id_tipo_documento` int(11) NOT NULL,
  `td_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_tipo_documento`
--

INSERT INTO `sw_tipo_documento` (`id_tipo_documento`, `td_nombre`) VALUES
(1, 'Cédula de Identidad'),
(2, 'Pasaporte'),
(3, 'Carnet de refugiado'),
(4, 'Cédula colombiana'),
(5, 'Cédula venezolana');
