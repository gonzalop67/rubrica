<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_lectivos.php");

	$periodo_lectivo = new periodos_lectivos();

	$periodo_lectivo->id_oferta_educativa = $_POST["id_oferta_educativa"];
	$periodo_lectivo->pe_anio_inicio = $_POST["anio_inicial"];
	$periodo_lectivo->pe_anio_fin = $_POST["anio_final"];
	$periodo_lectivo->pe_fecha_inicio = $_POST["fec_ini"];
	$periodo_lectivo->pe_fecha_fin = $_POST["fec_fin"];
	$periodo_lectivo->pe_nota_minima = $_POST["nota_minima"];
	$periodo_lectivo->pe_nota_aprobacion = $_POST["nota_aprobacion"];
	$periodo_lectivo->quien_inserta_comp_id = $_POST["quien_inserta_comp"];

	echo $periodo_lectivo->insertarPeriodoLectivo();
?>
