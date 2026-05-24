<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_curso = $_POST["id_curso"];

$qry = "SELECT pe.id_periodo_evaluacion, 
			   pe.pe_nombre, 
			   pc.id_periodo_evaluacion_curso,  
			   pc.pe_ponderacion, 
			   pc.pc_orden 
		  FROM sw_periodo_evaluacion pe, 
			   sw_periodo_evaluacion_curso pc 
		 WHERE pe.id_periodo_lectivo = pc.id_periodo_lectivo 
		   AND pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
		   AND pc.id_periodo_lectivo = $id_periodo_lectivo 
		   AND pc.id_curso = $id_curso 
		 ORDER BY pc.pc_orden ASC";

// die(var_dump($qry));

$consulta = $db->consulta($qry);

$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
	while ($periodos_evaluacion = $db->fetch_assoc($consulta)) {
		$id = $periodos_evaluacion["id_periodo_evaluacion"];
		$id_pc = $periodos_evaluacion["id_periodo_evaluacion_curso"];
		$name = $periodos_evaluacion["pe_nombre"];
		$orden = $periodos_evaluacion["pc_orden"];
		$ponderacion = $periodos_evaluacion["pe_ponderacion"] * 100;

		$cadena .= "<tr data-index='$id_pc' data-orden='$orden'>\n";

		$cadena .= "<td>$id</td>\n";
		$cadena .= "<td>$name</td>\n";
		// $cadena .= "<td>$ponderacion%</td>\n";

		$cadena .= "<td>\n";
		$cadena .= "<div class='btn-group'>\n";
		$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarPeriodoEvaluacionModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
		$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarPeriodoEvaluacion(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
		$cadena .= "</div>";
		$cadena .= "</td>\n";

		$cadena .= "</tr>\n";
	}
} else {
	$cadena .= "<tr>\n";
	$cadena .= "<td colspan='4' align='center'>No se han definido Periodos de Evaluaci&oacute;n...</td>\n";
	$cadena .= "</tr>\n";
}

echo $cadena;
