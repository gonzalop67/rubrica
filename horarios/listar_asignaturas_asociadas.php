<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$id_paralelo = $_POST["id_paralelo"];
	$id_dia_semana = $_POST["id_dia_semana"];
	$id_horario_def = $_POST["id_horario_def"];
	echo $horario->listarHorarioParalelo($id_paralelo, $id_dia_semana, $id_horario_def);
?>
