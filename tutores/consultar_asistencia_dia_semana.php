<?php
require_once("../scripts/clases/class.mysql.php");
$db = new mysql();

// Variables POST
$id_dia_semana = $_POST["id_dia_semana"];
$id_horario_def = $_POST["id_horario_def"];
$id_paralelo = $_POST["id_paralelo_tutor"];
$ae_fecha = $_POST["ae_fecha"];

$contador_asistentes = 0;
$contador_ausentes = 0;

$cadena = "<table id=\"t_asistencia\" class=\"table table-striped table-hover fuente9\">\n";
$cadena .= "<thead>\n";
$cadena .= "<th>Nro.</th>\n";
$cadena .= "<th>Id</th>\n";
$cadena .= "<th>Nómina</th>\n";
// Obtener las horas clase asociadas al día de la semana
$query = "SELECT hc.id_hora_clase, hc_nombre FROM `sw_hora_dia` hd, `sw_hora_clase` hc WHERE hc.id_hora_clase = hd.id_hora_clase AND id_dia_semana = $id_dia_semana ORDER BY hc_orden";
//echo $id_paralelo; die();
$consulta = $db->consulta($query);
$num_registros = $db->num_rows($consulta);
if ($num_registros > 0) {
    while ($row = $db->fetch_object($consulta)) {
        // Obtener el docente y la asignatura
        $query = "SELECT as_nombre, us_shortname FROM sw_horario ho, sw_asignatura a, sw_usuario u WHERE u.id_usuario = ho.id_usuario AND a.id_asignatura = ho.id_asignatura AND  id_paralelo = $id_paralelo AND id_dia_semana = $id_dia_semana AND id_hora_clase = $row->id_hora_clase";
        $result = $db->consulta($query)->fetch_object();
        // print_r("<pre>"); print_r($result); print_r("</pre>"); die();
        $cadena .= "<th><a href='#' title='$result->as_nombre - $result->us_shortname'>" . $row->hc_nombre . "</a></th>\n";
    }
    $cadena .= "<th>Asistencia</th>\n";
    $cadena .= "<th>Observación</th>\n";
    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";
    // Selección de las asistencias para desplegarlas
    $query = "SELECT e.id_estudiante, 
                     es_apellidos, 
                     es_nombres 
                FROM sw_estudiante e, 
                     sw_estudiante_periodo_lectivo ep 
               WHERE e.id_estudiante = ep.id_estudiante 
                 AND es_retirado = 'N'
                 AND activo = 1  
                 AND ep.id_paralelo = $id_paralelo
            ORDER BY es_apellidos, es_nombres";
    $consulta = $db->consulta($query);
    $num_registros = $db->num_rows($consulta);
    if ($num_registros > 0) {
        $contador = 0;
        while ($row = $db->fetch_object($consulta)) {
            $contador++;
            $cadena .= "<tr>\n";
            $id_estudiante = $row->id_estudiante;
            $cadena .= "<td>" . $contador . "</td>\n";
            $cadena .= "<td>" . $id_estudiante . "</td>\n";
            $cadena .= "<td>" . $row->es_apellidos . " " . $row->es_nombres . "</td>\n";
            // Obtener las horas clase asociadas al día de la semana
            $query = "SELECT hc.id_hora_clase FROM `sw_hora_dia` hd, `sw_hora_clase` hc WHERE hc.id_hora_clase = hd.id_hora_clase AND id_dia_semana = $id_dia_semana ORDER BY hc_orden";
            $consulta2 = $db->consulta($query);
            $contador_asiste = 0;
            $contador_no_asiste = 0;
            while ($row2 = $db->fetch_object($consulta2)) {
                $id_hora_clase = $row2->id_hora_clase;
                // Aqui va el nuevo codigo para recuperar las asistencias
                $query = "SELECT i.id_inasistencia, 
                                 in_abreviatura 
                            FROM sw_asistencia_estudiante a, 
                                 sw_inasistencia i 
                           WHERE i.id_inasistencia = a.id_inasistencia
                             AND id_estudiante = $id_estudiante 
                             AND id_paralelo = $id_paralelo
                             AND id_hora_clase = $id_hora_clase
                             AND ae_fecha = '" . $ae_fecha . "'";
                $consulta3 = $db->consulta($query);
                $num_registros = $db->num_rows($consulta3);
                if ($num_registros > 0) {
                    $resultado = $db->fetch_assoc($consulta3);
                    if ($resultado["in_abreviatura"] == 'P') {
                        $cadena .= "<td style='color: green;'><i class='fa fa-fw fa-check-circle'></i></td>\n";
                        $contador_asiste++;
                    } else {
                        $cadena .= "<td style='color: red;'><i class='fa fa-fw fa-remove'></i></td>\n";
                        $contador_no_asiste++;
                    }
                } else {
                    $cadena .= "<td style='color: red;'><i class='fa fa-fw fa-remove'></i></td>\n";
                    $contador_no_asiste++;
                }
            }
            // Determinar si asiste o no
            $query = "SELECT * 
                        FROM sw_asistencia_tutor
                       WHERE id_estudiante = $id_estudiante 
                         AND id_paralelo = $id_paralelo 
                         AND at_fecha = '$ae_fecha'";
            $asistencia_tutor = $db->consulta($query);
            $num_registros = $db->num_rows($asistencia_tutor);
            $disabled = "";
            if ($num_registros == 0) {
                if ($contador_asiste >= $contador_no_asiste) {
                    $observacion = "ASISTE";
                    $checked = "checked";
                    $id_inasistencia = 1; // Asiste
                    $contador_asistentes++;
                } else {
                    $observacion = "NO ASISTE";
                    $checked = "";
                    $id_inasistencia = 2; // No asiste (Falta injustificada)
                    $contador_ausentes++;
                }
                // Insertar el registro
                $query = "INSERT INTO sw_asistencia_tutor (id_estudiante, id_paralelo, at_fecha, id_inasistencia) VALUES ($id_estudiante, $id_paralelo, '$ae_fecha', $id_inasistencia)";
                $db->consulta($query);
            } else {
                // Existe el registro y se debe desplegar
                $registro = $db->fetch_assoc($asistencia_tutor);
                $id_inasistencia = $registro["id_inasistencia"];
                if ($id_inasistencia == 1) {
                    $observacion = "ASISTE";
                    $checked = "checked";
                    $contador_asistentes++;
                } else {
                    $checked = "";
                    if ($id_inasistencia == 2) {
                        $observacion = "FALTA INJUSTIFICADA";
                    } else if ($id_inasistencia == 3) {
                        $observacion = "FALTA JUSTIFICADA";
                        $disabled = "disabled";
                    }
                    $contador_ausentes++;
                }
            }
            // Desplegar el checkbox
            $cadena .= "<td><input type=\"checkbox\" name=\"chkasistencia_" . $contador . "\" $checked $disabled  onclick=\"actualizar_asistencia_tutor(this," . $id_estudiante . "," . $id_paralelo . ",'" . $ae_fecha . "')\"></td>\n";
            // Desplegar la observación
            $cadena .= "<td><div id='observacion_" . $id_estudiante . "'>$observacion</div></td>\n";
            $cadena .= "</tr>\n";
        }
    } else {
        $cadena .= "</thead>\n";
        $cadena .= "<tbody>\n";
        $cadena .= "<tr>\n";
        $cadena .= "<td colspan='100%' class='text-center'>No se han matriculado estudiantes...</td>\n";
        $cadena .= "</tr>\n";
    }
} else {
    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";
    $cadena .= "<tr>\n";
    $cadena .= "<td colspan='100%' class='text-center'>No se han definido horas clase...</td>\n";
    $cadena .= "</tr>\n";
}
//
$cadena .= "</tbody>\n";
$cadena .= "</table>\n";

$datos = [
    'asistentes' => $contador_asistentes,
    'ausentes' => $contador_ausentes,
    'cadena' => $cadena
];

echo json_encode($datos);
