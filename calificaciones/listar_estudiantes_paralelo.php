<?php
include_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_curso = $_POST["id_curso"];

$id_paralelo = $_POST["id_paralelo"];

$id_asignatura = $_POST["id_asignatura"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$id_usuario = $_SESSION["id_usuario"];

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

function listarEstudiantesParalelo($num_total_registros, $estudiantes)
{
	global $db, $id_paralelo, $id_asignatura, $id_aporte_evaluacion;

	$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	if ($num_total_registros > 0) {
		$contador = 0;
		while ($estudiante = $db->fetch_object($estudiantes)) {
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_estudiante = $estudiante->id_estudiante;
			$apellidos = $estudiante->es_apellidos;
			$nombres = $estudiante->es_nombres;
			$retirado = $estudiante->es_retirado;
			$id_paralelo = $estudiante->id_paralelo;
			$id_asignatura = $estudiante->id_asignatura;
			$id_tipo_asignatura = $estudiante->id_tipo_asignatura;
			$cadena .= "<td width=\"5%\">$contador</td>\n";
			$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
			$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";
			//Aca vamos a obtener el estado del aporte de evaluacion
			$query = $db->consulta("SELECT ac.ap_estado, 
											  quien_inserta_comp_id
										 FROM sw_aporte_evaluacion a,
											  sw_aporte_paralelo_cierre ac,
											  sw_periodo_lectivo pl,
											  sw_paralelo p
										WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
										  AND pl.id_periodo_lectivo = p.id_periodo_lectivo
										  AND ac.id_paralelo = p.id_paralelo
										  AND p.id_paralelo = " . $id_paralelo .
				" AND a.id_aporte_evaluacion = " . $id_aporte_evaluacion);
			$resultado = $db->fetch_object($query);
			$estado_aporte = $resultado->ap_estado;
			$quien_inserta_comportamiento = $resultado->quien_inserta_comp_id;
			//Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
			if ($id_tipo_asignatura == 1) { //CUANTITATIVA
				// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
				$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion, 
															id_tipo_aporte, 
															ac.ap_estado, 
															ap_ponderacion 
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
														AND ac.id_paralelo = $id_paralelo");
				$num_total_registros = $db->num_rows($rubrica_evaluacion);
				if ($num_total_registros > 0) {
					$suma_rubricas = 0;
					$contador_rubricas = 0;
					while ($rubricas = $db->fetch_object($rubrica_evaluacion)) {
						$contador_rubricas++;
						$id_rubrica_evaluacion = $rubricas->id_rubrica_evaluacion;
						$tipo_aporte = $rubricas->id_tipo_aporte;
						$estado_aporte = $rubricas->ap_estado;
						$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
						$num_total_registros = $db->num_rows($qry);
						$rubrica_estudiante = $db->fetch_assoc($qry);
						if ($num_total_registros > 0) {
							$calificacion = $rubrica_estudiante["re_calificacion"];
						} else {
							$calificacion = 0;
						}
						// $ponderado = $calificacion * $ponderacion;
						$suma_rubricas += $calificacion;
						// $suma_rubricas += $ponderado;
						$calificacion = $calificacion == 0 ? "" : $calificacion;
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $tipo_aporte . "_" . $contador . "\" class=\"inputPequenio nota" . $contador_rubricas . "\" value=\"" . $calificacion . "\"";
						if ($estado_aporte == 'A' && $retirado == 'N') {
							$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\"  onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $calificacion . ")\" /></td>\n";
						} else {
							$cadena .= " disabled /></td>\n";
						}
					}
					$promedio = $suma_rubricas / $contador_rubricas;
					$promedio = $promedio == 0 ? "" : truncar($promedio, 2);
					$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio promedio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
				} else {
					$cadena .= "<tr>\n";
					$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
					$cadena .= "</tr>\n";
				}
			} else { //CUALITATIVA
				// Aqui va el codigo para obtener la calificacion cualitativa
				$qry = $db->consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
				$num_total_registros = $db->num_rows($qry);
				$cualitativa = $db->fetch_object($qry);
				if ($num_total_registros > 0) {
					$calificacion = $cualitativa->rc_calificacion;
				} else {
					$calificacion = " ";
				}
				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"cualitativa_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $id_aporte_evaluacion . "_" . $contador . "\" class=\"inputPequenio nota1\" value=\"" . $calificacion . "\"";
				if ($estado_aporte == 'A' && $retirado == 'N') {
					$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ",'" . $calificacion . "')\" /></td>\n";
				} else {
					$cadena .= " disabled /></td>\n";
				}

				// if ($quien_inserta_comportamiento == 2) { // Ingresan los docentes
				// 	// Aqui va el codigo para obtener el comportamiento
				// 	$qry = $db->consulta("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
				// 	$num_total_registros = $db->num_rows($qry);
				// 	$comportamiento = $db->fetch_assoc($qry);
				// 	if ($num_total_registros > 0) {
				// 		$calificacion = $comportamiento["co_cualitativa"];
				// 	} else {
				// 		$calificacion = '';
				// 	}
				// 	$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"comportamiento_" . $contador . "\" class=\"inputPequenio\" value=\"" . $calificacion . "\"";
				// 	if ($estado_aporte == 'A' && $retirado == 'N') {
				// 		$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionComportamiento(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ")\" /></td>\n";
				// 	} else {
				// 		$cadena .= " disabled /></td>\n";
				// 	}
				// }
			}

			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
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

function listarCalificacionesParalelo($num_estudiantes, $estudiantes)
{
	global $db, $id_aporte_evaluacion, $id_paralelo, $id_asignatura, $id_periodo_evaluacion;

	$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	if ($num_estudiantes > 0) {
		$contador = 0;
		while ($paralelos = $db->fetch_assoc($estudiantes)) {
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_estudiante = $paralelos["id_estudiante"];
			$apellidos = $paralelos["es_apellidos"];
			$nombres = $paralelos["es_nombres"];
			$id_paralelo = $paralelos["id_paralelo"];
			$id_asignatura = $paralelos["id_asignatura"];
			$cadena .= "<td width=\"5%\">$contador</td>\n";
			$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
			$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";
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
			if ($num_total_registros > 0) {
				// Aqui calculo los promedios y desplegar en la tabla
				$suma_ponderados = 0;
				while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
					$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
					$id_tipo_aporte = $aporte["id_tipo_aporte"];
					$tipo_aporte = $aporte["ta_descripcion"];
					$estado_aporte = $aporte["ap_estado"];
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

					if ($tipo_aporte == 'PARCIAL' || $tipo_aporte == 'FASE_PRI') {
						$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";

						$suma_ponderados += $promedio_ponderado;

						$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio, '.') + 4);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio_ponderado . "\" style=\"color:#666;\" /></td>\n";
					} else {
						$examen_quimestral = $promedio;
					}
				}

				$ponderado_aportes = $suma_ponderados;

				$ponderado_examen = $ponderacion * $examen_quimestral;
				$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;

				$nota_quimestral = ($examen_quimestral == 0) ? "" : substr($examen_quimestral, 0, strpos($examen_quimestral, '.') + 3);

				$ponderado_examen = ($ponderado_examen == 0) ? "" : substr($ponderado_examen, 0, strpos($ponderado_examen, '.') + 4);

				$calificacion_quimestral = ($calificacion_quimestral == 0) ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1 is-active\" id=\"examenquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_quimestral . "\"";

				if ($estado_aporte == 'A') {
					$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $id_tipo_aporte . "," . $examen_quimestral . "," . $ponderacion . ")\" /></td>\n";
				} else {
					$cadena .= " disabled /></td>\n";
				}

				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_examen . "\" style=\"color:#666;\" /></td>\n";

				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $calificacion_quimestral . "\" style=\"color:#666;\" /></td>\n";
			}
			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
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

function listarCalificacionesFaseProyecto($num_total_registros, $estudiantes)
{
	global $db, $id_periodo_evaluacion;

	$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	if ($num_total_registros > 0) {
		$contador = 0;
		while ($paralelos = $db->fetch_assoc($estudiantes)) {
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_estudiante = $paralelos["id_estudiante"];
			$apellidos = $paralelos["es_apellidos"];
			$nombres = $paralelos["es_nombres"];
			$id_paralelo = $paralelos["id_paralelo"];
			$id_asignatura = $paralelos["id_asignatura"];
			$cadena .= "<td width=\"5%\">$contador</td>\n";
			$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
			$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

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
					$id_tipo_aporte = $aporte["id_tipo_aporte"];
					$tipo_aporte = $aporte["ta_descripcion"];
					$estado_aporte = $aporte["ap_estado"];
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

					if ($tipo_aporte !== 'FASE_PRI') {
						$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";

						$suma_ponderados += $promedio_ponderado;

						$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio, '.') + 4);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio_ponderado . "\" style=\"color:#666;\" /></td>\n";
					} else {
						$fase_proyecto = $promedio;

						$ponderado_fase = $ponderacion * $fase_proyecto;

						$nota_fase = ($fase_proyecto == 0) ? "" : substr($fase_proyecto, 0, strpos($fase_proyecto, '.') + 3);

						$ponderado_fase_input = ($ponderado_fase == 0) ? "" : substr($ponderado_fase, 0, strpos($ponderado_fase, '.') + 3);

						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1 is-active\" id=\"faseproyecto_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_fase . "\"";

						if ($estado_aporte == 'A') {
							$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $id_tipo_aporte . "," . $fase_proyecto . "," . $ponderacion . ")\" /></td>\n";
						} else {
							$cadena .= " disabled /></td>\n";
						}

						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_fase_input . "\" style=\"color:#666;\" /></td>\n";
					}
				}

				$ponderado_aportes = $suma_ponderados;

				$calificacion_quimestral = $ponderado_aportes + $ponderado_fase;

				$calificacion_quimestral = ($calificacion_quimestral == 0) ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $calificacion_quimestral . "\" style=\"color:#666;\" /></td>\n";
			}

			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
			$cadena .= "</tr>\n";
		}
	}
	$cadena .= "</table>";
	return $cadena;
}

