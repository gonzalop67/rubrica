<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables
$id_asignatura = $_POST["id_asignatura"];
$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";

// Cabecera de la tabla
$cadena .= "<tr class='cabeceraTabla'>\n";
$cadena .= "<th width=\"5%\" class='text-center'>Nro.</th>\n";
$cadena .= "<th width=\"5%\" class='text-left'>Id.</th>\n";
$cadena .= "<th width=\"30%\" class='text-left'>Nómina</th>\n";
// Titulos de los periodos de evaluacion
$qry = "SELECT pe.id_periodo_evaluacion, 
               pe_abreviatura,  
               pc.pe_ponderacion 
          FROM sw_periodo_evaluacion pe,
               sw_periodo_evaluacion_curso pc, 
               sw_tipo_periodo tp 
         WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
           AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
           AND tp.id_tipo_periodo = pe.id_tipo_periodo 
           AND pe.id_tipo_periodo IN (1, 7, 8) 
           AND pc.id_periodo_lectivo = $id_periodo_lectivo
           AND pc.id_curso = (SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo) 
         ORDER BY pc_orden ASC";
$consulta = $db->consulta($qry);
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
    while ($periodo = $db->fetch_assoc($consulta)) {
        $tipo_periodo = $periodo["pe_abreviatura"];
        $ponderacion = $periodo["pe_ponderacion"];
        $cadena .= "<th width=\"60px\" class='text-center'>$tipo_periodo</th>\n";
        $cadena .= "<th width=\"60px\" class='text-center'>" . number_format($ponderacion * 100, 2) . "%</th>\n";
    }
}
$cadena .= "<th width=\"60px\" class='text-left'>Prom.F.</th>\n";
$cadena .= "<th width=\"60px\" class='text-left'>Sup.</th>\n";
$cadena .= "<th width=\"120px\" class='text-left'>Observación</th>\n";
$cadena .= "<th width=\"*\">&nbsp;</th>\n"; // Esto es para igualar el espaciado entre columnas
$cadena .= "</tr>\n";

$qry = "SELECT e.id_estudiante, 
				c.id_curso, 
				d.id_paralelo, 
				d.id_asignatura, 
				e.es_apellidos, 
				e.es_nombres, 
				es_retirado, 
				dg_abreviatura, 
				as_nombre, 
				cu_nombre, 
				pa_nombre,
				id_tipo_asignatura 
		FROM sw_distributivo d, 
				sw_estudiante_periodo_lectivo ep, 
				sw_estudiante e, 
				sw_def_genero dg, 
				sw_asignatura a, 
				sw_curso c, 
				sw_paralelo p 
		WHERE d.id_paralelo = ep.id_paralelo 
			AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
			AND ep.id_estudiante = e.id_estudiante 
			AND dg.id_def_genero = e.id_def_genero 
			AND d.id_asignatura = a.id_asignatura 
			AND d.id_paralelo = p.id_paralelo 
			AND p.id_curso = c.id_curso 
			AND d.id_paralelo = $id_paralelo
			AND d.id_asignatura = $id_asignatura
			AND es_retirado <> 'S'
			AND activo = 1 ORDER BY es_apellidos, es_nombres ASC";

$estudiantes = $db->consulta($qry);
$num_total_registros = $db->num_rows($estudiantes);

//
if ($num_total_registros > 0) {
    $contador = 0;
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
        $id_curso = $paralelos["id_curso"];
        $id_asignatura = $paralelos["id_asignatura"];

        $cadena .= "<td width=\"5%\">$contador</td>\n";
        $cadena .= "<td width=\"5%\" align=\"left\">$id_estudiante</td>\n";
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
                   AND pc.id_curso = (SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo) 
                 ORDER BY pc_orden ASC";

        $periodos_evaluacion = $db->consulta($qry);
        $num_total_registros = $db->num_rows($periodos_evaluacion);
        if ($num_total_registros > 0) {
            // Aqui calculo los promedios y desplegar en la tabla
            $suma_ponderados_subperiodos = 0;
            while ($periodo = $db->fetch_assoc($periodos_evaluacion)) {
                $id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
                $ponderacion_subperiodo = $periodo["pe_ponderacion"];
                // $tipo_periodo = $periodo["tp_descripcion"];

                $qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion";
                $consulta = $db->consulta($qry);

                $record = $db->fetch_object($consulta);
                $promedio_sub_periodo = $record->calificacion;

                $nota_sub_periodo = $promedio_sub_periodo == 0 ? "" : substr($promedio_sub_periodo, 0, strpos($promedio_sub_periodo, '.') + 3);

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" disabled value=\"" . $nota_sub_periodo . "\" style=\"color:#666;\" /></td>\n";

                $promedio_subperiodo_ponderado = $promedio_sub_periodo * $ponderacion_subperiodo;
                $suma_ponderados_subperiodos += $promedio_subperiodo_ponderado;

                $subperiodo_ponderado = $promedio_subperiodo_ponderado == 0 ? "" : substr($promedio_subperiodo_ponderado, 0, strpos($promedio_subperiodo_ponderado, '.') + 4);

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" disabled value=\"" . $subperiodo_ponderado . "\" style=\"color:#666;\" /></td>\n";
            }
        }

        // Promedio Final del Periodo Lectivo
        $puntaje_final = $suma_ponderados_subperiodos;

        $puntaje_final = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

        $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" disabled value=\"" . $puntaje_final . "\" style=\"color:#666;\" /></td>\n";

        // Obtener la calificacion del examen supletorio
        $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio";
        
        $resultado = $db->consulta($qry);
        $calificacion = $db->fetch_assoc($resultado);
        $supletorio = $calificacion["supletorio"];

        $supletorio = $supletorio == 0 ? "" : substr($supletorio, 0, strpos($supletorio, '.') + 3);

        $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"" . $supletorio . "\" style=\"color:#666;\" disabled /></td>\n";

        // Obtener el id_aporte_evaluacion del examen supletorio
        $qry = "SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND id_tipo_aporte = 3 AND p.id_periodo_lectivo = $id_periodo_lectivo";
        // print_r($qry); die();
        $resultado = $db->consulta($qry);
        $result = $db->fetch_object($resultado);
        $id_aporte_evaluacion = $result->id_aporte_evaluacion;

        // Obtener el estado de cierre del aporte de evaluación 
        $qry = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo";
        $resultado = $db->consulta($qry);
        $result = $db->fetch_object($resultado);
        $estado = $result->ap_estado;

        // OBSERVACION FINAL
        $observacion = "";
        $color = "#666";
        if ($retirado == "S")
            $observacion = "RETIRAD" . $terminacion;
        else if ($puntaje_final != "") {
            if ($puntaje_final >= 7) {
                $observacion = "APRUEBA";
                $color = "#008000";
            } else if ($puntaje_final > 4) {
                if ($estado == 'A') {
                    if ($supletorio == "") {
                        $observacion = "SUPLETORIO";
                        $color = "#ff8c00";
                    } else if ($supletorio >= 7) {
                        $observacion = "APRUEBA";
                        $color = "#008000";
                    } else {
                        $observacion = "NO APRUEBA";
                        $color = "#FF0000";
                    }
                } else {
                    if ($supletorio == "") {
                        $observacion = "NO APRUEBA";
                        $color = "#FF0000";
                    } else if ($supletorio >= 7) {
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

        $cadena .= "<td width=\"120px\" align=\"left\"><input type=\"text\" class=\"inputMedio\" disabled value=\"" . $observacion . "\" style=\"color:$color;\" /></td>\n";

        $cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
        $cadena .= "</tr>\n";
    }
}

$cadena .= "</table>";

echo $cadena;
