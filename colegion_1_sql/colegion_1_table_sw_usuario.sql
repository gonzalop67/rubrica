
DROP TABLE IF EXISTS `sw_usuario`;
CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `us_titulo` varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_apellidos` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_nombres` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_shortname` varchar(45) NOT NULL,
  `us_fullname` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_login` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_password` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_clave` varchar(64) NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_alias` varchar(15) NOT NULL,
  `us_activo` int(11) NOT NULL,
  `us_bgcolor` varchar(7) NOT NULL DEFAULT '#FFFFFF'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_usuario` (`id_usuario`, `id_periodo_lectivo`, `id_perfil`, `us_titulo`, `us_apellidos`, `us_nombres`, `us_shortname`, `us_fullname`, `us_login`, `us_password`, `us_clave`, `us_foto`, `us_alias`, `us_activo`, `us_bgcolor`) VALUES
(1, 1, 1, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'ADMINISTRADOR', 'AlhnlffT1WlPf0wsWGnJBTGDhEmD4vG+UwQrCxhpy9k=', '4a9b31c1ad41914eab818f7ef8b1879189178a24', '645816057.png', '', 1, '#FFFFFF'),
(2, 1, 2, 'Lic.', 'Salazar Ordóñez', 'Carmen Alicia', 'Lic. Alicia Salazar', 'Salazar Ordóñez Carmen Alicia', 'ALICIAS', 'tXHrcb5DZrDXITM7MhUV/vAdfMphUky1NizH6NQ0UxI=', 'c7e34148827d7580828f6b6a905420c3860ff779', '', '', 1, '#6666FF'),
(3, 1, 2, 'Dr.', 'Enríquez Martínez', 'Carlos Alberto', 'Dr. Carlos Enriquez', 'Enríquez Martínez Carlos Alberto', 'CARLOSE', 'o6nJPLDoyAFl3rcOgixM86hdjvFi8lnRElIpFhHybbo=', '117cbf6f025d734789ef8ca9e2ef0dc6ba0c11aa', '', '', 1, '#FFCC66'),
(6, 1, 2, 'Tlgo.', 'Cabascango Herrera', 'Milton Fabián', 'Tlgo. Milton Cabascango', 'Cabascango Herrera Milton Fabián', 'MILTONC', 'NC737WPy9brG5VhJAWGV8ni8BQmN9ZaXArMhaO6PzCg=', '1be3929aac7bd77c0e764fd2a15a636223f89226', '', '', 1, '#00FF00'),
(10, 1, 2, 'Lic.', 'Proaño Estrella', 'Wilson Eduardo', 'Lic. Wilson Proaño', 'Proaño Estrella Wilson Eduardo', 'WILSONP', 'k7gbrllf4+BcF/KgVsFqXUObms7D+yi5AXppsGOUjkY=', 'b41bc8a92ec69df9d1a9ed66c3b81b685be68e62', '', '', 1, '#669900'),
(19, 1, 2, 'Lic.', 'Cedeño Zambrano', 'Ninfa Edith Moncerrate', 'Lic. Edith Cedeño', 'Cedeño Zambrano Ninfa Edith Moncerrate', 'EDITHC', 'raZnoxm59AC9JDgyUnQB4lbV3EcB6rsw4UGdK94aNf8=', '', '', '', 1, '#FF6666'),
(21, 1, 2, 'Lic.', 'Mejía Segarra', 'Rómulo Oswaldo', 'Lic. Rómulo Mejía', 'Mejía Segarra Rómulo Oswaldo', 'ROMULOM', 'PUHZvdtioTYGpCxlS5pKWd60deFLDQzsSF1iTmcYzjs=', '', '', '', 1, '#008080'),
(26, 1, 2, 'Lic.', 'Salgado Araujo', 'María del Rosario', 'Lic. Rosario Salgado', 'Salgado Araujo María del Rosario', 'ROSARIOS', '55uupFaT01KlgWCY3k37zlKDfYLxLaWpo3PVTZf/eHA=', '', '', '', 1, '#FFFF00'),
(68, 3, 2, 'Lic.', 'Calero Navarrete', 'Elmo Eduardo', 'Lic. Elmo Calero', 'Calero Navarrete Elmo Eduardo', 'elmoc', '9iBymsu2VElv3Rd/75kp6gWd9DbFcjJrA5/ndJZz3K0=', '', '', '', 1, '#0000FF'),
(71, 4, 2, 'Msc.', 'Rosero Medina', 'Roberto Hernán', 'Msc. Roberto Rosero', 'Rosero Medina Roberto Hernán', 'robertos', 'rJwKFX2KdiQARwZV+4Hz35oNBKWih2sTxaNHp/PsnAw=', '', '', '', 1, '#FFFFCC'),
(73, 4, 2, 'Lic.', 'Zambrano Cedeño', 'Walter Adbón', 'Lic. Walter Zambrano', 'Zambrano Cedeño Walter Adbón', 'walterz', '7xRJgxU8McPSaBH//hd0FV407XDN/20rM69I7K04Tjw=', '', '', '', 1, '#00C080'),
(7, 5, 2, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Gonzalo Nicolás Peñaherrera Escobar', 'gonzalop', 'AlhnlffT1WlPf0wsWGnJBTGDhEmD4vG+UwQrCxhpy9k=', '', '', '', 1, '#DDFF97'),
(13, 5, 3, 'Mr.', 'Secretaria', 'Secretaria', '', 'Secretaria Secretaria', 'secretaria', 'MpJ5ikPTNIvjluneEmygjWwnpOwNBNtdx1naj5YMeq0=', '', '', '', 1, '#FFFFFF'),
