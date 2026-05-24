<?php
include("../scripts/clases/class.mysql.php");
// include("../scripts/clases/class.paralelos.php");
// $paralelos = new paralelos();
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

function listarComportamientosPorTutores()
{
	global $db, $id_paralelo, $id_periodo_evaluacion;

	$cadena = "";

	$qry = "SELECT e.id_estudiante, "
		. "       e.es_apellidos, "
		. "       e.es_nombres "
		. "  FROM sw_estudiante_periodo_lectivo ep, "
		. "       sw_estudiante e "
		. " WHERE ep.id_estudiante = e.id_estudiante "
		. "   AND ep.id_paralelo = " . $id_paralelo
		. "   AND es_retirado = 'N' "
		. "   AND activo = 1 "
		. " ORDER BY es_apellidos, es_nombres ASC";

	$consulta = $db->consulta($qry);
	$num_total_registros = $db->num_rows($consulta);
	$cadena = "";
	$qry = "";
	if ($num_total_registros > 0) {
		$contador = 0;
		while ($paralelo = $db->fetch_assoc($consulta)) {
			$contador++;
			$cadena .= "<tr>\n";
			$id_estudiante = $paralelo["id_estudiante"];
			$apellidos = $paralelo["es_apellidos"];
			$nombres = $paralelo["es_nombres"];
			$cadena .= "<td>$contador</td>\n";
			$cadena .= "<td>$id_estudiante</td>\n";
			$cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";

			$qryString = "SELECT id_aporte_evaluacion 
				       FROM sw_aporte_evaluacion 
					  WHERE id_tipo_aporte = 1 
					    AND id_periodo_evaluacion = $id_periodo_evaluacion";

			$aportes_evaluacion = $db->consulta($qryString);

			$suma_comportamiento = 0;
			$num_total_registros = $db->num_rows($aportes_evaluacion);

			while ($aporte_evaluacion = $db->fetch_object($aportes_evaluacion)) {
				$id_aporte_evaluacion = $aporte_evaluacion->id_aporte_evaluacion;

				$queryString = "SELECT id_escala_comportamiento "
					. "  FROM sw_comportamiento_tutor "
					. " WHERE id_estudiante = " . $id_estudiante
					. "   AND id_paralelo = " . $id_paralelo
					. "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion;

				$query = $db->consulta($queryString);

				if ($num_total_registros > 0) {
					$comportamiento_estudiante = $db->fetch_assoc($query);
					$calificacion = $comportamiento_estudiante["id_escala_comportamiento"];
				} else {
					$calificacion = 0;
				}

				$suma_comportamiento += $calificacion;
			}

			$comportamiento = ceil($suma_comportamiento / $num_total_registros);

			$qry = "SELECT * FROM sw_escala_comportamiento WHERE id_escala_comportamiento = $comportamiento";

			$query = $db->consulta($qry);
			$num_total_registros = $db->num_rows($query);

			if ($num_total_registros > 0) {
				$escala_comportamiento = $db->fetch_assoc($query);
				$comportamiento_estudiante = $escala_comportamiento["ec_equivalencia"];
			} else {
				$comportamiento_estudiante = "";
			}

			$cadena .= "<td>\n";
			$cadena .= "<input type='text' value='$comportamiento_estudiante' disabled />\n";
			$cadena .= "</td>\n";

			$cadena .= "</tr>\n";
		}
	} else {
		$cadena .= "<tr>\n";
		$cadena .= "<td colspan=\"4\" class=\"text-center\">No se han matriculado estudiantes en este paralelo...</td>\n";
		$cadena .= "</tr>\n";
	}

	return $cadena;
}

$db = new MySQL();
// Obtener quien inserta comportamiento
$consulta = $db->consulta("SELECT nombre 
	FROM sw_periodo_lectivo pl, 
	     sw_quien_inserta_comp co 
	WHERE co.id = pl.quien_inserta_comp_id 
	AND id_periodo_lectivo = $id_periodo_lectivo");
$quien_inserta_comp = $db->fetch_object($consulta)->nombre;
if ($quien_inserta_comp == "Tutor") {
	echo listarComportamientosPorTutores();
	// echo "Tutor";
} else {
	// echo $paralelos->listarEstudiantesComportamiento($id_paralelo, $id_periodo_evaluacion);
	echo "Docentes";
}
