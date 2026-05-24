
DROP TABLE IF EXISTS `sw_usuario_perfil`;
CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(10, 7),
(13, 2),
(13, 5),
(13, 7),
(19, 2),
(19, 7),
(21, 2),
(21, 6),
(21, 7),
(26, 2),
(26, 7),
(68, 2),
(68, 7),
(71, 2),
(71, 7),
(73, 2),
(73, 7)
