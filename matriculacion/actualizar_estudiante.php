<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	session_start();
	$estudiante = new estudiantes();
	$estudiante->code = $_POST["id_estudiante"];
	$estudiante->id_tipo_documento = $_POST["id_tipo_documento"];
	$estudiante->id_def_genero = $_POST["id_def_genero"];
	$estudiante->id_def_nacionalidad = $_POST["id_def_nacionalidad"];
	$estudiante->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$estudiante->id_paralelo = $_POST["id_paralelo"];
	$estudiante->es_apellidos = $_POST["es_apellidos"];
	$estudiante->es_nombres = $_POST["es_nombres"];
	$estudiante->es_cedula = $_POST["es_cedula"];
	$estudiante->es_email = $_POST["es_email"];
	$estudiante->es_direccion = $_POST["es_direccion"];
	$estudiante->es_sector = $_POST["es_sector"];
	$estudiante->es_telefono = $_POST["es_telefono"];
	$estudiante->es_fec_nacim = $_POST["es_fec_nacim"];
	echo $estudiante->actualizarEstudiante();
?>
