<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.mallas.php");
	$malla = new mallas();
	$malla->id_curso = $_POST["id_curso"];
	echo $malla->listarMalla();
?>