function listarCalificacionesSupletorio($num_total_registros, $estudiantes)
{
	global $db, $id_periodo_lectivo, $id_curso;

	$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";

	if ($num_total_registros > 0) {
		$contador = 0;
		$suma_ponderados_subperiodos = 0;
		while ($paralelos = $db->fetch_assoc($estudiantes)) {
			$contador++;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$id_estudiante = $paralelos["id_estudiante"];
			$apellidos = $paralelos["es_apellidos"];
			$nombres = $paralelos["es_nombres"];

			$retirado = $paralelos["es_retirado"];
			$es_genero = $paralelos["dg_abreviatura"];
			$terminacion = ($es_genero == "M") ? "O" : "A";

			$id_paralelo = $paralelos["id_paralelo"];
			$id_asignatura = $paralelos["id_asignatura"];

			$cadena .= "<td width=\"5%\">$contador</td>\n";
			$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
			$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

			// Calcular las notas de bimestres, trimestres o quimestres
			$qry = "SELECT pe.id_periodo_evaluacion, 
						   tp_descripcion,  
						   pc.pe_ponderacion 
					  FROM sw_periodo_evaluacion pe,
						   sw_periodo_evaluacion_curso pc, 
						   sw_tipo_periodo tp 
					 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
					   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
					   AND tp.id_tipo_periodo = pe.id_tipo_periodo 
					   AND pe.id_tipo_periodo IN (1, 7, 8) 
					   AND pc.id_periodo_lectivo = $id_periodo_lectivo
					   AND pc.id_curso = $id_curso 
				  ORDER BY pc_orden ASC";

			$periodos_evaluacion = $db->consulta($qry);
			$num_total_registros = $db->num_rows($periodos_evaluacion);

			if ($num_total_registros > 0) {
				// Aqui calculo los promedios y desplegar en la tabla
				$suma_ponderados_subperiodos = 0;
				while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
					$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
					$ponderacion_subperiodo = $periodo["pe_ponderacion"];

					$qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo";

					$consulta = $db->consulta($qry);
					$registro = $db->fetch_object($consulta);

					$promedio_subperiodo = $registro->promedio_sub_periodo;

					$nota_subperiodo = $promedio_subperiodo == 0 ? "" : substr($promedio_subperiodo, 0, strpos($promedio_subperiodo, '.') + 3);

					// Desplegar la calificación del subperiodo (bimestre, trimestre, ...)
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promediosubperiodo_" . $id_estudiante . "_" . $id_periodo_evaluacion . "_" . $contador . "\" disabled value=\"" . $nota_subperiodo . "\" style=\"color:#666;\" /></td>\n";

					// Calcular y desplegar el ponderado del subperiodo
					$promedio_subperiodo_ponderado = $promedio_subperiodo * $ponderacion_subperiodo;
					$suma_ponderados_subperiodos += $promedio_subperiodo_ponderado;

					$subperiodo_ponderado = $promedio_subperiodo_ponderado == 0 ? "" : substr($promedio_subperiodo_ponderado, 0, strpos($promedio_subperiodo_ponderado, '.') + 4);

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadosubperiodo_" . $id_estudiante . "_" . $id_periodo_evaluacion . "_" . $contador . "\" disabled value=\"" . $subperiodo_ponderado . "\" style=\"color:#666;\" /></td>\n";
				}

				// Promedio Final del Periodo Lectivo
				$puntaje_final = $suma_ponderados_subperiodos;

				$puntaje_final = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionperiodo_" .  $id_estudiante . "_" . $contador . "\" disabled value=\"" . $puntaje_final . "\" style=\"color:#666;\" /></td>\n";

				// Obtener la calificacion del examen supletorio
				$qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio";
				$resultado = $db->consulta($qry);
				$calificacion = $db->fetch_assoc($resultado);
				$supletorio = $calificacion["supletorio"];

				$supletorio = $supletorio == 0 ? "" : substr($supletorio, 0, strpos($supletorio, '.') + 3);

				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionsupletorio_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $supletorio . "\" style=\"color:#666;\" /></td>\n";

				// Obtener el rango de calificaciones para acceder al examen supletorio según el periodo lectivo
				$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo";
				$resultado = $db->consulta($qry);
				$registro = $db->fetch_object($resultado);
				$rango_desde = $registro->rango_desde;
				$rango_hasta = $registro->rango_hasta;

				// Obtener la nota mínima para aprobar el periodo lectivo
				$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
				$resultado = $db->consulta($qry);
				$registro = $db->fetch_object($resultado);
				$nota_aprobacion = $registro->pe_nota_aprobacion;

				// OBSERVACION FINAL
				$observacion = "";
				$color = "#666";
				if ($retirado == "S")
					$observacion = "RETIRAD" . $terminacion;
				else if ($puntaje_final != "") {
					if ($puntaje_final > $rango_hasta) {
						$observacion = "APRUEBA";
						$color = "#008000";
					} else if ($puntaje_final >= $rango_desde) {
						if ($supletorio == "") {
							$observacion = "SUPLETORIO";
							$color = "#ff8c00";
						} else {
							if ($supletorio >= $nota_aprobacion) {
								$observacion = "APRUEBA";
								$color = "#008000";
							} else {
								$observacion = "NO APRUEBA";
								$color = "#FF0000";
							}
						}
					} else {
						$observacion = "NO APRUEBA";
						$color = "#FF0000";
					}
				}

				$cadena .= "<td width=\"120px\" align=\"left\"><input type=\"text\" class=\"inputMedio\" id=\"observacion_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $observacion . "\" style=\"color:$color;\" /></td>\n";
			}

			$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
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
			   id_tipo_asignatura, 
			   dg_abreviatura 
		  FROM sw_distributivo d, 
			   sw_estudiante_periodo_lectivo ep, 
			   sw_estudiante e,
			   sw_def_genero dg,  
			   sw_asignatura a, 
			   sw_curso c, 
			   sw_paralelo p 
		 WHERE d.id_paralelo = ep.id_paralelo 
		   AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
		   AND e.id_estudiante = ep.id_estudiante 
		   AND dg.id_def_genero = e.id_def_genero 
		   AND d.id_asignatura = a.id_asignatura 
		   AND d.id_paralelo = p.id_paralelo 
		   AND p.id_curso = c.id_curso 
		   AND d.id_paralelo = $id_paralelo
		   AND d.id_asignatura = $id_asignatura
		   AND es_retirado <> 'S' 
		   AND activo = 1 
	  ORDER BY es_apellidos, es_nombres ASC";

try {
	$estudiantes = $db->consulta($qry);
	$num_total_registros = $db->num_rows($estudiantes);

	if ($tipo_aporte == 'PARCIAL')	// Parciales
		echo listarEstudiantesParalelo($num_total_registros, $estudiantes);
	else if ($tipo_aporte == 'EXAMEN_SUB_PERIODO')   // Examen Sub Periodo
		echo listarCalificacionesParalelo($num_total_registros, $estudiantes);
	else if ($tipo_aporte == 'FASE_PRI') // Proyecto Integrador o Interdisciplinario
		echo listarCalificacionesFaseProyecto($num_total_registros, $estudiantes);
	else if ($tipo_aporte == 'SUPLETORIO') // Examen Supletorio
		echo listarCalificacionesSupletorio($num_total_registros, $estudiantes);
} catch (Exception $e) {
	echo $e->getMessage();
}



/* include_once("../scripts/clases/class.paralelos.php");
include_once("../scripts/clases/class.aportes_evaluacion.php");
session_start();
$paralelos = new paralelos();
$paralelos->id_curso = $_POST["id_curso"];
$paralelos->id_paralelo = $_POST["id_paralelo"];
$paralelos->id_asignatura = $_POST["id_asignatura"];
$paralelos->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
$paralelos->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$paralelos->id_usuario = $_SESSION["id_usuario"];
$aportes_evaluacion = new aportes_evaluacion();
$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
$tipo_aporte = $aportes_evaluacion->obtenerTipoAporte();
if ($tipo_aporte == 'PARCIAL')	// Parciales
	echo $paralelos->listarEstudiantesParalelo();
else if ($tipo_aporte == 'EXAMEN_SUB_PERIODO') // Examen Sub Periodo
	echo $paralelos->listarCalificacionesParalelo($_POST["id_periodo_evaluacion"], 2);
else if ($tipo_aporte == 'PROYECTO_FINAL' || $tipo_aporte == 'EVAL_SUBNIVEL') // Proyecto Integrador o Interdisciplinario / Evaluación de Subnivel (10mo. grado y 3ro. bachillerato)
	echo $paralelos->listarCalificacionesProyecto($_SESSION["id_periodo_lectivo"], $tipo_aporte);
else if ($tipo_aporte == 'FASE_PRI') // Proyecto Integrador o Interdisciplinario
	echo $paralelos->listarCalificacionesFaseProyecto($_POST["id_periodo_evaluacion"]); */
