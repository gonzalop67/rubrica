<?php
include("clases/class.mysql.php");

$db = new mysql();

$id_periodo_evaluacion = $_POST['id_periodo_evaluacion'];
$id_asignatura = $_POST['id_asignatura'];
$id_paralelo = $_POST['id_paralelo'];

$cadena = "";

$consulta = $db->consulta("SELECT ap_abreviatura,
								  id_tipo_aporte, 
								  ap_ponderacion 
							 FROM sw_aporte_evaluacion 
							WHERE id_periodo_evaluacion = $id_periodo_evaluacion
							ORDER BY ap_orden");

$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
	//Cabecera de la tabla
	$cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
	$cadena .= "<thead>\n";
	$cadena .= "<tr>\n";
	$cadena .= "<th>Nro.</th>\n";
	$cadena .= "<th>Id</th>\n";
	$cadena .= "<th>N&oacute;mina</th>\n";

	$contador_aportes = 0;
	$suma_ponderacion = 0;
	while ($titulo_aporte = $db->fetch_assoc($consulta)) {
		$contador_aportes++;
		$suma_ponderacion += $titulo_aporte["ap_ponderacion"];
		$cadena .= "<th>" . $titulo_aporte["ap_abreviatura"] . "</th>\n";
		$cadena .= "<th>" . ($titulo_aporte["ap_ponderacion"] * 100) . "%</th>";
	}

	$query = $db->consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
	$record = $db->fetch_assoc($query);
	$cadena .= "<th>" . $record["pe_abreviatura"] . "</th>\n";

	$cadena .= "</tr>\n";
	$cadena .= "</thead>\n";
	//Cuerpo de la tabla
	$cadena .= "<tbody>\n";
	$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
	$num_total_registros = $db->num_rows($consulta);
	if ($num_total_registros > 0) {
		$contador = 0;
		while ($estudiante = $db->fetch_assoc($consulta)) {
			$contador++;
			$id_estudiante = $estudiante["id_estudiante"];
			$apellidos = $estudiante["es_apellidos"];
			$nombres = $estudiante["es_nombres"];
			$cadena .= "<tr>\n";
			$cadena .= "<td>$contador</td>\n";
			$cadena .= "<td>$id_estudiante</td>\n";
			$cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";

			// Aqui se calculan los promedios de cada aporte de evaluacion
			$aportes = $db->consulta("SELECT a.id_aporte_evaluacion, 
										 id_tipo_aporte, 
										 ac.ap_estado, 
										 ap_ponderacion 
									FROM sw_periodo_evaluacion p, 
										 sw_aporte_evaluacion a, 
										 sw_aporte_paralelo_cierre ac 
								   WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
									 AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
									 AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
									 AND ac.id_paralelo = $id_paralelo
								   ORDER BY ap_orden");
			$num_total_registros = $db->num_rows($aportes);
			if ($num_total_registros > 0) {
				// Aqui calculo los promedios y desplegar en la tabla
				$suma_aportes = 0;
				$contador_aportes = 0;
				$suma_promedios = 0;
				$suma_ponderados = 0;
				while ($aporte = $db->fetch_assoc($aportes)) {
					$contador_aportes++;
					$tipo_aporte = $aporte["id_tipo_aporte"];
					$estado_aporte = $aporte["ap_estado"];
					$ponderacion = $aporte["ap_ponderacion"];
					$rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion 
													   FROM sw_rubrica_evaluacion r,
															sw_asignatura a
													  WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
														AND a.id_asignatura = $id_asignatura
														AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
					$total_rubricas = $db->num_rows($rubrica_evaluacion);
					if ($total_rubricas > 0) {
						$suma_rubricas = 0;
						$contador_rubricas = 0;
						while ($rubricas = $db->fetch_assoc($rubrica_evaluacion)) {
							$contador_rubricas++;
							$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
							$qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
							$total_registros = $db->num_rows($qry);
							if ($total_registros > 0) {
								$rubrica_estudiante = $db->fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}
							$suma_rubricas += $calificacion;
						}
					}
					$promedio = $suma_rubricas / $contador_rubricas;
					$promedio_ponderado = $promedio * $ponderacion;
					$nota_promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
					$cadena .= "<td>" . $nota_promedio . "</td>";
					$nota_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio_ponderado, '.') + 4);
					$cadena .= "<td>" . $nota_ponderado . "</td>";
					$suma_promedios += $promedio;
					$suma_ponderados += $promedio_ponderado;
				}

				// Desplegar la suma de los promedios ponderados

				$suma_ponderados = $suma_ponderados == 0 ? "" : substr($suma_ponderados, 0, strpos($suma_ponderados, '.') + 3);
				$cadena .= "<td>" . $suma_ponderados . "</td>";
			}
			$cadena .= "</tr>\n";
		}
	}
	$cadena .= "</tbody>\n";
	$cadena .= "</table>\n";
}

echo $cadena;
