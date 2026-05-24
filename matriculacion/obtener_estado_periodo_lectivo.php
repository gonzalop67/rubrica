<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.estudiantes.php");
    session_start();
    $estudiante = new estudiantes();
    $estudiante->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    echo $estudiante->obtenerEstadoPeriodoLectivo();
?>