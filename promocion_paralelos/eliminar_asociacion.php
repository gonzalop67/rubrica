<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.promociones.php");
	$promociones = new promociones();
	$promociones->code = $_POST["id"];
	echo $promociones->eliminarAsociacion();
?>
