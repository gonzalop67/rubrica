<?php
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.promociones.php");
	$promociones = new promociones();
    $promociones->code = $_POST["id"];
	$promociones->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $promociones->PromocionarParalelo();
?>
