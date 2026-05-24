<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$selects = new selects();
	$selects->id_horario_def = $_POST["id_horario_def"];
	echo $selects->cargarDiasSemana();
?>