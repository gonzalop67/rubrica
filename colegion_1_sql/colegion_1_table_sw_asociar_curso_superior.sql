
DROP TABLE IF EXISTS `sw_asociar_curso_superior`;
CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) NOT NULL,
  `id_curso_inferior` int(11) DEFAULT NULL,
  `id_curso_superior` int(11) DEFAULT NULL,
  `id_periodo_lectivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sw_asociar_curso_superior` (`id_asociar_curso_superior`, `id_curso_inferior`, `id_curso_superior`, `id_periodo_lectivo`) VALUES
(1, 47, 48, 5),
(2, 48, 49, 5),
(3, 49, 50, 5),
(4, 49, 53, 5),
(5, 49, 56, 5),
(6, 50, 51, 5),
(7, 51, 52, 5),
(8, 53, 54, 5),
(9, 54, 55, 5),
(10, 56, 57, 5),
(11, 57, 58, 5),
(12, 41, 42, 4),
(13, 42, 43, 4),
(14, 43, 35, 4),
(15, 35, 36, 4),
(16, 36, 37, 4),
(17, 32, 33, 4),
(18, 33, 34, 4),
(19, 38, 39, 4),
(20, 39, 40, 4);
