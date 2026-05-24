<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asistencias.php");
	$asistencia = new asistencias();
    $asistencia->code = $_POST["id_asistencia_estudiante"];
    $asistencia->in_abreviatura = $_POST["in_abreviatura"];
    $asistencia->at_justificacion = $_POST["at_justificacion"];
	echo $asistencia->actualizarJustificacionEstudianteTutor();
?>
