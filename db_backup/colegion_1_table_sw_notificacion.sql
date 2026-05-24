
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_notificacion`
--

DROP TABLE IF EXISTS `sw_notificacion`;
CREATE TABLE `sw_notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `notificacion` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
