
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_respuesta`
--

DROP TABLE IF EXISTS `sw_respuesta`;
CREATE TABLE `sw_respuesta` (
  `id_respuesta` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `re_texto` text NOT NULL,
  `re_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `re_autor` int(11) NOT NULL,
  `re_perfil` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
