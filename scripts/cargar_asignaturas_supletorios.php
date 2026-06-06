<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.combos.php");
	session_start();
	$asignaturas = new selects();
	$id_paralelo = $_POST["id_paralelo"];
	echo $asignaturas->cargarAsignaturasSupletorio($id_paralelo);
?>