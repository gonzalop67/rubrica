
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_permiso`
--

DROP TABLE IF EXISTS `sw_permiso`;
CREATE TABLE `sw_permiso` (
  `id_permiso` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `read` int(11) NOT NULL,
  `insert` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `delete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `sw_permiso`
--

INSERT INTO `sw_permiso` (`id_permiso`, `id_menu`, `id_perfil`, `read`, `insert`, `update`, `delete`) VALUES
(13, 148, 1, 1, 1, 1, 1),
(14, 157, 2, 1, 1, 0, 0),
(17, 160, 1, 1, 1, 1, 1),
(18, 161, 1, 1, 1, 1, 1),
(19, 162, 1, 1, 1, 1, 1),
(20, 163, 1, 1, 1, 1, 1),
(21, 164, 1, 1, 1, 1, 1),
(22, 165, 1, 1, 1, 1, 1),
(23, 169, 3, 1, 1, 0, 0),
(24, 82, 3, 1, 0, 1, 0),
(25, 83, 3, 1, 0, 1, 0),
(26, 84, 3, 1, 0, 1, 0),
(27, 85, 3, 1, 0, 1, 0),
(28, 87, 3, 1, 0, 1, 0);
