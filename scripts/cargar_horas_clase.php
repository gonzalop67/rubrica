<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	$select = new selects();
	$select->id_dia_semana = $_POST["id_dia_semana"];
	$select->id_horario_def = $_POST["id_horario_def"];
	echo $select->cargarHorasClase();
?>