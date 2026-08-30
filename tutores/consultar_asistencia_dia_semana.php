<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../scripts/clases/class.mysql.php");
$db = new MySQL(); // Con la 'M' y 'SQL' en mayúsculas para Linux

header('Content-Type: application/json; charset=utf-8');

// 1. Recibir y limpiar parámetros POST forzando números enteros limpios
$id_dia_semana  = isset($_POST["id_dia_semana"]) ? intval($_POST["id_dia_semana"]) : 0;
$id_horario_def = isset($_POST["id_horario_def"]) ? intval($_POST["id_horario_def"]) : 0;
$id_paralelo    = isset($_POST["id_paralelo_tutor"]) ? intval($_POST["id_paralelo_tutor"]) : 0;
$ae_fecha       = isset($_POST["ae_fecha"]) ? $db->filtrar($_POST["ae_fecha"]) : '';

$contador_asistentes = 0;
$contador_ausentes = 0;

$cadena = "<table id=\"t_asistencia\" class=\"table table-striped table-hover fuente9\">\n";
$cadena .= "<thead>\n";
$cadena .= "<tr>\n";
$cadena .= "<th>Nro.</th>\n";
$cadena .= "<th>Id</th>\n";
$cadena .= "<th>Nómina</th>\n";

// ====================================================================
// PASO A: Obtener y aislar las horas de la jornada
// ====================================================================
$queryHoras = "SELECT id_horario_detalle, nombre AS hc_nombre 
               FROM sw_horario_detalles 
               WHERE id_horario_def = $id_horario_def 
               ORDER BY hora_inicio ASC";

$consultaHoras = $db->consulta($queryHoras);
$num_registros_horas = $db->num_rows($consultaHoras);

$lista_horas_jornada = array();

