<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.jornadas.php");
	$jornada = new jornadas();
	echo $jornada->cargar_jornadas();
?>