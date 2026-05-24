<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.modalidades.php");
	$modalidad = new modalidades();
	$modalidad->mo_nombre = $_POST["mo_nombre"];
	$modalidad->mo_activo = $_POST["mo_activo"];
	echo $modalidad->insertarModalidad();
?>
