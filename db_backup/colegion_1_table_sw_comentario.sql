
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comentario`
--

DROP TABLE IF EXISTS `sw_comentario`;
CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_comentario_padre` int(11) NOT NULL,
  `id_usuario_para` int(11) NOT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) NOT NULL,
  `co_nombre` varchar(64) NOT NULL,
  `co_texto` varchar(250) NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_comentario`
--

INSERT INTO `sw_comentario` (`id_comentario`, `id_comentario_padre`, `id_usuario_para`, `co_id_usuario`, `co_tipo`, `co_perfil`, `co_nombre`, `co_texto`, `co_fecha`) VALUES
(122, 0, 1, 1777, 1, 'ESTUDIANTE', 'EXAVIER JEAN LOUIS', 'Jean Louis  Exavier ', '2022-12-16 14:06:12'),
(128, 0, 1, 545, 1, 'ESTUDIANTE', 'TINAJERO NEIRA VICTOR ENRIQUE', 'buenas tardes a todos los profesores todos mis agradecimitosde todos estos  tiempos y me supieron sacar adelante     valores extremos de otro nivel  y abrazos bien grandes para todos y voy  y abrazo bien grande para linciciada salazar', '2022-12-16 14:06:12'),
(60, 0, 1, 742, 1, 'ESTUDIANTE', 'MARCALLA SILLO JHON PAUL', 'gracias por todo su apoyo profesores fueron una gran enseñanzas para nosotros para poder seguir adelante con nuestros estudio  y espero superarme  un poco mas  en mis estudios\n', '2022-12-16 14:06:12'),
(190, 0, 0, 1761, 1, 'ESTUDIANTE', 'TENE PILCO EDISON FABIAN', 'buenas tardes licenciado soy edison fabian tene pilco  me podria ayudar con el retiro de mis papeles  por favor y muchas gracias ,que tenga una excelente tarde', '2023-04-17 15:59:42'),
(62, 0, 1, 544, 1, 'ESTUDIANTE', 'LECHON YANEZ MARIA ERLINDA', 'Acradesco por la informaciòn proporcionada por este medio.\n', '2022-12-16 14:06:12'),
(189, 0, 0, 1761, 1, 'ESTUDIANTE', 'TENE PILCO EDISON FABIAN', 'si me puede ayudar a retirar los papeles', '2023-04-17 15:52:56'),
(67, 0, 1, 986, 1, 'ESTUDIANTE', 'AJON GREFA CARLOS ALFREDO', 'bueno este año ha sido un poquito complicado pero e salido adelante gracias a todos losq confiaron en mi y a los profesores tambien  en noveno a mejorar un poco mas\n', '2022-12-16 14:06:12'),
(68, 0, 1, 990, 1, 'ESTUDIANTE', 'GUACAN COLCHA FANNY MARISOL', 'HOLA   CHICOS ESPERO  VERLES   EL  OTRO  AÑO  EN  DECIMO ..Y  SII  FUE UN  POCO  COMPLICADO  ESTE   AÑO  PERO  YA   SE   ACAVOO\nNOS   ESPERA   OTRO  AÑO  MAS   Y  NO  SE   QUEDEN  SIGAN  ADELANTEE   \n', '2022-12-16 14:06:12'),
(132, 0, 1, 307, 1, 'ESTUDIANTE', 'CUASPUD LOPEZ JUAN CARLOS', 'Me inscribí para el periodo 2020-2021 y quiero saber como hago para actualizar mis datos e ingresar a las clases', '2022-12-16 14:06:12'),
(70, 0, 1, 1307, 1, 'ESTUDIANTE', 'PINO TENECELA DIEGO RENE', 'Gracias profesores de mi querido colegio SALAMANCA. POR QUE A PESAR DE LOS ENOJOS Q LES HACEMOS DAR AUN ASI NOS SIGUEN ENSEÑANDO CON TODA SU DEDICACION. GRACIAS A USTEDES YO Y MIS COMPAÑEROS SEGUMOS ESTUDIANDO PARA CULMINAR NUESTROS ESTUDIOS ESTUDIOS', '2022-12-16 14:06:12'),
(131, 0, 1, 2146, 1, 'ESTUDIANTE', 'ROSALES CALDERON ALAN YULI', 'buenas tardes mil disculpe la molestia era para saber sobre las matriculas \r\n', '2022-12-16 14:06:12'),
(130, 0, 1, 1591, 1, 'ESTUDIANTE', 'QUISPE MONTALVO DIANA CAROLINA', 'buena stardes    queria saber par la matriculade este nuevo año lectivo 2020', '2022-12-16 14:06:12'),
(75, 0, 1, 800, 1, 'ESTUDIANTE', 'QUINTIN QUINTIN OSCAR EDUARDO', 'No lo puedo creer primera vez en supletorio ha echale ganas no queda mas papa', '2022-12-16 14:06:12'),
(77, 0, 1, 1429, 1, 'ESTUDIANTE', 'FONSECA PURUNCAJAS EDISON ANDRES', 'que felicidad no me e quedado en nada a seguir con muchas ganas', '2022-12-16 14:06:12'),
(78, 0, 1, 986, 1, 'ESTUDIANTE', 'AJON GREFA CARLOS ALFREDO', 'BUeno me he quedado a supletorio solo espero pasar mi trabajo no me da para seguir estudiando pero daré lo que más pueda gracias licen Walter Zambrano por todo si Dios lo permite seguiré el año que viene con más entusiasmo y a mis compañeros del 9no ', '2022-12-16 14:06:12'),
(82, 0, 1, 1138, 1, 'ESTUDIANTE', 'PAVON CUASQUE ANDERSON LEONEL', 'Gracias  por  todo  queridos   licenciados \n', '2022-12-16 14:06:12'),
(87, 0, 1, 1724, 1, 'ESTUDIANTE', 'TOAPANTA VILLAMARIN WENDY GABRIELA', 'Gracias a todos los licenciados por este año que acabo', '2022-12-16 14:06:12'),
(89, 0, 1, 1107, 1, 'ESTUDIANTE', 'CALAPI MORALES JOSE VICENTE', 'Mucha gracias a todo los lecenciados  por todo el año lectivo muchas gracias, pero de igual manera me quedo en sopletorios y remediales no hay más toca luchar', '2022-12-16 14:06:12'),
(174, 173, 1, 587, 2, 'Docente', 'Ing. Gonzalo Peñaherrera', 'ok, ya está corregido', '2022-12-16 14:06:12'),
(137, 132, 1, 2364, 1, 'ESTUDIANTE', 'PUJOTA JIMENEZ LUIS SANTIAGO', 'Me inscribí para noveno pero no se ninguna respuesta me quedaron en dar el número del tutor y Nada por favor ayuden en con eso', '2022-12-16 14:06:12'),
(91, 0, 1, 800, 1, 'ESTUDIANTE', 'ALTAMIRANO QUINTIN OSCAR EDUARDO', 'hasta el final con ñeque ultimo año  inf', '2022-12-16 14:06:12'),
(92, 0, 1, 268, 1, 'ESTUDIANTE', 'RUIZ SOLANO CRISTIAN PAUL', 'hola disculpa estaba orbservando mis calificaciones y en algunas no hay las calificaciones por si acaso es falla del sistema o que cosa', '2022-12-16 14:06:12'),
(93, 0, 1, 545, 1, 'ESTUDIANTE', 'TINAJERO NEIRA VICTOR ENRIQUE', 'Gracias por el apoyo que nos da los linciados  para salir adelante para superar nuestra metas', '2022-12-16 14:06:12'),
(138, 0, 1, 1896, 1, 'ESTUDIANTE', 'QUILO CACOANGO CRISTIAN ALI', 'Estimados buenas tardes, \r\npor favor me podrían indicar como puedo sacar un certificado de que me encuentro estudiando en esta institución?\r\n\r\nMuchas gracias.', '2022-12-16 14:06:12'),
(99, 0, 1, 545, 1, 'ESTUDIANTE', 'TINAJERO NEIRA VICTOR ENRIQUE', 'Gracias por los profesores que nos apoyaron y seguimos adelante  aunque estemos separados en el colegio tenemos que ganar una batalla ', '2022-12-16 14:06:12'),
(103, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'La importance de  aprender   a ensenar Los ESTUDIANTES  RELACion con el aprendizaje   con los PROFESORES ', '2022-12-16 14:06:12'),
(104, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'La IMPORTANCIa de apender', '2022-12-16 14:06:12'),
(105, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'aurorita UNAPANTA MUNIVE  gracias  Por  PROFESORES  que  nos  apoyaron  y Segundo  ABELANTE  mis estudios ', '2022-12-16 14:06:12'),
(106, 103, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'MARIA  AURORITA Unapanta MUNIVE  mis estudios Muchas mis profesor de fesica Wilso biologia Roberto  Rosero de ingles  Alicia  Historia profesor Luis  y lenguaje Rosario  y MATEMATICAS   EMPRENDEMIENTo y gestion  TODos  los PROFESORES muchas  gracias ', '2022-12-16 14:06:12'),
(107, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'AURORITA Unapanta profesor WALTER Y PROFESOR  ROBERTO ROSERO MUCHAS GRACIAS los cuides Angelitos  MIS  estudios  biologia y ciencias NATURAL  senorita  Alicia  quiero mucho  todos PROFESORES  muchas gracias  de   todo fisica   profesor LUIS  MATEMATI', '2022-12-16 14:06:12'),
(109, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'AURORITA Unapanta MUNIVE  BIOLOGIA me GUSTA Biologia Roberto Rosero  y quimica y Elmo lenguaje ROSARIO  y histuria  LUIS  y ingles  Alicia  y fisica  Wilson  ', '2022-12-16 14:06:12'),
(110, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'AURORITA UNAPANTA MUNIVE BIOLOGIA Roberto y Ciencias Naturales  y PROFESOR HISTORIA  Luis Y profesor QUIMICA ELMO  de INGLES senorita Alicia  senorita Rosario  lenguaje  de profesor fesica   MATEMATICAS profesor  Efren  profesor WALTER de EMPRENDIEMT', '2022-12-16 14:06:12'),
(111, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'AURORITA Unapanta MUNIVE  mis ESTUDIOS MUCHAS  GRACIAS PROFESOR ROBERTO ROSERO MUCHAS GRACIAS y profesor QUIMICA ELMO DE INGLES senorita Alicia senorita Rosario lenguaje DE PROFESOR FESICA  Wilson profesor HISTORIA Luis de EMPRENDIEMTO  y gestion WEL', '2022-12-16 14:06:12'),
(177, 0, 68, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:51:55'),
(159, 0, 1, 2320, 1, 'ESTUDIANTE', 'CAGUA FARIAS MARIA ESTEFANIA', 'Buenos días LICENCIADos gracias  x su apoyo  culmine otro año  mas', '2022-12-16 14:06:12'),
(113, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'Maria AURORITa UNAPANTA MUNIVE  ENCESNAZA aprender  a leer BIN  MATEMATICAS Efren lenguaje  DE biologia Roberto  Rosero  de quimica de Historia de fisica luis de  INGLES  SENORITA ALICIA mis Profesores muchas GRACIAS  de TODOS simpre esta  los ESTUDI', '2022-12-16 14:06:12'),
(114, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'MARIA AURORITa UNAPANTA MUNIVE  GRACIAS profesores Efren  PROFESOR WALTER de Roberto ROSERO  profesor luis Senorita Alicia senorita Rosario  profesor Wilson  muchas GRACIAS CONFEansia leer ESPREBIR ESTUDIAr  DIOSITO  cuende MUCHo mas  los PROFESORES ', '2022-12-16 14:06:12'),
(115, 0, 1, 558, 1, 'ESTUDIANTE', 'UNAPANTA MUNIVE MARIA AURORA', 'MARIA AURORITa UNAPANTA MUNIVE  MUCHAS GRACIAS  de todo  quiridios  Profesores ROBERTO ROSERO  Elmo  Alicia  luis  Efren  y FISICA  lenguaje ROSARIO y Walton quiridios PROFESORES MUCHAS GRACIAS  de todo Los Angelitos  Blancos ', '2022-12-16 14:06:12'),
(117, 0, 1, 545, 1, 'ESTUDIANTE', 'TINAJERO NEIRA VICTOR ENRIQUE', 'Buenos dias profesores lo doy todo mi adracimiento por el apoyo que supe salir adelante sin un paso atras especialmente las materias de biologia quimica matematicas ingles y todos profesores todo mi agradeciento por los valores y todo actitud supe lo', '2022-12-16 14:06:12'),
(118, 0, 1, 2066, 1, 'ESTUDIANTE', 'PIERRE LECNY', 'Quiero revisar mis notas', '2022-12-16 14:06:12'),
(123, 122, 1, 1777, 1, 'ESTUDIANTE', 'EXAVIER JEAN LOUIS', 'Quiero revisar mi notas ', '2022-12-16 14:06:12'),
(124, 123, 1, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'por favor ingrese al menu consultar y a la opcion anuales, allí tendrá las calificaciones del año lectivo', '2022-12-16 14:06:12'),
(125, 118, 1, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'POR FAVOR INGRESE AL MENU CONSULTAR Y A LA OPCION ANUALES, ALLÍ TENDRÁ LAS CALIFICACIONES DEL AÑO LECTIVO', '2022-12-16 14:06:12'),
(129, 0, 1, 545, 1, 'ESTUDIANTE', 'TINAJERO NEIRA VICTOR ENRIQUE', 'buenas tardes a todos los profesores todos mis agradecimitosde todos estos  tiempos y me supieron sacar adelante     valores extremos de otro nivel  y abrazos bien grandes para todos y voy  y abrazo bien grande para linciciada salazar', '2022-12-16 14:06:12'),
(133, 132, 1, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'por favor comunicarse con el lic. romulo mejia rector de la institucion, numero de celular: +593 97 921 8583', '2022-12-16 14:06:12'),
(134, 131, 1, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'POR FAVOR COMUNICARSE CON EL LIC. ROMULO MEJIA RECTOR DE LA INSTITUCION, NUMERO DE CELULAR: +593 97 921 8583', '2022-12-16 14:06:12'),
(135, 130, 1, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'POR FAVOR COMUNICARSE CON EL LIC. ROMULO MEJIA RECTOR DE LA INSTITUCION, NUMERO DE CELULAR: +593 97 921 8583', '2022-12-16 14:06:12'),
(140, 0, 1, 2407, 1, 'ESTUDIANTE', 'CADENA VIRACUCHA DAISY ANAHI', 'Cuando  podre ver mis notas disculpe licenciados ', '2022-12-16 14:06:12'),
(173, 0, 1, 3104, 1, 'ESTUDIANTE', 'CONFORME CUSME LITA ALEXANDRA', 'Lida es no lita', '2022-12-16 14:06:12'),
(142, 140, 1, 587, 2, 'Docente', 'Ing. Gonzalo Peñaherrera', 'hola, las notas se irán subiendo de acuerdo a cuando vayan revisando y calificando los docentes', '2022-12-16 14:06:12'),
(169, 0, 71, 2917, 1, 'ESTUDIANTE', 'CHILUISA YUGCHA JESSICA MARISOL', 'Licen quiero saber mis notas de quimica y biologia', '2022-12-21 19:37:50'),
(170, 0, 26, 2431, 1, 'ESTUDIANTE', 'VINUEZA MANOBANDA JENNY MARGARITA', 'Licen Charito buenas noches quería saber porque aún no me pasa las notas si yo ya le presente todo ', '2022-12-21 19:23:05'),
(176, 0, 598, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:50:29'),
(178, 0, 19, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:53:46'),
(179, 0, 3, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:54:18'),
(180, 0, 15, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:54:32'),
(181, 0, 21, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:54:58'),
(182, 0, 20, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:55:15'),
(183, 0, 10, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:55:31'),
(184, 0, 71, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:55:52'),
(185, 0, 599, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:56:06'),
(186, 0, 73, 1, 2, 'Administrador', 'Ing. Gonzalo Peñaherrera', 'BUENOS DIAS TARDES O NOCHES, POR FAVOR INGRESAR LA NOTA DEL EXAMEN BIMESTRAL EN LOS CURSOS DE BGU INTENSIVO, ESTA NOTA CORRESPONDE AL PROMEDIO DE LOS PARCIALES.', '2022-12-21 19:56:23'),
(192, 0, 0, 3188, 1, 'ESTUDIANTE', 'SANCHEZ ROSILLO LEONARDO DAVID', ' Licen como ago para retirar mis papeles ', '2024-07-10 01:30:49');
