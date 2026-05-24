<?php
    session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
    $tipo_educacion = new tipos_educacion();
    $tipo_educacion->code = $_POST["id_tipo_educacion"];
    $tipo_educacion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $tipo_educacion->bajarNivelEducacion();
?>
