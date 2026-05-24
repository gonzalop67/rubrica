<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_periodo.php");
	$tipos_periodo = new tipos_periodo();
	echo $tipos_periodo->cargar_tipos_periodo();
?>