<?php
session_start();
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$qry = "SELECT id_paralelo, 
			   pa_nombre, 
			   es_figura AS especialidad, 
			   cu_nombre AS curso, 
			   jo_nombre, 
			   pa_orden
		  FROM sw_paralelo p, 
			   sw_curso c, 
			   sw_especialidad e, 
			   sw_jornada j 
		 WHERE c.id_curso = p.id_curso 
		   AND e.id_especialidad = c.id_especialidad 
		   AND j.id_jornada = p.id_jornada 
		   AND id_periodo_lectivo = $id_periodo_lectivo
	     ORDER BY pa_orden ASC";

try {
	$consulta = $db->consulta($qry);

	$num_rows = $db->num_rows($consulta);

	$cadena = "";

	if ($num_rows > 0) {
		$contador = 0;
		while ($paralelo = $db->fetch_assoc($consulta)) {
			$contador++;
			$id = $paralelo["id_paralelo"];
			$name = $paralelo["pa_nombre"];
			$especialidad = $paralelo["especialidad"];
			$curso = $paralelo["curso"];
			$jornada = $paralelo["jo_nombre"];
			$orden = $paralelo["pa_orden"];
			$cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
			$cadena .= "<td>$contador</td>\n";
			$cadena .= "<td>$especialidad</td>\n";
			$cadena .= "<td>$curso</td>\n";
			$cadena .= "<td>$name</td>\n";
			$cadena .= "<td>$jornada</td>\n";
			$cadena .= "<td>\n";
			$cadena .= "<div class='btn-group'>\n";
			$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarParaleloModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
			$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarParalelo(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
			$cadena .= "</div>\n";
			$cadena .= "</td>\n";
			$cadena .= "</tr>\n";
		}
	} else {
		$cadena .= "<tr>\n";
		$cadena .= "<td colspan='8' align='center'>No se han definido paralelos en este periodo lectivo...</td>\n";
		$cadena .= "</tr>\n";
	}

	echo $cadena;
} catch (Exception $e) {
	echo $e->getMessage();
}
