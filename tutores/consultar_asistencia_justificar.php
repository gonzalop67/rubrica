<?php
ini_set('display_errors', 0); 
error_reporting(E_ALL);

require_once("../scripts/clases/class.mysql.php");
$db = new mysql();

header('Content-Type: application/json; charset=utf-8');

// Parámetros POST limpios
$id_dia_semana  = isset($_POST["id_dia_semana"]) ? intval($_POST["id_dia_semana"]) : 0;
$id_horario_def = isset($_POST["id_horario_def"]) ? intval($_POST["id_horario_def"]) : 0;
$id_paralelo    = isset($_POST["id_paralelo_tutor"]) ? intval($_POST["id_paralelo_tutor"]) : 0;
$ae_fecha       = isset($_POST["ae_fecha"]) ? preg_replace('/[^0-9\-]/', '', $_POST["ae_fecha"]) : '';

$contador_faltas_injustificadas = 0;
$contador_faltas_justificadas = 0;

// Verificar si el tutor ya guardó asistencia ese día
$queryCheckTutor = "SELECT 1 FROM sw_asistencia_tutor WHERE id_paralelo = $id_paralelo AND at_fecha = '$ae_fecha' LIMIT 1";
// echo $queryCheckTutor; exit;
$resCheckTutor = $db->consulta($queryCheckTutor);

if ($db->num_rows($resCheckTutor) == 0) {
    echo json_encode([
        'ok' => false,
        'mensaje' => "Primero debe registrar las asistencias en el módulo de Tutoría para esta fecha..."
    ]);
    exit;
}

// Iniciar armado de la tabla
$cadena = "<table id=\"t_asistencia\" class=\"table table-striped table-hover fuente9\">\n";
$cadena .= "<thead>\n";
$cadena .= "<tr>\n";
$cadena .= "<th>Nro.</th>\n";
$cadena .= "<th>Id</th>\n";
$cadena .= "<th>Nómina</th>\n";

// CORRECCIÓN DE TABLAS: Usamos sw_horario_detalles tal como en el módulo anterior
$queryCabeceraHoras = "SELECT id_horario_detalle, nombre AS hc_nombre 
                       FROM sw_horario_detalles 
                       WHERE id_horario_def = $id_horario_def 
                       ORDER BY hora_inicio ASC";

$resCabeceraHoras = $db->consulta($queryCabeceraHoras);
$num_registros_horas = $db->num_rows($resCabeceraHoras);

$lista_horas_clase = array();

