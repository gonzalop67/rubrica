<?php
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$id_usuario = $_SESSION["id_usuario"];
	$id_horario_def = $_POST["id_horario_def"];
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $horario->listarHorarioDocente($id_usuario, $id_horario_def);
?>
