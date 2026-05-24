
DROP TABLE IF EXISTS `sw_dia_feriado`;
CREATE TABLE `sw_dia_feriado` (
  `id_dia_feriado` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `df_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_dia_feriado` (`id_dia_feriado`, `id_periodo_lectivo`, `df_fecha`) VALUES
(1, 6, '2018-10-08'),
(2, 6, '2018-11-01'),
(3, 6, '2018-11-02'),
(4, 6, '2018-12-07'),
(5, 6, '2018-12-24'),
(6, 6, '2018-12-25'),
(7, 6, '2018-12-26'),
(8, 6, '2018-12-27'),
(9, 6, '2018-12-28'),
(10, 6, '2018-12-31'),
(11, 6, '2019-01-01'),
(12, 6, '2019-02-18'),
(13, 6, '2019-02-19'),
(14, 6, '2019-02-20'),
(15, 6, '2019-02-21'),
(16, 6, '2019-02-22'),
(17, 6, '2019-04-19'),
(18, 6, '2019-05-03'),
(19, 6, '2019-05-24');
