<?php
include("../scripts/clases/class.mysql.php");
// include("../scripts/clases/class.paralelos.php");
// include("../scripts/clases/class.aportes_evaluacion.php");

// Instanciamos la clase MySQL
$db = new MySQL();

session_start();
$paralelos = new paralelos();
$paralelos->id_curso = $_POST["id_curso"];
$id_curso = $_POST["id_curso"];
$paralelos->id_paralelo = $_POST["id_paralelo"];
$id_paralelo = $_POST["id_paralelo"];
$paralelos->id_asignatura = $_POST["id_asignatura"];
$id_asignatura = $_POST["id_asignatura"];
$paralelos->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
// $paralelos->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
// $id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$paralelos->id_usuario = $_SESSION["id_usuario"];
$aportes_evaluacion = new aportes_evaluacion();
$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

function listarEstudiantesParalelo($num_estudiantes, $estudiantes)
{
    global $db, $id_aporte_evaluacion, $id_curso, $id_paralelo, $id_asignatura;

    $cadena = "<table class='table fuente8'>\n";
    $cadena .= "<thead class='thead-dark fuente9'>\n";
    $cadena .= "<th>Nro.</th>\n";
    $cadena .= "<th>Id.</th>\n";
    $cadena .= "<th>Nómina</th>\n";

    // Acá recupero las abreviaturas de los insumos de calificación
    $abrev = $db->consulta("SELECT ru_abreviatura,
								   ru_nombre, 
                                   ru_descripcion
							  FROM sw_rubrica_evaluacion
						     WHERE id_tipo_asignatura = 1
							   AND id_aporte_evaluacion = $id_aporte_evaluacion");

    $num_registros = $db->num_rows($abrev);

    while ($titulo_rubrica = $db->fetch_object($abrev)) {
        $cadena .= "<th><a href='#' style='color: #fff' title='$titulo_rubrica->ru_descripcion'>$titulo_rubrica->ru_abreviatura</a></th>\n";
    }

    $cadena .= "<th>PROM</th>\n";
    $num_columnas = 4 + $num_registros;

    $consulta = $db->consulta("SELECT quien_inserta_comp FROM sw_curso WHERE id_curso = $id_curso");
    $resultado = $db->fetch_object($consulta);

    if ($resultado->quien_inserta_comp == 0) {
        $cadena .= "<th>COMP</th>\n";
        $num_columnas++;
    }

    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";

    $contador = 0;
    if ($num_estudiantes > 0) {
        while ($registro = $db->fetch_object($estudiantes)) {
            $contador++;
            $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
            $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
            $cadena .= "<td width='40px' align='left'>$contador</td>\n";
            $cadena .= "<td width='40px' align='left'>$registro->id_estudiante</td>\n";
            $cadena .= "<td width='360px' align='left'>$registro->es_apellidos $registro->es_nombres</td>\n";
            //Aca vamos a obtener el estado del aporte de evaluacion
            $record = $db->consulta("SELECT ac.ap_estado, 
                                            quien_inserta_comp
                                       FROM sw_aporte_evaluacion a,
                                            sw_aporte_paralelo_cierre ac,
                                            sw_curso c,
                                            sw_paralelo p
                                      WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
                                        AND c.id_curso = p.id_curso
                                        AND ac.id_paralelo = p.id_paralelo
                                        AND p.id_paralelo = $id_paralelo 
                                        AND a.id_aporte_evaluacion = $id_aporte_evaluacion");
            $resultado = $db->fetch_object($record);
            $estado_aporte = $resultado->ap_estado;
            $quien_inserta_comportamiento = $resultado->quien_inserta_comp;
            //Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
            if ($registro->id_tipo_asignatura == 1) { //CUANTITATIVA
                // Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
                $rubricas = $db->consulta("SELECT id_rubrica_evaluacion, 
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
                                              AND ac.id_paralelo = $id_paralelo");

                if ($db->num_rows($rubricas) > 0) {
                    $suma_rubricas = 0;
                    $contador_rubricas = 0;
                    while ($rubrica = $db->fetch_object($rubricas)) {
                        $contador_rubricas++;
                        $id_rubrica_evaluacion = $rubrica->id_rubrica_evaluacion;
                        $tipo_aporte = $rubrica->id_tipo_aporte;
                        $estado_aporte = $rubrica->ap_estado;
                        // Obtener la calificación si existe
                        $consulta = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $registro->id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
                        $rubrica_estudiante = $db->fetch_object($consulta);
                        if ($db->num_rows($consulta) > 0) {
                            $calificacion = $rubrica_estudiante->re_calificacion;
                        } else {
                            $calificacion = 0;
                        }
                        $suma_rubricas += $calificacion;
                        $calificacion = $calificacion == 0 ? "" : $calificacion;
                        $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_" . $id_rubrica_evaluacion . "_" . $registro->id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $tipo_aporte . "_" . $contador . "\" class=\"inputPequenio nota" . $contador_rubricas . "\" value=\"" . $calificacion . "\"";
                        if ($estado_aporte == 'A' && $registro->es_retirado == 'N') {
                            $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\"  onblur=\"editarCalificacion(this," . $registro->id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $calificacion . ")\" /></td>\n";
                        } else {
                            $cadena .= " disabled /></td>\n";
                        }
                    }
                    $promedio = $suma_rubricas / $contador_rubricas;
                    $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio promedio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
                } else {
                    $cadena .= "<tr>\n";
                    $cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
                    $cadena .= "</tr>\n";
                }
            } else { //CUALITATIVA
                // Aqui va el codigo para obtener la calificacion cualitativa
                $consulta = $db->consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = $registro->id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
                $cualitativa = $db->fetch_object($consulta);
                if ($db->num_rows($consulta) > 0) {
                    $calificacion = $cualitativa->rc_calificacion;
                } else {
                    $calificacion = " ";
                }
                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"cualitativa_" . $registro->id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $id_aporte_evaluacion . "_" . $contador . "\" class=\"inputPequenio nota1\" value=\"" . $calificacion . "\"";
                if ($estado_aporte == 'A' && $registro->es_retirado == 'N') {
                    $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this," . $registro->id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ",'" . $calificacion . "')\" /></td>\n";
                } else {
                    $cadena .= " disabled /></td>\n";
                }
            }

            if ($quien_inserta_comportamiento == 0) { // Ingresan los docentes
                // Aqui va el codigo para obtener el comportamiento
                $consulta = $db->consulta("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $registro->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
                $comportamiento = $db->fetch_object($consulta);
                if ($db->num_rows($consulta) > 0) {
                    $calificacion = $comportamiento->co_cualitativa;
                } else {
                    $calificacion = '';
                }
                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"comportamiento_" . $contador . "\" class=\"inputPequenio\" value=\"" . $calificacion . "\"";
                if ($estado_aporte == 'A' && $registro->es_retirado == 'N') {
                    $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionComportamiento(this," . $registro->id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ")\" /></td>\n";
                } else {
                    $cadena .= " disabled /></td>\n";
                }
            }
            $cadena .= "</tr>\n";
        }
    } else {
        $cadena .= "<tr>\n";
        $cadena .= "<td colspan=\"$num_columnas\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td>\n";
        $cadena .= "</tr>\n";
    }

    $cadena .= "</tbody>\n";
    $cadena .= "</table>\n";

    return $cadena;
}

function listarCalificacionesParalelo($num_estudiantes, $estudiantes)
{
    global $db, $id_aporte_evaluacion, $id_paralelo, $id_asignatura;

    $cadena = "<table class='table fuente8'>\n";
    $cadena .= "<thead class='thead-dark fuente9'>\n";
    $cadena .= "<th>Nro.</th>\n";
    $cadena .= "<th>Id.</th>\n";
    $cadena .= "<th>Nómina</th>\n";
    // Obtener el id_periodo_evaluacion correspondiente al id_aporte_evaluacion
    $consulta = $db->consulta("SELECT id_periodo_evaluacion FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
    $registro = $db->fetch_object($consulta);
    $id_periodo_evaluacion = $registro->id_periodo_evaluacion;
    // Recuperar los titulares de los parciales
    $registros = $db->consulta("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $id_periodo_evaluacion);
    $num_total_registros = $db->num_rows($registros);
    $contador_aportes = 0;
    while ($titulo_aporte = $db->fetch_object($registros)) {
        $contador_aportes++;
        if ($contador_aportes < $num_total_registros)
            $cadena .= "<th>" . $titulo_aporte->ap_abreviatura . "</th>\n";
        else {
            $cadena .= "<th>PROM.</th>\n";
            $cadena .= "<th>80%</th>\n";
            $cadena .= "<th>" . $titulo_aporte->ap_abreviatura . "</th>\n";
            $cadena .= "<th>20%</th>\n";
            $cadena .= "<th>NOTA Q.</th>\n";
        }
    }
    $cadena .= "</thead>\n";
    // Ahora sí nos dedicamos al cuerpo de la tabla
    $cadena .= "<tbody>\n";
    // Cuerpo de la tabla
    if ($num_estudiantes > 0) {
        $contador = 0;
        while ($estudiante = $db->fetch_object($estudiantes)) {
            $contador++;
            $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
            $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
            $id_estudiante = $estudiante->id_estudiante;
            $apellidos = $estudiante->es_apellidos;
            $nombres = $estudiante->es_nombres;
            $cadena .= "<td width='40px' align='left'>$contador</td>\n";
            $cadena .= "<td width='40px' align='left'>$id_estudiante</td>\n";
            $cadena .= "<td width='360px' align='left'>" . $apellidos . " " . $nombres . "</td>\n";
            // Aqui se calculan los promedios de cada aporte de evaluacion
            $aportes_evaluacion = $db->consulta("SELECT a.id_aporte_evaluacion, 
                                                        id_tipo_aporte, 
                                                        ac.ap_estado 
                                                   FROM sw_periodo_evaluacion p, 
                                                        sw_aporte_evaluacion a, 
                                                        sw_aporte_paralelo_cierre ac 
                                                  WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
                                                    AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
                                                    AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
                                                    AND ac.id_paralelo = $id_paralelo");
            // Aqui calculo los promedios y desplegar en la tabla
            $contador_aportes = 0;
            $suma_promedios = 0;
            while ($aporte = $db->fetch_object($aportes_evaluacion)) {
                $contador_aportes++;
                $tipo_aporte = $aporte->id_tipo_aporte;
                $estado_aporte = $aporte->ap_estado;
                $rubricas_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion 
                                                        FROM sw_rubrica_evaluacion r,
                                                             sw_asignatura a
                                                       WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
                                                         AND a.id_asignatura = $id_asignatura
                                                         AND id_aporte_evaluacion = " . $aporte->id_aporte_evaluacion);
                $suma_rubricas = 0;
                $contador_rubricas = 0;
                while ($rubrica = $db->fetch_object($rubricas_evaluacion)) {
                    $contador_rubricas++;
                    $id_rubrica_evaluacion = $rubrica->id_rubrica_evaluacion;
                    $consulta = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
                    $registro = $db->fetch_object($consulta);
                    if ($db->num_rows($consulta) > 0) {
                        $calificacion = $registro->re_calificacion;
                    } else {
                        $calificacion = 0;
                    }
                    $suma_rubricas += $calificacion;
                }
                $promedio = $suma_rubricas / $contador_rubricas;

                if ($contador_aportes < $num_total_registros) {
                    $suma_promedios += $promedio;
                    $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
                } else {
                    $examen_quimestral = $promedio;
                }
            }
            // Aqui debo calcular el ponderado de los promedios parciales
            $promedio_aportes = $suma_promedios / ($contador_aportes - 1);
            $ponderado_aportes = 0.8 * $promedio_aportes;
            $ponderado_examen = 0.2 * $examen_quimestral;
            $calificacion_quimestral = $ponderado_aportes + $ponderado_examen;

            $promedio_aportes = $promedio_aportes == 0 ? "" : substr($promedio_aportes, 0, strpos($promedio_aportes, '.') + 3);

            $ponderado_aportes = $ponderado_aportes == 0 ? "" : substr($ponderado_aportes, 0, strpos($ponderado_aportes, '.') + 4);

            $examen_quimestral = ($examen_quimestral == 0) ? "" : $examen_quimestral;

            $ponderado_examen = $ponderado_examen == 0 ? "" : substr($ponderado_examen, 0, strpos($ponderado_examen, '.') + 4);

            $calificacion_quimestral = $calificacion_quimestral == 0 ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedioaportes_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $promedio_aportes . "\" style=\"color:#666;\" /></td>\n";
            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_aportes . "\" style=\"color:#666;\" /></td>\n";
            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1\" id=\"examenquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $examen_quimestral . "\"";
            if ($estado_aporte == 'A') {
                $cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $examen_quimestral . ")\" /></td>\n";
            } else {
                $cadena .= " disabled /></td>\n";
            }
            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_examen . "\" style=\"color:#666;\" /></td>\n";
            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $calificacion_quimestral . "\" style=\"color:#666;\" /></td>\n";

            $cadena .= "</tr>\n";
        }
    }

    $cadena .= "</tbody>\n";
    $cadena .= "</table>";

    return $cadena;
}

// Consultar los estudiantes del paralelo
$estudiantes = $db->consulta("SELECT e.id_estudiante, 
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
							     AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");

$num_estudiantes = $db->num_rows($estudiantes);
$cadena = "";

$consulta = $db->consulta("SELECT ta_descripcion FROM sw_aporte_evaluacion ap, sw_tipo_aporte ta WHERE ta.id_tipo_aporte = ap.id_tipo_aporte AND id_aporte_evaluacion = $id_aporte_evaluacion");
$aporte_evaluacion = $db->fetch_object($consulta);
$tipo_aporte = $aporte_evaluacion->ta_descripcion;

if ($tipo_aporte == 'PARCIAL') {  // Parciales
    // echo $paralelos->listarEstudiantesParalelo();
    $cadena = listarEstudiantesParalelo($num_estudiantes, $estudiantes);
} else if ($tipo_aporte == 'EXAMEN_SUB_PERIODO') {  // Examen Sub Periodo
    // echo $paralelos->listarCalificacionesParalelo($_POST["id_periodo_evaluacion"], 2);
    $cadena = listarCalificacionesParalelo($num_estudiantes, $estudiantes);
}

echo $cadena;

// echo $tipo_aporte;
