<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horarios = new horarios();
    $id_horario_def = $_POST["id_horario_def"];
	echo $horarios->eliminarTituloHorario($id_horario_def);
?>