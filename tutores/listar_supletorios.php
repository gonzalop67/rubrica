<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Variables POST
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];

// Obtener el tipo de asignatura 1 cuantitativa 2 cualitativa
$qry = "SELECT id_tipo_asignatura FROM sw_asignatura WHERE id_asignatura = $id_asignatura";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$id_tipo_asignatura = $registro->id_tipo_asignatura;

// Obtener el rango de calificaciones para acceder al examen supletorio según el periodo lectivo
$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$rango_desde = $registro->rango_desde;
$rango_hasta = $registro->rango_hasta;

// Obtener el estado del periodo lectivo y la nota mínima para aprobar el periodo
$qry = "SELECT pe_estado, pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
$resultado = $db->consulta($qry);
$registro = $db->fetch_object($resultado);
$nota_aprobacion = $registro->pe_nota_aprobacion;
$pe_estado = $registro->pe_estado;

$cadena = "<table id=\"tbl_supletorios\" class=\"table table-striped table-hover fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$cadena .= "<thead class='thead-dark fuente9'>\n";
$cadena .= "<th width=\"5%\">Nro.</th>\n";
$cadena .= "<th width=\"5%\">Id.</th>\n";
$cadena .= "<th width=\"30%\" style=\"text-align: left;\">N&oacute;mina</th>";

// Obtener id_curso
$qry = "SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo";
$consulta = $db->consulta($qry);
$resultado = $db->fetch_object($consulta);
$id_curso = $resultado->id_curso;

$consulta = $db->consulta("SELECT pe.id_periodo_evaluacion, id_tipo_periodo, pe_abreviatura, pe_nombre, pc.pe_ponderacion FROM sw_periodo_evaluacion pe, sw_periodo_evaluacion_curso pc WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion AND pc.id_curso = $id_curso AND pc.id_periodo_lectivo = $id_periodo_lectivo ORDER BY pc_orden");
$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
	while ($titulo_aporte = $db->fetch_assoc($consulta)) {
		$id_tipo_periodo = $titulo_aporte["id_tipo_periodo"];
		$id = $titulo_aporte["id_periodo_evaluacion"];
		$desc = $titulo_aporte["pe_nombre"];
		$abrev = $titulo_aporte["pe_abreviatura"];
		if ($id_tipo_periodo == 1 || $id_tipo_periodo == 7 || $id_tipo_periodo == 8) {
			$cadena .= "<th width=\"60px\" align=\"center\">\n";
			$cadena .= "<a href=\"#\" style='color: #fff; cursor: auto;' id=\"$id\" title=\"$desc\">" . $abrev . "</a>\n";
			$cadena .= "</th>\n";
			$cadena .= "<th width=\"60px\" align=\"center\">" . number_format($titulo_aporte["pe_ponderacion"] * 100, 2) . "%</th>\n";
		}
	}

	$cadena .= "<th width=\"60px\" align=\"center\" title='NOTA FINAL'>NOTA F.</th>\n";
	$cadena .= "<th width=\"60px\" align=\"center\" title='EXAMEN SUPLETORIO'>SUP.</th>\n";
	$cadena .= "<th width=\"120px\" align=\"left\">OBSERVACION</th>\n";
}

$cadena .= "</thead>\n";
$cadena .= "<tbody>\n";

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
		   AND di.id_paralelo = $id_paralelo
		   AND di.id_asignatura = $id_asignatura
		   AND es_retirado <> 'S'
		   AND activo = 1 
	  ORDER BY es_apellidos, es_nombres ASC";

$consulta = $db->consulta($qry);
$num_total_registros = $db->num_rows($consulta);

// Aquí va el procedimiento para desplegar el listado de estudiantes...
if ($num_total_registros > 0) {
	$contador = 0;
	$qryString = "";
	while ($paralelos = $db->fetch_assoc($consulta)) {
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
		$id_curso = $paralelos["id_curso"];
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
				$tipo_periodo = $periodo["tp_descripcion"];

				if ($id_tipo_asignatura === 1) {
					// Asignatura cuantitativa
					$qryString = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion,$id_estudiante,$id_paralelo,$id_asignatura) AS promedio_sub_periodo";
					$query = $db->consulta($qryString);
					$record = $db->fetch_object($query);
					$promedio_sub_periodo = $record->promedio_sub_periodo;
					$promedio_ponderado = number_format($promedio_sub_periodo * $ponderacion_subperiodo, 2);

					$promedio_sub_periodo = $promedio_sub_periodo == 0 ? "" : substr($promedio_sub_periodo, 0, strpos($promedio_sub_periodo, '.') + 3);

					$cadena .= "<td width=\"60px\" align=\"center\" style=\"color:#666;\">" . $promedio_sub_periodo . "</td>\n";

					$suma_ponderados_subperiodos += $promedio_ponderado;

					$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio_ponderado, '.') + 4);

					$cadena .= "<td width=\"60px\" align=\"center\" style=\"color:#666;\">" . $promedio_ponderado . "</td>\n";
				} else {
					// Asignatura cualitativa
					
				}
			}

			// Promedio Final del Periodo Lectivo
			$puntaje_final = $suma_ponderados_subperiodos;

			$nota_final = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

			$cadena .= "<td width=\"60px\" align=\"center\" style=\"color:#666;\">" . $nota_final . "</td>\n";

			$query = $db->consulta("SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS examen_supletorio");
			$registro = $db->fetch_object($query);
			$examen_supletorio = $registro->examen_supletorio;

			$nota_supletorio = $examen_supletorio == 0 ? "" : substr($examen_supletorio, 0, strpos($examen_supletorio, '.') + 3);

			$cadena .= "<td width=\"60px\" align=\"left\" style=\"color:#666;\">" . $nota_supletorio . "</td>\n";

			// OBSERVACIÓN
			$observacion = "";
			$color = "#666";

			if ($puntaje_final >= $nota_aprobacion) {
				$observacion = "APRUEBA";
				$color = "#008000";
			} elseif ($puntaje_final >= $rango_desde) {
				if ($pe_estado === "T") {
					if ($examen_supletorio >= $nota_aprobacion) {
						$observacion = "APRUEBA";
						$color = "#008000";
					} else {
						$observacion = "NO APRUEBA";
						$color = "#ff0000";
					}
				} else {
					if ($examen_supletorio >= $nota_aprobacion) {
						$observacion = "APRUEBA";
						$color = "#008000";
					} else {
						$observacion = "SUPLETORIO";
						$color = "#ff8c00";
					}
				}
			} else {
				$observacion = "NO APRUEBA";
				$color = "#ff0000";
			}

			$cadena .= "<td width=\"60px\" align=\"left\" style=\"color:" . $color . ";\">" . $observacion . "</td>\n";
		}

		$cadena .= "</tr>\n";
	}
}

$cadena .= "</tbody>\n";
$cadena .= "</table>\n";

echo $cadena;
