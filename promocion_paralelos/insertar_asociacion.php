<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.promociones.php");
	session_start();
	$promociones = new promociones();
	$promociones->id_paralelo_actual = $_POST["id_paralelo_actual"];
	$promociones->id_paralelo_anterior = $_POST["id_paralelo_anterior"];
	$promociones->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $promociones->asociarParalelosPromocion();
?>
