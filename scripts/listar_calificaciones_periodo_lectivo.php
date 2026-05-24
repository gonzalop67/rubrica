<?php
include("clases/class.mysql.php");
include("../funciones/funciones_sitio.php");

$db = new mysql();

function truncar($numero, $digitos)
{
	$truncar = pow(10, $digitos);
	return intval($numero * $truncar) / $truncar;
}

function existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
{
	global $db;
	$qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.pe_principal = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
	$registro = $db->fetch_assoc($qry);
	$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

	$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
	return ($db->num_rows($qry) > 0);
}

function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
{
	global $db;

	$query = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND id_tipo_periodo = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo";
	$qry = $db->consulta($query);
	$registro = $db->fetch_assoc($qry);
	$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

	$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

	$num_registros = $db->num_rows($qry);

	if ($num_registros > 0) {
		$rubrica_estudiante = $db->fetch_assoc($qry);
		$calificacion = $rubrica_estudiante["re_calificacion"];
	} else {
		$calificacion = 0;
	}

	return $calificacion;
}

function obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
{
	global $db;
	// Obtencion de la fecha de cierre del aporte indicado por el campo pe_principal
	$qry = $db->consulta("SELECT ac.ap_fecha_cierre 
        FROM sw_periodo_evaluacion p, 
             sw_aporte_evaluacion a, 
             sw_aporte_paralelo_cierre ac,  
             sw_paralelo pa 
       WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion 
         AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
         AND pa.id_paralelo = ac.id_paralelo 
         AND pa.id_paralelo = $id_paralelo 
         AND pe_principal = $pe_principal 
         AND p.id_periodo_lectivo = $id_periodo_lectivo");
	$registro = $db->fetch_assoc($qry);
	return $registro["ap_fecha_cierre"];
}

session_start();

$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Obtener el id_tipo_asignatura
$qryString = "SELECT * FROM sw_asignatura WHERE id_asignatura = $id_asignatura";
$query = $db->consulta($qryString);

if (empty($query)) {
	echo "Error en la consulta: " . $qryString;
	die();
}

$id_tipo_asignatura = $db->fetch_object($query)->id_tipo_asignatura;

//Aqui "construyo" la tabla con los datos de calificaciones de la asignatura seleccionada
$cadena = "";

//Cabecera de la tabla
$cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
$cadena .= "<thead>\n";
$cadena .= "<th>Nro.</th>\n";
$cadena .= "<th>Id</th>\n";
$cadena .= "<th>N&oacute;mina</th>\n";

if ($id_tipo_asignatura == 1) { // Asignatura cuantitativa
	$query = $db->consulta("SELECT pe_ponderacion, pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo = 1 AND id_periodo_lectivo = $id_periodo_lectivo");
	$num_total_registros = $db->num_rows($query);
	if ($num_total_registros > 0) {
		while ($titulo_periodo = $db->fetch_assoc($query)) {
			$cadena .= "<th>" . $titulo_periodo["pe_abreviatura"] . "</th>\n";
			$cadena .= "<th>" . ($titulo_periodo["pe_ponderacion"] * 100) . "%</th>\n";
		}
	}

	//Imprimir Proyecto Integrador si existe
	$query = $db->consulta("SELECT pe_abreviatura, pe_ponderacion FROM sw_periodo_evaluacion WHERE id_tipo_periodo = 7 AND id_periodo_lectivo = " . $id_periodo_lectivo);
	$num_total_registros = $db->num_rows($query);
	if ($num_total_registros > 0) {
		while ($titulo_periodo = $db->fetch_assoc($query)) {
			$cadena .= "<th>" . $titulo_periodo["pe_abreviatura"] . "</th>\n";
			$cadena .= "<th>" . ($titulo_periodo["pe_ponderacion"] * 100) . "%</th>\n";
		}
	}

	$cadena .= "<th>NOTA F.</th>\n";
	$cadena .= "<th>OBSER.</th>\n";

	//Imprimir las cabeceras de exámenes supletorios
	$query = $db->consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo IN (2, 3) AND id_periodo_lectivo = " . $id_periodo_lectivo);
	$num_total_registros = $db->num_rows($query);
	if ($num_total_registros > 0) {
		while ($titulo_periodo = $db->fetch_assoc($query)) {
			$cadena .= "<th>" . $titulo_periodo["pe_abreviatura"] . "</th>\n";
		}
	}

	$cadena .= "<th>OBS. FINAL</th>\n";
} else { // Asignatura cualitativa
	$query = $db->consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo = 1 AND id_periodo_lectivo = $id_periodo_lectivo");
	$num_total_registros = $db->num_rows($query);
	if ($num_total_registros > 0) {
		while ($titulo_periodo = $db->fetch_assoc($query)) {
			$cadena .= "<th>" . $titulo_periodo["pe_abreviatura"] . "</th>\n";
		}
	}
}

$cadena .= "</thead>\n";

//Cuerpo de la tabla
$cadena .= "<tbody>\n";

$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, dg_abreviatura, es_retirado FROM sw_estudiante e, sw_def_genero dg, sw_estudiante_periodo_lectivo p WHERE dg.id_def_genero = e.id_def_genero AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");

$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
	$contador = 0;
	while ($paralelo = $db->fetch_object($consulta)) {
		$id_estudiante = $paralelo->id_estudiante;
		$apellidos = $paralelo->es_apellidos;
		$nombres = $paralelo->es_nombres;
		$retirado = $paralelo->es_retirado;
		$terminacion = ($paralelo->dg_abreviatura == "M") ? "O" : "A";

		$contador++;
		$cadena .= "<tr>\n";
		$cadena .= "<td>$contador</td>\n";
		$cadena .= "<td>$id_estudiante</td>\n";
		$cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";

		// Aquí va el código para calcular el promedio de cada subperiodo así como también
		// el promedio ponderado de cada subperiodo

		if ($id_tipo_asignatura == 1) {
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion, pe_ponderacion FROM sw_periodo_evaluacion WHERE id_tipo_periodo IN (1, 7) AND id_periodo_lectivo = $id_periodo_lectivo ORDER BY pe_orden ASC");

			$num_total_registros = $db->num_rows($periodos_evaluacion);

			if ($num_total_registros > 0) {

				$suma_ponderados_subperiodos = 0;
				while ($periodo = $db->fetch_object($periodos_evaluacion)) {
					$id_periodo_evaluacion = $periodo->id_periodo_evaluacion;
					$pe_ponderacion = $periodo->pe_ponderacion;

					$qry = "SELECT id_aporte_evaluacion, ap_ponderacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
					$aporte_evaluacion = $db->consulta($qry);
					$num_total_registros = $db->num_rows($aporte_evaluacion);

					if ($num_total_registros > 0) {
						// Aqui calculo los promedios y desplegar en la tabla
						$suma_promedios = 0;
						$contador_aportes = 0;
						$suma_ponderados = 0;
						while ($aporte = $db->fetch_object($aporte_evaluacion)) {
							$contador_aportes++;
							$ponderacion_aporte = $aporte->ap_ponderacion;

							$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = " . $aporte->id_aporte_evaluacion);
							$total_rubricas = $db->num_rows($rubrica_evaluacion);

							if ($total_rubricas > 0) {
								$suma_rubricas = 0;
								$contador_rubricas = 0;
								while ($rubricas = $db->fetch_object($rubrica_evaluacion)) {
									$contador_rubricas++;
									$id_rubrica_evaluacion = $rubricas->id_rubrica_evaluacion;

									$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
									$total_registros = $db->num_rows($qry);

									if ($total_registros > 0) {
										$rubrica_estudiante = $db->fetch_object($qry);
										$calificacion = $rubrica_estudiante->re_calificacion;
									} else {
										$calificacion = 0;
									}
									$suma_rubricas += $calificacion;
								}
								// Aqui calculo el promedio del aporte de evaluacion
								$promedio = truncar($suma_rubricas / $contador_rubricas, 2);
								$ponderado = truncar($promedio * $ponderacion_aporte, 3);

								$suma_promedios += $promedio;
								$suma_ponderados += $ponderado;
							}
						}
					}

					// Aqui se calculan las calificaciones del periodo de evaluacion
					$calificacion_subperiodo = truncar($suma_ponderados, 2);
					$nota_subperiodo = ($calificacion_subperiodo == 0) ? "" : substr($calificacion_subperiodo, 0, strpos($calificacion_subperiodo, '.') + 3);
					$cadena .= "<td align='left'>" . $nota_subperiodo . "</td>";

					$calificacion_ponderada = $calificacion_subperiodo * $pe_ponderacion;
					$nota_ponderada = ($calificacion_ponderada == 0) ? "" : substr($calificacion_ponderada, 0, strpos($calificacion_ponderada, '.') + 4);
					$cadena .= "<td align='left'>" . $nota_ponderada . "</td>";

					$suma_ponderados_subperiodos += $calificacion_ponderada;
				}
			}

			$puntaje_final = truncar($suma_ponderados_subperiodos, 2);
			$nota_final = ($puntaje_final == 0) ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);
			$cadena .= "<td align='left'>" . $nota_final . "</td>\n";

			// Desplegar la equivalencia final (APRUEBA, NO APRUEBA, SUPLETORIO, REMEDIAL)

			$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
			$query = $db->consulta($qry);
			$registro = $db->fetch_object($query);
			$nota_minima = $registro->pe_nota_aprobacion;

			$observacion = "";
			$total_registros = 0;

			$examen_supletorio = 0;
			$examen_remedial = 0;

			if ($puntaje_final >= $nota_minima) {
				$observacion = "APRUEBA";
			} else {
				// Consultar si el estudiante tiene que dar exámenes supletorios o remediales
				$qry = "SELECT * 
					  FROM sw_equivalencia_supletorios 
					 WHERE id_periodo_lectivo = $id_periodo_lectivo 
					 ORDER BY rango_desde DESC";
				$query = $db->consulta($qry);
				$total_registros = $db->num_rows($query);

				while ($registro = $db->fetch_object($query)) {
					$rango_desde = $registro->rango_desde;
					$rango_hasta = $registro->rango_hasta;
					if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
						$observacion = $registro->nombre_examen;
					}
				}
			}

			$cadena .= "<td align='left'>" . $observacion . "</td>\n";

			// Observación Final (Luego del periodo de evaluación de exámenes supletorios)

			$equiv_final = "";

			if ($observacion != "APRUEBA") {
				if ($observacion == "SUPLETORIO") {
					// Determinar la nota del examen supletorio

					$fecha_actual = new DateTime("now");
					$fecha_cierre = new DateTime(obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));

					if ($fecha_actual >= $fecha_cierre) {
						$examen_supletorio = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

						if ($examen_supletorio >= 7) {
							// equivalencia final cualitativa
							$equiv_final = "APRUEBA";
						} else {
							if ($total_registros == 1) {
								$equiv_final = "NO APRUEBA";
							} else {
								// Antigua LOEI
								$examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

								if ($examen_remedial >= 7) {
									$equiv_final = "APRUEBA";
								} else {
									$equiv_final = "NO APRUEBA";
								}
							}
						}
					}
				} else if ($observacion == "REMEDIAL") {
					// Antigua LOEI
					$examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

					if ($examen_remedial >= 7) {
						$equiv_final = "APRUEBA";
					} else {
						$equiv_final = "NO APRUEBA";
					}
				} else {
					// No alcanza el puntaje mínimo
					$equiv_final = "NO APRUEBA";
				}
			}

			$nota_supletorio = $examen_supletorio == 0 ? "" : substr($examen_supletorio, 0, strpos($examen_supletorio, '.') + 3);
			$nota_remedial = $examen_remedial == 0 ? "" : substr($examen_remedial, 0, strpos($examen_remedial, '.') + 3);

			if ($total_registros == 1) {
				$cadena .= "<td align='left'>" . $nota_supletorio . "</td>\n";
			} else {
				// Antigua LOEI
				$cadena .= "<td align='left'>" . $nota_supletorio . "</td>\n";
				$cadena .= "<td align='left'>" . $nota_remedial . "</td>\n";
			}

			$cadena .= "<td align='left'>" . $equiv_final . "</td>\n";
		} else {
			//Aqui consulto las asignaturas cualitativas
			$periodos_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_tipo_periodo = 1 AND id_periodo_lectivo = $id_periodo_lectivo ORDER BY pe_orden ASC");

			$num_total_registros = $db->num_rows($periodos_evaluacion);

			if ($num_total_registros > 0) {
				while ($periodo = $db->fetch_object($periodos_evaluacion)) {
					$id_periodo_evaluacion = $periodo->id_periodo_evaluacion;

					$query = $db->consulta("SELECT calc_prom_subperiodo_cualitativa($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_sub_periodo");

					$registro = $db->fetch_object($query);
					$promedio_sub_periodo = $registro->promedio_sub_periodo;

					$query = $db->consulta("SELECT ref_cualitativa FROM sw_escala_referencial WHERE nota_cuantitativa = $promedio_sub_periodo");
					$registro = $db->fetch_object($query);
					if (empty($registro)) {
						$ref_cualitativa = "";
					} else {
						$ref_cualitativa = $registro->ref_cualitativa;
					}

					$cadena .= "<td align='left'>" . $ref_cualitativa . "</td>\n";
				}
			}
		}

		$cadena .= "</tr>\n";
	} // fin while ($paralelo = $db->fetch_object($consulta))
}

$cadena .= "</tbody>\n";
$cadena .= "</table>\n";

echo $cadena;