if ($num_registros_horas > 0) {
    
    // Almacenar las horas en un array de memoria
    while ($rowHora = $db->fetch_object($resCabeceraHoras)) {
        $lista_horas_clase[] = [
            'id_horario_detalle' => $rowHora->id_horario_detalle,
            'hc_nombre' => $rowHora->hc_nombre
        ];

        // Obtener la asignatura y docente asignados a esta hora
        $queryMateria = "SELECT a.as_nombre, u.us_shortname 
                         FROM sw_horario_clases ho
                         INNER JOIN sw_asignatura a ON ho.id_asignatura = a.id_asignatura 
                         LEFT JOIN sw_usuario u    ON ho.id_usuario = u.id_usuario
                         WHERE ho.id_paralelo = $id_paralelo 
                           AND ho.dia_semana = $id_dia_semana 
                           AND ho.id_horario_detalle = {$rowHora->id_horario_detalle}
                           AND ho.id_horario_def = $id_horario_def 
                         LIMIT 1";

        $resMateria = $db->consulta($queryMateria);
        $as_nombre = "Hora sin materia asignada";
        $us_shortname = "Docente N/A";

        if ($db->num_rows($resMateria) > 0) {
            $regMat = $db->fetch_object($resMateria);
            $as_nombre = $regMat->as_nombre;
            $us_shortname = $regMat->us_shortname;
        }

        $cadena .= "<th><a href='#' title='{$as_nombre} - {$us_shortname}' style='text-decoration:none; color:#333;'>{$rowHora->hc_nombre}</a></th>\n";
    }

    $cadena .= "<th>Acción</th>\n";
    $cadena .= "<th>Observación</th>\n";
    $cadena .= "<th>Justificación</th>\n";
    $cadena .= "</tr>\n";
    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";

    // Cargar la nómina de estudiantes
    $queryAlumnos = "SELECT e.id_estudiante, es_apellidos, es_nombres 
                     FROM sw_estudiante e
                     INNER JOIN sw_estudiante_periodo_lectivo ep ON e.id_estudiante = ep.id_estudiante 
                     WHERE es_retirado = 'N'
                       AND activo = 1  
                       AND ep.id_paralelo = $id_paralelo
                     ORDER BY es_apellidos ASC, es_nombres ASC";

    $resAlumnos = $db->consulta($queryAlumnos);
    
    if ($db->num_rows($resAlumnos) > 0) {
        $contador = 0;

        while ($rowEst = $db->fetch_object($resAlumnos)) {
            $contador++;
            $cadena .= "<tr>\n";
            $id_estudiante = $rowEst->id_estudiante;
            
            $cadena .= "<td>" . $contador . "</td>\n";
            $cadena .= "<td>" . $id_estudiante . "</td>\n";
            $cadena .= "<td>" . $rowEst->es_apellidos . " " . $rowEst->es_nombres . "</td>\n";

            // Recorrer las celdas de asistencia por hora clase
            foreach ($lista_horas_clase as $horaJornada) {
                $id_hora_clase = $horaJornada['id_horario_detalle'];

                $queryAsis = "SELECT id_tipo_inasistencia FROM sw_asistencia_estudiante
                              WHERE id_estudiante = $id_estudiante 
                                AND id_paralelo = $id_paralelo
                                AND id_horario_detalle = $id_hora_clase
                                AND ae_fecha = '$ae_fecha' 
                              LIMIT 1";

                $resAsis = $db->consulta($queryAsis);

                if ($db->num_rows($resAsis) > 0) {
                    $resultadoAsis = $db->fetch_assoc($resAsis);
                    // Suponiendo que 1 es 'Asiste' según el módulo anterior
                    if (intval($resultadoAsis["id_tipo_inasistencia"]) === 1) {
                        $cadena .= "<td style='color: green; vertical-align: middle;'><i class='fa fa-fw fa-check-circle'></i></td>\n";
                    } else {
                        $cadena .= "<td style='color: red; vertical-align: middle;'><i class='fa fa-fw fa-remove'></i></td>\n";
                    }
                } else {
                    $cadena .= "<td style='color: red; vertical-align: middle;'><i class='fa fa-fw fa-remove'></i></td>\n";
                }
            }

            // Consultar el estado del tutor consolidado
            $queryTutor = "SELECT id_asistencia_tutor, id_inasistencia, at_justificacion 
                           FROM sw_asistencia_tutor
                           WHERE id_estudiante = $id_estudiante 
                             AND id_paralelo = $id_paralelo 
                             AND at_fecha = '$ae_fecha'
                           LIMIT 1";

            // echo $queryTutor; exit;

            $resTutor = $db->consulta($queryTutor);
            $observacion = "SIN REGISTRO";
            $justificacionText = "";

            if ($db->num_rows($resTutor) > 0) {
                $registroTutor = $db->fetch_assoc($resTutor);
                $id_inasistencia = intval($registroTutor["id_inasistencia"]);
                $id_asistencia_tutor = $registroTutor["id_asistencia_tutor"];
                $justificacionText = $registroTutor["at_justificacion"];

                if ($id_inasistencia === 1) {
                    $observacion = "ASISTE";
                    $cadena .= "<td></td>\n";
                } else {
                    if ($id_inasistencia === 2) {
                        $observacion = "FALTA INJUSTIFICADA";
                        $contador_faltas_injustificadas++;
                        $cadena .= "<td><button type=\"button\" class=\"btn btn-success btn-sm\" onclick=\"justificar_asistencia_tutor(this, {$id_estudiante}, {$id_paralelo}, '{$ae_fecha}')\"><i class=\"fa fa-fw fa-check-square\"></i> Justificar</button></td>\n";
                    } else if ($id_inasistencia === 3) {
                        $observacion = "FALTA JUSTIFICADA";
                        $contador_faltas_justificadas++;
                        $cadena .= "<td><button type=\"button\" class=\"btn btn-primary btn-sm\" onclick=\"deshacer_asistencia_tutor({$id_asistencia_tutor})\"><i class=\"fa fa-fw fa-undo\"></i> Deshacer</button></td>\n";
                    }
                }
            } else {
                $cadena .= "<td></td>\n";
            }

            $cadena .= "<td style='vertical-align: middle;'><div id='observacion_{$id_estudiante}' style='font-weight:bold;'>$observacion</div></td>\n";
            $cadena .= "<td style='vertical-align: middle;'><div id='justificacion_{$id_estudiante}' class='text-muted'>$justificacionText</div></td>\n";
            $cadena .= "</tr>\n";
        }
    } else {
        $cadena .= "<tr><td colspan='100%' class='text-center text-muted' style='padding:20px;'>No se han matriculado estudiantes en este paralelo...</td></tr>\n";
    }
} else {
    $cadena .= "<tr><td colspan='100%' class='text-center text-warning' style='padding:20px;'>Este horario no tiene definidos bloques de horas clase...</td></tr>\n";
}

$cadena .= "</tbody>\n";
$cadena .= "</table>\n";

$datos = [
    'ok' => true,
    'cadena' => $cadena,
    'titulo' => "FALTAS INJUSTIFICADAS: " . $contador_faltas_injustificadas . " — FALTA JUSTIFICADA: " . $contador_faltas_justificadas
];

echo json_encode($datos);
exit;
?>
