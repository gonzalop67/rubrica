
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_def_genero`
--

DROP TABLE IF EXISTS `sw_def_genero`;
CREATE TABLE `sw_def_genero` (
  `id_def_genero` int(11) NOT NULL,
  `dg_nombre` varchar(50) NOT NULL,
  `dg_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_def_genero`
--

INSERT INTO `sw_def_genero` (`id_def_genero`, `dg_nombre`, `dg_abreviatura`) VALUES
(1, 'Femenino', 'F'),
(2, 'Masculino', 'M');