if ($num_registros_horas > 0) {

    // Primero aislamos las horas
    while ($rowHora = $db->fetch_assoc($consultaHoras)) {
        $lista_horas_jornada[] = [
            'id_horario_detalle' => $rowHora['id_horario_detalle'],
            'hc_nombre' => $rowHora['hc_nombre']
        ];
    }

    // Consultamos las materias para las cabeceras de forma independiente
    foreach ($lista_horas_jornada as $hora) {
        $id_hora_det = $hora['id_horario_detalle'];

        $queryMateria = "SELECT a.as_nombre, u.us_shortname 
                         FROM sw_horario_clases ho
                         INNER JOIN sw_asignatura a ON ho.id_asignatura = a.id_asignatura 
                         LEFT JOIN sw_usuario u    ON ho.id_usuario = u.id_usuario
                         WHERE ho.id_paralelo = $id_paralelo 
                           AND ho.dia_semana = $id_dia_semana 
                           AND ho.id_horario_detalle = $id_hora_det
                           AND ho.id_horario_def = $id_horario_def 
                         LIMIT 1";

        $resMateria = $db->consulta($queryMateria);
        $as_nombre = "Hora sin materia asignada";
        $us_shortname = "Docente N/A";

        if ($db->num_rows($resMateria) > 0) {
            $regMat = $db->fetch_assoc($resMateria);
            $as_nombre = $regMat['as_nombre'];
            $us_shortname = $regMat['us_shortname'];
        }

        $cadena .= "<th><a href='#' title='" . $as_nombre . " - " . $us_shortname . "' style='text-decoration:none; color:#333;'>" . $hora['hc_nombre'] . "</a></th>\n";
    }

    // echo json_encode($cadena); exit;

    $cadena .= "<th>Asistencia</th>\n";
    $cadena .= "<th>Observación</th>\n";
    $cadena .= "</tr>\n";
    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";

    // ====================================================================
    // PASO B: Selección alfabética de los estudiantes matriculados activos
    // ====================================================================
    $queryAlumnos = "SELECT e.id_estudiante, es_apellidos, es_nombres 
                     FROM sw_estudiante e
                     INNER JOIN sw_estudiante_periodo_lectivo ep ON e.id_estudiante = ep.id_estudiante 
                     WHERE es_retirado = 'N'
                       AND activo = 1  
                       AND ep.id_paralelo = $id_paralelo
                     ORDER BY es_apellidos ASC, es_nombres ASC";

    $consultaEstudiantes = $db->consulta($queryAlumnos);
    $num_registros_estudiantes = $db->num_rows($consultaEstudiantes);

    if ($num_registros_estudiantes > 0) {
        $contador = 0;

        while ($rowEst = $db->fetch_assoc($consultaEstudiantes)) {
            $contador++;
            $cadena .= "<tr>\n";
            $id_estudiante = intval($rowEst['id_estudiante']);

            $cadena .= "<td>" . $contador . "</td>\n";
            $cadena .= "<td>" . $id_estudiante . "</td>\n";
            $cadena .= "<td>" . $rowEst['es_apellidos'] . " " . $rowEst['es_nombres'] . "</td>\n";

            $contador_asiste = 0;
            $contador_no_asiste = 0;

            // Recorremos las horas guardadas de forma segura
            foreach ($lista_horas_jornada as $horaJornada) {
                $id_hora_clase = intval($horaJornada['id_horario_detalle']);

                $queryAsis = "SELECT id_tipo_inasistencia FROM sw_asistencia_estudiante
                              WHERE id_estudiante = $id_estudiante 
                                AND id_paralelo = $id_paralelo
                                AND id_horario_detalle = $id_hora_clase
                                AND ae_fecha = '$ae_fecha' 
                              LIMIT 1";

                $consultaAsis = $db->consulta($queryAsis);

                if ($db->num_rows($consultaAsis) > 0) {
                    $resultadoAsis = $db->fetch_assoc($consultaAsis);

                    if (intval($resultadoAsis["id_tipo_inasistencia"]) === 1) {
                        $cadena .= "<td style='color: green; vertical-align: middle;'><i class='fa fa-fw fa-check-circle' title='Asiste'></i></td>\n";
                        $contador_asiste++;
                    } else {
                        $cadena .= "<td style='color: red; vertical-align: middle;'><i class='fa fa-fw fa-remove' title='Inasistencia / Novedad'></i></td>\n";
                        $contador_no_asiste++;
                    }
                } else {
                    $cadena .= "<td style='color: red; vertical-align: middle;'><i class='fa fa-fw fa-remove' title='Sin registro del docente'></i></td>\n";
                    $contador_no_asiste++;
                }
            }

            // ====================================================================
            // PASO C: Lógica consolidada del Tutor (AISLADA TOTALMENTE)
            // ====================================================================
            $queryTutor = "SELECT id_inasistencia FROM sw_asistencia_tutor
                           WHERE id_estudiante = $id_estudiante 
                             AND id_paralelo = $id_paralelo 
                             AND at_fecha = '$ae_fecha' 
                           LIMIT 1";

            // SOLUCIÓN: Usamos mysqli_query nativo del recurso de tu enlace para no pisar el puntero de tu clase $db
            $resTutor = mysqli_query($db->conexion, $queryTutor);
            $num_registros_tutor = mysqli_num_rows($resTutor);
            $disabled = "";

            if ($num_registros_tutor == 0) {
                if ($contador_asiste >= $contador_no_asiste) {
                    $observacion = "ASISTE";
                    $checked = "checked";
                    $id_inasistencia = 1;
                    $contador_asistentes++;
                } else {
                    $observacion = "NO ASISTE";
                    $checked = "";
                    $id_inasistencia = 2;
                    $contador_ausentes++;
                }

                $queryInsertTutor = "INSERT INTO sw_asistencia_tutor (id_estudiante, id_paralelo, at_fecha, id_inasistencia) 
                                     VALUES ($id_estudiante, $id_paralelo, '$ae_fecha', $id_inasistencia)";
                mysqli_query($db->conexion, $queryInsertTutor);
            } else {
                // SOLUCIÓN: Extraemos usando la función nativa del driver de conexión aislado
                $registroTutor = mysqli_fetch_assoc($resTutor);
                $id_inasistencia = intval($registroTutor["id_inasistencia"]);

                if ($id_inasistencia === 1) {
                    $observacion = "ASISTE";
                    $checked = "checked";
                    $contador_asistentes++;
                } else {
                    $checked = "";
                    $observacion = "NO ASISTE"; // <- Valor por defecto por si no es ni 2 ni 3

                    if ($id_inasistencia === 2) {
                        $observacion = "FALTA INJUSTIFICADA";
                    } else if ($id_inasistencia === 3) {
                        $observacion = "FALTA JUSTIFICADA";
                        $disabled = "disabled";
                    }
                    $contador_ausentes++;
                }
            }

            $cadena .= "<td style='vertical-align: middle;'><input type=\"checkbox\" name=\"chkasistencia_" . $contador . "\" $checked $disabled onclick=\"actualizar_asistencia_tutor(this," . $id_estudiante . "," . $id_paralelo . ",'" . $ae_fecha . "')\"></td>\n";
            $cadena .= "<td style='vertical-align: middle;'><div id='observacion_" . $id_estudiante . "' style='font-weight:bold; font-size:11px;'>$observacion</div></td>\n";
            $cadena .= "</tr>\n";
        } // Cierre del while de estudiantes
    } else {
        // Si no hay estudiantes, abrimos el tbody temporalmente para meter el mensaje
        $cadena .= "<tbody><tr><td colspan='100%' class='text-center text-muted' style='padding:20px;'>No se encontraron estudiantes matriculados activos en este paralelo...</td></tr>\n";
    }
} else {
    // Si no hay horas configuradas
    $cadena .= "<tbody><tr><td colspan='100%' class='text-center text-warning' style='padding:20px;'>Este horario no tiene definidos bloques de horas clase...</td></tr>\n";
}

$cadena .= "</tbody>\n";
$cadena .= "</table>\n";

$datos = [
    'asistentes' => $contador_asistentes,
    'ausentes' => $contador_ausentes,
    'cadena' => $cadena
];

echo json_encode($datos);
exit;
