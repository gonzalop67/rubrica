
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil`
--

DROP TABLE IF EXISTS `sw_perfil`;
CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) NOT NULL,
  `pe_nombre` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_nivel_acceso` int(11) NOT NULL DEFAULT 2,
  `pe_acceso_login` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_perfil`
--

INSERT INTO `sw_perfil` (`id_perfil`, `pe_nombre`, `pe_nivel_acceso`, `pe_acceso_login`) VALUES
(1, 'Administrador', 3, 1),
(2, 'Docente', 2, 1),
(3, 'Secretaría', 1, 1),
(5, 'Inspección', 1, 1),
(6, 'Autoridad', 3, 1),
(7, 'Tutor', 2, 1),
(13, 'DECE', 2, 1);
