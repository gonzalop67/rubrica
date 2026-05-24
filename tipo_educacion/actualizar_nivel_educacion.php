<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	session_start();
	$tipos_educacion = new tipos_educacion();
	$tipos_educacion->code = $_POST["id_tipo_educacion"];
    $tipos_educacion->te_nombre = $_POST["te_nombre"];
    $tipos_educacion->te_bachillerato = $_POST["te_bachillerato"];
	$tipos_educacion->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $tipos_educacion->actualizarNivelEducacion();
?>
