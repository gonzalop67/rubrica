<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horarios = new horarios();
	$horarios->code = $_POST["id"];
	echo $horarios->obtenerTituloHorario();
?>