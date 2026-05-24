<?php
require_once("../scripts/clases/class.mysql.php");
$db = new mysql();

$id_paralelo = $_POST["id_paralelo"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

function listarComportamientosPorTutores()
{
	global $db, $id_paralelo, $id_aporte_evaluacion;

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

			$qry = "SELECT co_calificacion, "
				. "       id_escala_comportamiento "
				. "  FROM sw_comportamiento_tutor "
				. " WHERE id_estudiante = " . $id_estudiante
				. "   AND id_paralelo = " . $id_paralelo
				. "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion;

			$query = $db->consulta($qry);

			$num_total_registros = $db->num_rows($query);

			if ($num_total_registros > 0) {
				$comportamiento_estudiante = $db->fetch_assoc($query);
				$calificacion = $comportamiento_estudiante["co_calificacion"];
				$id_escala_comportamiento = $comportamiento_estudiante["id_escala_comportamiento"];
			} else {
				$calificacion = "";
				$id_escala_comportamiento = 0;
			}

			$cadena .= "<td><input type=\"text\" id=\"puntaje_" . $contador . "\" style=\"text-transform: uppercase;\" value=\"" . $calificacion . "\"";
			$qry = $db->consulta("SELECT ac.ap_estado
									   FROM sw_aporte_evaluacion a,
											sw_aporte_paralelo_cierre ac,
											sw_curso c,
											sw_paralelo p
									  WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
										AND c.id_curso = p.id_curso
										AND ac.id_paralelo = p.id_paralelo
										AND p.id_paralelo = $id_paralelo 
									    AND a.id_aporte_evaluacion = $id_aporte_evaluacion");
			$aporte = $db->fetch_assoc($qry);
			$estado_aporte = $aporte["ap_estado"];
			if ($estado_aporte == 'A') {
				$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_aporte_evaluacion . ")\" /></td>\n";
			} else {
				$cadena .= " disabled /></td>\n";
			}

			$cadena = "SELECT ec_relacion FROM sw_escala_comportamiento WHERE id_escala_comportamiento = $id_escala_comportamiento";
			/*
			$query = $db->consulta($qry);

			if (!$query) {
				die("NO SE HAN DEFINIDO LAS ESCALAS DE COMPORTAMIENTO!");
			} else if ($db->num_rows($query) > 0) {
				$escala_comportamiento = $db->fetch_assoc($qry);
				$descripcion = $escala_comportamiento["ec_relacion"];
			} else {
				$descripcion = "";
			}
			$cadena .= "<td><input type=\"text\" id=\"equivalencia_" . $contador . "\" disabled value=\"" . $descripcion . "\" /></td>\n";
*/
			$cadena .= "</tr>\n";
		}
	} else {
		$cadena .= "<tr>\n";
		$cadena .= "<td colspan=\"4\" class=\"text-center\">No se han matriculado estudiantes en este paralelo...</td>\n";
		$cadena .= "</tr>\n";
	}
	return $cadena;
}

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$consulta = $db->consulta("SELECT nombre 
								 FROM sw_periodo_lectivo pl, 
								 	  sw_quien_inserta_comp comp
							    WHERE comp.id = pl.quien_inserta_comp_id 
								  AND pl.id_periodo_lectivo = $id_periodo_lectivo");
$resultado = $db->fetch_assoc($consulta);
if ($resultado["nombre"] == "Docente") // Insertan los Docentes
	// echo $paralelos->listarEstudiantesComportamientoParciales($id_paralelo, $id_aporte_evaluacion);
	echo "Docentes";
else
	echo listarComportamientosPorTutores();
