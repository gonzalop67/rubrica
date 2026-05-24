
DROP TABLE IF EXISTS `sw_hora_clase`;
CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `hc_nombre` varchar(10) NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_ordinal` int(11) NOT NULL,
  `hc_tipo` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_hora_clase` (`id_hora_clase`, `id_periodo_lectivo`, `hc_nombre`, `hc_hora_inicio`, `hc_hora_fin`, `hc_ordinal`, `hc_tipo`) VALUES
(1, 6, 'Tut. 4', '18:05:00', '18:45:00', 4, 'T'),
(2, 6, '1a', '18:45:00', '19:30:00', 5, 'C'),
(3, 6, '2a', '19:30:00', '20:05:00', 6, 'C'),
(4, 6, '3a', '20:05:00', '20:45:00', 7, 'C'),
(5, 6, '4a', '21:00:00', '21:30:00', 8, 'C'),
(6, 6, '5a', '21:30:00', '22:05:00', 9, 'C'),
(7, 6, 'Tut. 1', '16:20:00', '16:55:00', 1, 'T'),
(8, 6, 'Tut. 2', '16:55:00', '17:30:00', 2, 'T'),
(9, 6, 'Tut. 3', '17:30:00', '18:05:00', 3, 'T');
