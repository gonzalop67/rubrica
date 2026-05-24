
DROP TABLE IF EXISTS `sw_feriado`;
CREATE TABLE `sw_feriado` (
  `id_feriado` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `fe_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_feriado` (`id_feriado`, `id_periodo_lectivo`, `fe_fecha`) VALUES
(1, 6, '2018-10-08'),
(3, 6, '2018-11-02'),
(5, 6, '2018-12-24'),
(7, 6, '2018-12-25'),
(9, 6, '2018-12-26'),
(10, 6, '2018-11-01'),
(11, 6, '2018-12-27'),
(13, 6, '2018-12-28'),
(15, 6, '2018-12-31'),
(17, 6, '2019-01-01'),
(18, 6, '2019-02-18'),
(19, 6, '2019-02-19'),
(20, 6, '2019-02-20'),
(21, 6, '2019-02-21'),
(22, 6, '2019-02-22'),
(23, 6, '2019-04-19'),
(24, 6, '2019-05-03'),
(25, 6, '2019-05-24');
