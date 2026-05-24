<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.dias_semana.php");
	$dia_semana = new dias_semana();
	$dia_semana->ds_nombre = $_POST["ds_nombre"];
	$dia_semana->id_horario_def = $_POST["id_horario_def"];
	echo $dia_semana->insertarDiaSemana();
?>
