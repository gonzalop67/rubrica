<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$selects = new selects();
	$selects->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $selects->cargarParalelosPeriodoLectivo();
?>