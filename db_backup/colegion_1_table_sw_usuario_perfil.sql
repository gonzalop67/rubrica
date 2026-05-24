
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario_perfil`
--

DROP TABLE IF EXISTS `sw_usuario_perfil`;
CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_usuario_perfil`
--

INSERT INTO `sw_usuario_perfil` (`id_usuario`, `id_perfil`) VALUES
(1, 1),
(2, 2),
(2, 7),
(3, 2),
(3, 7),
(6, 2),
(6, 7),
(7, 2),
(7, 7),
(10, 2),
(10, 6),
(10, 7),
(13, 2),
(13, 7),
(15, 2),
(15, 7),
(19, 2),
(19, 7),
(20, 2),
(20, 7),
(21, 2),
(21, 6),
(21, 7),
(24, 2),
(24, 7),
(26, 2),
(26, 7),
(36, 2),
(39, 2),
(40, 2),
(40, 5),
(40, 6),
(40, 7),
(42, 2),
(43, 2),
(43, 7),
(68, 2),
(68, 5),
(68, 7),
(71, 2),
(71, 7),
(73, 2),
(73, 7),
(75, 2),
(587, 2),
(587, 7),
(593, 3),
(594, 2),
(597, 2),
(597, 7),
(598, 2),
(598, 7),
(599, 2),
(599, 3),
(599, 7),
(600, 7),
(600, 13),
(601, 2),
(601, 7),
(604, 2),
(604, 7),
(607, 2),
(607, 7),
(608, 2),
(609, 2);
