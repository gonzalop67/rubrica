
DROP TABLE IF EXISTS `sw_club_docente`;
CREATE TABLE `sw_club_docente` (
  `id_club_docente` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_club_docente` (`id_club_docente`, `id_club`, `id_usuario`, `id_periodo_lectivo`) VALUES
(1, 2, 21, 2),
(2, 1, 25, 2),
(3, 4, 3, 2),
(4, 3, 43, 2),
(5, 2, 24, 2),
(6, 5, 40, 2),
(7, 5, 16, 2),
(8, 1, 42, 2),
(9, 4, 61, 2),
(11, 3, 10, 2),
(12, 1, 61, 3),
(13, 3, 5, 3),
(14, 4, 43, 3),
(15, 5, 40, 3),
(16, 2, 13, 3);
