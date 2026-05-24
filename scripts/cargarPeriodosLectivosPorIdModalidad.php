<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$selects = new selects();
	$selects->id_modalidad = $_POST["id_modalidad"];
	echo $selects->cargarPeriodosLectivosPorIdModalidad();
?>