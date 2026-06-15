<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.institucion.php");
	$institucion = new institucion();
	$institucion->in_nombre = $_POST["in_nombre"];
	$institucion->in_direccion = $_POST["in_direccion"];
	$institucion->in_telefono = $_POST["in_telefono"];
	$institucion->in_regimen = $_POST["in_regimen"];
	$institucion->in_nom_rector = $_POST["in_nom_rector"];
	$institucion->in_genero_rector = $_POST["in_genero_rector"];
	$institucion->in_nom_vicerrector = $_POST["in_nom_vicerrector"];
	$institucion->in_genero_vicerrector = $_POST["in_genero_vicerrector"];
	$institucion->in_nom_secretario = $_POST["in_nom_secretario"];
	$institucion->in_genero_secretario = $_POST["in_genero_secretario"];
	$institucion->in_copiar_y_pegar = $_POST["in_copiar_y_pegar"];
	$institucion->in_amie = $_POST["in_amie"];
	$institucion->in_ciudad = $_POST["in_ciudad"];
	echo $institucion->actualizarDatosInstitucion();
?>
