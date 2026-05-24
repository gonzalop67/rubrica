<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();

$id_curso = $_POST["id_curso"];
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$id_usuario = $_SESSION["id_usuario"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

function listarCalificacionesAsignatura($num_total_registros, $estudiantes)
{
	global $db, $id_aporte_evaluacion, $id_paralelo, $id_periodo_lectivo;

	$cadena = "<table id='tabla_calificaciones' class='fuente8' width='100%' cellspacing='0' cellpadding='0' border='0'>\n";

	if ($num_total_registros > 0) {
		$contador = 0;
		while ($paralelos = $db->fetch_assoc($estudiantes)) {
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class='$fondolinea' onmouseover='className='itemEncimaTabla'' onmouseout='className='$fondolinea''>\n";
			$id_estudiante = $paralelos["id_estudiante"];
			$apellidos = $paralelos["es_apellidos"];
			$nombres = $paralelos["es_nombres"];
			$id_asignatura = $paralelos["id_asignatura"];
			$id_tipo_asignatura = $paralelos["id_tipo_asignatura"];
			$cadena .= "<td width='5%'>$contador</td>\n";
			$cadena .= "<td width='5%'>$id_estudiante</td>\n";
			$cadena .= "<td width='30%' align='left'>" . $apellidos . " " . $nombres . "</td>\n";

			// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
			$qry = "SELECT id_rubrica_evaluacion, 
						   id_tipo_aporte, 
						   ac.ap_estado 
					  FROM sw_rubrica_evaluacion r, 
						   sw_aporte_evaluacion a, 
						   sw_aporte_paralelo_cierre ac,
						   sw_asignatura asignatura
					 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
					   AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
					   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
					   AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
					   AND asignatura.id_asignatura = $id_asignatura
					   AND r.id_aporte_evaluacion = $id_aporte_evaluacion
					   AND ac.id_paralelo = $id_paralelo";
			//return $qry;
			$rubrica_evaluacion = $db->consulta($qry);
			$num_total_registros = $db->num_rows($rubrica_evaluacion);
			//
			if ($num_total_registros > 0) {
				//Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
				if ($id_tipo_asignatura == 1) { //CUANTITATIVA
					$suma_rubricas = 0;
					$contador_rubricas = 0;
					while ($rubricas = $db->fetch_assoc($rubrica_evaluacion)) {
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];

						//
						$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
						$num_total_registros = $db->num_rows($qry);
						$rubrica_estudiante = $db->fetch_assoc($qry);
						if ($num_total_registros > 0) {
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						//

						$suma_rubricas += $calificacion;

						$calificacion = $calificacion == 0 ? "" : $calificacion;
						$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio text-center' value='" . $calificacion . "' disabled /></td>\n";
					}
					//
					$promedio = $suma_rubricas / $contador_rubricas;
					$promedio = $promedio == 0 ? "" : truncar($promedio, 2);
					$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
					$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio promedio text-center' id='promedio_" . $id_rubrica_evaluacion . "_" . $contador . "' disabled value='" . $promedio . "' style='color:#666;' /></td>\n";

					// Obtener comportamiento
					$sql = "SELECT qi.nombre FROM sw_periodo_lectivo pl, sw_quien_inserta_comp qi WHERE qi.id = quien_inserta_comp_id AND pl.id_periodo_lectivo = $id_periodo_lectivo";
    				$result = $db->consulta($sql);
    				$quien_inserta_comportamiento = $db->fetch_object($result)->nombre;

					if (strtolower($quien_inserta_comportamiento) == 'docente') {
                        // Aqui va el codigo para obtener el comportamiento
                        $sql = "SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion;

                        $consulta = $db->consulta($sql);
                        $num_total_registros = $db->num_rows($consulta);

                        $comportamiento = $db->fetch_assoc($consulta);
                        if ($num_total_registros > 0) {
                            $calificacion = $comportamiento["co_cualitativa"];
                        } else {
                            $calificacion = '';
                        }

                        $cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio text-center' style='color:#666;' value='$calificacion' disabled></td>\n";
                    }
				}
			} else {
				// Asignaturas cualitativas
			}

			$cadena .= "<td width='*'>&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
			$cadena .= "</tr>\n";
		}
	} else {
		$cadena .= "<tr>\n";
		$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
		$cadena .= "</tr>\n";
	}

	$cadena .= "</table>";
	return $cadena;
}

function listarCalificacionesParalelo($id_periodo_evaluacion, $num_total_registros, $estudiantes, $tipo_aporte)
{
	global $db;

	$cadena = "<table id='tabla_calificaciones' class='fuente8' width='100%' cellspacing='0' cellpadding='0' border='0'>\n";

	if ($num_total_registros > 0) {
		$contador = 0;
		while ($paralelos = $db->fetch_assoc($estudiantes)) {
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class='$fondolinea' onmouseover='className='itemEncimaTabla'' onmouseout='className='$fondolinea''>\n";
			$id_estudiante = $paralelos["id_estudiante"];
			$apellidos = $paralelos["es_apellidos"];
			$nombres = $paralelos["es_nombres"];
			$id_paralelo = $paralelos["id_paralelo"];
			$id_asignatura = $paralelos["id_asignatura"];
			$cadena .= "<td width='5%'>$contador</td>\n";
			$cadena .= "<td width='5%'>$id_estudiante</td>\n";
			$cadena .= "<td width='30%' align='left'>" . $apellidos . " " . $nombres . "</td>\n";

			// Aqui se calculan los promedios de cada aporte de evaluacion
			$aporte_evaluacion = $db->consulta("SELECT a.id_aporte_evaluacion, 
														  a.id_tipo_aporte, 
														  ta_descripcion, 
														  ac.ap_estado, 
														  ap_ponderacion 
													 FROM sw_periodo_evaluacion p, 
														  sw_aporte_evaluacion a,
														  sw_tipo_aporte ta, 
														  sw_aporte_paralelo_cierre ac 
													WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
													  AND ta.id_tipo_aporte = a.id_tipo_aporte 
													  AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
													  AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
													  AND ac.id_paralelo = $id_paralelo
													ORDER BY ap_orden");
			$num_total_registros = $db->num_rows($aporte_evaluacion);
			//
			if ($num_total_registros > 0) {
				// Aqui calculo los promedios y desplegar en la tabla
				$suma_ponderados = 0;
				while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
					$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
					$ta_descripcion = $aporte["ta_descripcion"];
					$ponderacion = $aporte["ap_ponderacion"];

					$qry = "SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
					$resultado = $db->consulta($qry);
					$registro = $db->fetch_assoc($resultado);
					$promedio = $registro["promedio"];

					$qry = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a WHERE a.id_aporte_evaluacion = r.id_aporte_evaluacion AND a.id_aporte_evaluacion = $id_aporte_evaluacion";
					$resultado = $db->consulta($qry);
					$registro = $db->fetch_assoc($resultado);
					$id_rubrica_evaluacion = $registro["id_rubrica_evaluacion"];

					$promedio_ponderado = $promedio * $ponderacion;

					if ($ta_descripcion !== $tipo_aporte) {
						$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
						$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio' id='promedio_" . $id_rubrica_evaluacion . "_" . $contador . "' disabled value='" . $promedio . "' style='color:#666;' /></td>\n";

						$suma_ponderados += $promedio_ponderado;

						$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio, '.') + 4);
						$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio' disabled value='" . $promedio_ponderado . "' style='color:#666;' /></td>\n";
					} else {
						$fase_proyecto = $promedio;

						$ponderado_fase = $ponderacion * $fase_proyecto;

						$nota_fase = ($fase_proyecto == 0) ? "" : substr($fase_proyecto, 0, strpos($fase_proyecto, '.') + 3);

						$ponderado_fase = ($ponderado_fase == 0) ? "" : substr($ponderado_fase, 0, strpos($ponderado_fase, '.') + 4);

						$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio nota1 is-active' value='" . $nota_fase . "' disabled /></td>\n";

						$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio' disabled value='" . $ponderado_fase . "' style='color:#666;' /></td>\n";
					}
				}

				$ponderado_aportes = $suma_ponderados;

				$calificacion_quimestral = $ponderado_aportes + $ponderado_fase;

				$calificacion_quimestral = ($calificacion_quimestral == 0) ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

				$cadena .= "<td width='60px' align='left'><input type='text' class='inputPequenio' disabled value='" . $calificacion_quimestral . "' style='color:#666;' /></td>\n";
			}

			$cadena .= "<td width='*'>&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
			$cadena .= "</tr>\n";
		}
	}

	$cadena .= "</table>";
	return $cadena;
}

