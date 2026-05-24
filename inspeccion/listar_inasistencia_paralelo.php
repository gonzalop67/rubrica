<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asistencias.php");
	$asistencia = new asistencias();
	$asistencia->id_paralelo = $_POST["id_paralelo"];
    $asistencia->at_fecha = $_POST["at_fecha"];
	echo $asistencia->listarInasistenciaParaleloTutor();
?>
