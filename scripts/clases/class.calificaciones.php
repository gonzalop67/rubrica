<?php
class calificaciones extends MySQL
{
	public $id_paralelo = "";
	public $id_asignatura = "";
	public $id_aporte_evaluacion = "";

	public function obtenerQuienInsertaComportamiento($id_periodo_lectivo)
	{
		$sql = "SELECT qc.nombre FROM sw_periodo_lectivo pl, sw_quien_inserta_comp qc WHERE qc.id = pl.quien_inserta_comp_id AND pl.id_periodo_lectivo = $id_periodo_lectivo";
		$resultado = parent::consulta($sql);

		return $resultado->fetch_object()->nombre;
	}

	public function listarCalificacionesCualitativas($id_periodo_evaluacion)
	{
		$sql = "SELECT * FROM sw_escala_referencial ORDER BY ref_cualitativa ASC";
		$result = parent::consulta($sql);

		$referencia_cualitativa = [];
		while ($row = $result->fetch_assoc()) {
			array_push($referencia_cualitativa, $row["ref_cualitativa"]);
		}

		// print_r("<pre>\n");
		// print_r($array);
		// print_r("</pre>\n");

		// die();

		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 c.id_curso, 
											 di.id_paralelo, 
											 di.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
								        FROM sw_distributivo di, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
								       WHERE di.id_paralelo = ep.id_paralelo 
									     AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
									     AND ep.id_estudiante = e.id_estudiante 
									     AND di.id_asignatura = a.id_asignatura 
									     AND di.id_paralelo = p.id_paralelo 
									     AND p.id_curso = c.id_curso 
									     AND di.id_paralelo = $this->id_paralelo
			                             AND di.id_asignatura = $this->id_asignatura
			                             AND es_retirado <> 'S'
									     AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
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
				$aporte_evaluacion = parent::consulta("SELECT a.id_aporte_evaluacion, 
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
													      AND a.id_tipo_aporte IN (1, 2)
													      AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
													      AND ac.id_paralelo = $id_paralelo
														ORDER BY ap_orden");
				$num_total_registros = parent::num_rows($aporte_evaluacion);

				if ($num_total_registros > 0) {
					// Aqui calculo los promedios y desplegar en la tabla
					while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
						$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
						$tipo_aporte = $aporte["ta_descripcion"];
						$estado_aporte = $aporte["ap_estado"];

						$qry = "SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion AND id_asignatura = $id_asignatura";

						// Obtener la calificación registrada

						$resultado = parent::consulta($qry);
						$registro = parent::fetch_assoc($resultado);
						if (!empty($registro)) {
							$rc_calificacion = $registro["rc_calificacion"];
						} else {
							$rc_calificacion = "";
						}

						if ($tipo_aporte == 'PARCIAL') {
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" disabled value=\"" . $rc_calificacion . "\" style=\"color:#666;\" /></td>\n";
						} else {
							$examen_quimestral = $rc_calificacion;
						}
					}

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"cualitativa_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $id_aporte_evaluacion . "_" . $contador . "\" class=\"inputPequenio nota1\" value=\"" . $examen_quimestral . "\"";
					if ($estado_aporte == 'A') {
						$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ",'" . $examen_quimestral . "')\" /></td>\n";
					} else {
						// Verificar si tiene autorización para editar calificación
						$qry_autorizado = parent::consulta("SELECT * FROM sw_autorizacion WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
						$num_total_registros_autorizado = parent::num_rows($qry_autorizado);
						if ($num_total_registros_autorizado > 0) {
							$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ",'" . $examen_quimestral . "')\" /></td>\n";
						} else {
							$cadena .= " disabled /></td>\n";
						}
					}
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	public function listarCalificacionesSupletorio($id_periodo_lectivo, $id_curso)
	{
		// Obtener el listado de estudiantes
		$qry = "SELECT e.id_estudiante, 
					   c.id_curso, 
					   di.id_paralelo, 
					   di.id_asignatura, 
					   e.es_apellidos, 
					   e.es_nombres,
					   es_retirado,
					   dg_abreviatura,   
					   as_nombre, 
					   cu_nombre, 
					   pa_nombre,
					   id_tipo_asignatura 
				  FROM sw_distributivo di, 
					   sw_estudiante_periodo_lectivo ep, 
					   sw_estudiante e, 
					   sw_def_genero dg, 
					   sw_asignatura a, 
					   sw_curso c, 
					   sw_paralelo p 
				 WHERE di.id_paralelo = ep.id_paralelo 
				   AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
				   AND ep.id_estudiante = e.id_estudiante 
				   AND dg.id_def_genero = e.id_def_genero 
				   AND di.id_asignatura = a.id_asignatura 
				   AND di.id_paralelo = p.id_paralelo 
				   AND p.id_curso = c.id_curso 
				   AND di.id_paralelo = $this->id_paralelo
				   AND di.id_asignatura = $this->id_asignatura
				   AND activo = 1 
				 ORDER BY es_apellidos, es_nombres ASC";
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);

		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";

		if ($num_total_registros > 0) {
			$contador = 0;
			$suma_ponderados_subperiodos = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
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
				$periodos_evaluacion = parent::consulta($qry);
				$num_total_registros = parent::num_rows($periodos_evaluacion);

				if ($num_total_registros > 0) {
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_ponderados_subperiodos = 0;
					while ($periodo = parent::fetch_assoc($periodos_evaluacion)) {
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$ponderacion_subperiodo = $periodo["pe_ponderacion"];

						$qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo";

						$resultado = parent::consulta($qry);
						$registro = parent::fetch_object($resultado);

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

					$puntaje_final_string = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionperiodo_" .  $id_estudiante . "_" . $contador . "\" disabled value=\"" . $puntaje_final_string . "\" style=\"color:#666;\" /></td>\n";

					// Obtener la calificacion del examen supletorio
					$qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio";
					$resultado = parent::consulta($qry);
					$calificacion = parent::fetch_assoc($resultado);
					$supletorio = $calificacion["supletorio"];

					$supletorio = ($supletorio == 0) ? "" : $supletorio;

					// Obtener el estado del periodo de evaluación
					$qry = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $this->id_aporte_evaluacion AND id_paralelo = $this->id_paralelo";
					$resultado = parent::consulta($qry);
					$registro = parent::fetch_object($resultado);

					if (!empty($registro)) {
						$estado_aporte = $registro->ap_estado;
					} else {
						return "No se ha definido el aporte de evaluación para el examen supletorio.";
					}

					// Obtner el id_rubrica_evaluacion del examen supletorio
					$qry = "SELECT id_rubrica_evaluacion FROM sw_aporte_evaluacion a, sw_rubrica_evaluacion r WHERE a.id_aporte_evaluacion = r.id_aporte_evaluacion AND a.id_aporte_evaluacion = $this->id_aporte_evaluacion";
					$resultado = parent::consulta($qry);
					$registro = parent::fetch_object($resultado);
					$id_rubrica_evaluacion = $registro->id_rubrica_evaluacion;

					// Obtener el rango de calificaciones para acceder al examen supletorio según el periodo lectivo
					$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo";
					$resultado = parent::consulta($qry);
					$registro = parent::fetch_object($resultado);
					$rango_desde = $registro->rango_desde;
					$rango_hasta = $registro->rango_hasta;

					// Obtener la nota mínima para aprobar el periodo lectivo
					$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
					$resultado = parent::consulta($qry);
					$registro = parent::fetch_object($resultado);
					$nota_aprobacion = $registro->pe_nota_aprobacion;

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionsupletorio_" . $id_estudiante . "_" . $contador . "\"";

					if ($estado_aporte == 'A' && $puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
						$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacionSupletorio(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $nota_aprobacion . "," . $id_rubrica_evaluacion . ",'" . $supletorio . "')\" value=\"$supletorio\" /></td>\n";
					} else {
						// Verificar si tiene autorización para editar calificación
						$qry_autorizado = parent::consulta("SELECT * FROM sw_autorizacion WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
						$num_total_registros_autorizado = parent::num_rows($qry_autorizado);
						if ($num_total_registros_autorizado > 0) {
							$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacionSupletorio(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $nota_aprobacion . "," . $id_rubrica_evaluacion . ",'" . $supletorio . "')\" value=\"$supletorio\" /></td>\n";
						} else {
							$cadena .= " disabled /></td>\n";
						}
					}

					// OBSERVACION FINAL
					$observacion = "";
					$color = "#666";
					if ($retirado == "S")
						$observacion = "DESERTOR" . $terminacion;
					else if ($puntaje_final_string != "") {
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
}