$consulta = $db->consulta("SELECT ta_descripcion FROM sw_aporte_evaluacion ap, sw_tipo_aporte ta WHERE ta.id_tipo_aporte = ap.id_tipo_aporte AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
$aporte_evaluacion = $db->fetch_object($consulta);
$tipo_aporte = $aporte_evaluacion->ta_descripcion;

$qry = "SELECT e.id_estudiante, 
				c.id_curso, 
				d.id_paralelo, 
				d.id_asignatura, 
				e.es_apellidos, 
				e.es_nombres, 
				es_retirado, 
				as_nombre, 
				cu_nombre, 
				pa_nombre,
				id_tipo_asignatura 
		FROM sw_distributivo d, 
				sw_estudiante_periodo_lectivo ep, 
				sw_estudiante e, 
				sw_asignatura a, 
				sw_curso c, 
				sw_paralelo p 
		WHERE d.id_paralelo = ep.id_paralelo 
			AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
			AND ep.id_estudiante = e.id_estudiante 
			AND d.id_asignatura = a.id_asignatura 
			AND d.id_paralelo = p.id_paralelo 
			AND p.id_curso = c.id_curso 
			AND d.id_paralelo = $id_paralelo
			AND d.id_asignatura = $id_asignatura
			AND es_retirado <> 'S'
			AND activo = 1 ORDER BY es_apellidos, es_nombres ASC";

$estudiantes = $db->consulta($qry);
$num_total_registros = $db->num_rows($estudiantes);

switch ($tipo_aporte) {
	case "PARCIAL": // PARCIAL
		echo listarCalificacionesAsignatura($num_total_registros, $estudiantes);
		break;

	// case "EXAMEN_SUB_PERIODO":
	// 	// echo $listarCalificacionesParalelo($_POST["id_periodo_evaluacion"], 2);
	// 	echo $qry;
	// 	break;

	case "EXAMEN_SUB_PERIODO":
	case "FASE_PRI":
		echo listarCalificacionesParalelo($id_periodo_evaluacion, $num_total_registros, $estudiantes, $tipo_aporte);
		// echo $qry;
		break;

	case "PROYECTO_FINAL": // PROYECTO_FINAL
	case "EVAL_SUBNIVEL": // EVAL_SUBNIVEL
		// echo $listarCalificacionesProyecto($_SESSION["id_periodo_lectivo"], $tipo_aporte);
		echo $qry;
		break;

	default:
		echo "No existe el tipo de periodo seleccionado: $tipo_aporte";
		break;
}
