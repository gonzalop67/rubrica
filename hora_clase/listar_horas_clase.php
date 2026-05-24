<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.horas_clase.php");
	$hora_clase = new horas_clase();
	$hora_clase->id_horario_def = $_POST["id_horario_def"];
	echo $hora_clase->listar_horas_clase();
?>
