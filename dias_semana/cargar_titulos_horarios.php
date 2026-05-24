<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
    session_start();
	$horarios = new horarios();
	$horarios->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $horarios->cargarTitulosHorarios();
?>