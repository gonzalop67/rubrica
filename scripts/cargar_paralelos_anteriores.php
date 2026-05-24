<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.combos.php");
	session_start();
	$selects = new selects();
	$id_modalidad = $_SESSION["id_modalidad"];
	// die(var_dump($id_modalidad));
	echo $selects->cargarParalelosAnteriores($id_modalidad);
?>