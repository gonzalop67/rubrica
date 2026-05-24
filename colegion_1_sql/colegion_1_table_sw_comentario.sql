
DROP TABLE IF EXISTS `sw_comentario`;
CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) NOT NULL,
  `co_nombre` varchar(64) NOT NULL,
  `co_texto` varchar(250) NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_comentario` (`id_comentario`, `co_id_usuario`, `co_tipo`, `co_perfil`, `co_nombre`, `co_texto`, `co_fecha`) VALUES
(58, 1348, 1, 'ESTUDIANTE', 'CHALAN CUENCA DAYANA LIZBETH', 'espero superarme un poco mas \n', '2017-05-11 21:48:48'),
(56, 1146, 1, 'ESTUDIANTE', 'SATIAN SATIAN ANGEL POLIVIO', 'Espero Mejorar Mucho Más Este Segundo QUIMESTRE', '2017-03-04 17:29:42'),
(55, 796, 1, 'ESTUDIANTE', 'LAINES BAZAN ANDRES DAVID', 'Matemáticas es como ingles ,no entiendo pero ,luego luego se me queda algo', '2017-02-26 21:57:38'),
(59, 1136, 1, 'ESTUDIANTE', 'PASTUÑA TOAQUIZA LUZ MELIDA', 'gracias mi colegio salamanca , falta poco y termina el año escolar, a seguir estudiando', '2017-06-29 05:22:16'),
(60, 742, 1, 'ESTUDIANTE', 'MARCALLA SILLO JHON PAUL', 'gracias por todo su apoyo profesores fueron una gran enseñanzas para nosotros para poder seguir adelante con nuestros estudio  y espero superarme  un poco mas  en mis estudios\n', '2017-07-01 17:28:35'),
(61, 800, 1, 'ESTUDIANTE', 'QUINTIN QUINTIN OSCAR EDUARDO', 'en este nuevo año mejorare mas mi sos pilas \n', '2017-07-08 14:18:04'),
(62, 544, 1, 'ESTUDIANTE', 'LECHON YANEZ MARIA ERLINDA', 'Acradesco por la informaciòn proporcionada por este medio.\n', '2017-07-11 00:22:49'),
(65, 1192, 1, 'ESTUDIANTE', 'BAÑO VAQUILEMA LUIS FAIDEBER', 'espero el siguiente año lectivo superarme mucho mas....', '2017-07-13 21:38:34'),
(66, 1270, 1, 'ESTUDIANTE', 'MACIAS VILLARROEL JAJAIRA ARACELY', 'Buenas tardes Me pueden decir cuando son los remediales', '2017-07-26 21:04:27'),
(67, 986, 1, 'ESTUDIANTE', 'AJON GREFA CARLOS ALFREDO', 'bueno este año ha sido un poquito complicado pero e salido adelante gracias a todos losq confiaron en mi y a los profesores tambien  en noveno a mejorar un poco mas\n', '2017-07-27 22:47:06'),
(68, 990, 1, 'ESTUDIANTE', 'GUACAN COLCHA FANNY MARISOL', 'HOLA   CHICOS ESPERO  VERLES   EL  OTRO  AÑO  EN  DECIMO ..Y  SII  FUE UN  POCO  COMPLICADO  ESTE   AÑO  PERO  YA   SE   ACAVOO\nNOS   ESPERA   OTRO  AÑO  MAS   Y  NO  SE   QUEDEN  SIGAN  ADELANTEE   \n', '2017-07-27 23:57:48'),
(69, 209, 1, 'ESTUDIANTE', 'CANDO ACOSTA ALEXIS RONALDO', 'Cuando se dan los examenes Remediales ?', '2017-08-20 19:35:28'),
(70, 1307, 1, 'ESTUDIANTE', 'PINO TENECELA DIEGO RENE', 'Gracias profesores de mi querido colegio SALAMANCA. POR QUE A PESAR DE LOS ENOJOS Q LES HACEMOS DAR AUN ASI NOS SIGUEN ENSEÑANDO CON TODA SU DEDICACION. GRACIAS A USTEDES YO Y MIS COMPAÑEROS SEGUMOS ESTUDIANDO PARA CULMINAR NUESTROS ESTUDIOS ESTUDIOS', '2017-08-22 22:24:55'),
(71, 800, 1, 'ESTUDIANTE', 'QUINTIN QUINTIN OSCAR EDUARDO', 'Voy a ponerle mas ñeque en las materias muchachos todo esta en uno mismo bamos pa lante!!!????????', '2017-12-05 12:24:16'),
(72, 796, 1, 'ESTUDIANTE', 'LAINES BAZAN ANDRES DAVID', 'Que significa sin examen. .  ? \nQue pasó sin dar el examen .? ?? \nO que no esta la nota del examen...', '2018-06-29 04:16:22'),
(73, 1430, 1, 'ESTUDIANTE', 'SERAQUIVE CARRERA PAOLA LIZBETH', 'Porque no salen las notas de los exámenes como se si esque paso o no \n', '2018-07-03 02:49:29'),
(74, 1128, 1, 'ESTUDIANTE', 'MONTALVO MALDONADO MARIO JAVIER', 'muy buenas noches lic tenga la bondad digame cuando va a subir las notas por que me falta de matematica que me apruebe  por fa licen diego peñafiel le saluda sr mario montalvo\n', '2018-07-04 00:35:28'),
(75, 800, 1, 'ESTUDIANTE', 'QUINTIN QUINTIN OSCAR EDUARDO', 'No lo puedo creer primera vez en supletorio ha echale ganas no queda mas papa', '2018-07-04 01:57:32'),
(76, 1546, 1, 'ESTUDIANTE', 'SALDARRIAGA NARANJO ALEJANDRO ESTEVAN', 'cuando podemos dar el examen supletorio de progamacion', '2018-07-04 21:42:56'),
(77, 1429, 1, 'ESTUDIANTE', 'FONSECA PURUNCAJAS EDISON ANDRES', 'que felicidad no me e quedado en nada a seguir con muchas ganas', '2018-07-04 22:37:34'),
(78, 986, 1, 'ESTUDIANTE', 'AJON GREFA CARLOS ALFREDO', 'BUeno me he quedado a supletorio solo espero pasar mi trabajo no me da para seguir estudiando pero daré lo que más pueda gracias licen Walter Zambrano por todo si Dios lo permite seguiré el año que viene con más entusiasmo y a mis compañeros del 9no ', '2018-07-05 15:50:41'),
(82, 1138, 1, 'ESTUDIANTE', 'PAVON CUASQUE ANDERSON LEONEL', 'Gracias  por  todo  queridos   licenciados \n', '2018-07-11 01:10:24');
