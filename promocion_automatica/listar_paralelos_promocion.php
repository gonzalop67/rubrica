<?php
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.promociones.php");
	$promociones = new promociones();
	$promociones->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $promociones->cargarParalelosAPromocionar();
?>
