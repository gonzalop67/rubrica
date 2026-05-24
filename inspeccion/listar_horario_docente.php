<?php
    session_start();
    require_once '../scripts/clases/class.mysql.php';
    $id_usuario = $_POST["id_usuario"];
    $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    $db = new MySQL();
    $horas = $db->consulta("SELECT DISTINCT(hc.id_hora_clase)
                              FROM sw_horario ho, 
                                   sw_hora_clase hc, 
                                   sw_paralelo pa, 
                                   sw_periodo_lectivo pe
                             WHERE ho.id_hora_clase = hc.id_hora_clase
                               AND pa.id_paralelo = ho.id_paralelo
                               AND pe.id_periodo_lectivo = pa.id_periodo_lectivo 
                               AND pe.id_periodo_lectivo = $id_periodo_lectivo 
                             ORDER BY hc_ordinal");
    $cadena = "";
    while ($hora = $db->fetch_object($horas)) {
        $cadena .= "<tr>\n";
        $id_hora_clase = $hora->id_hora_clase;
        // Consulto el nombre de la hora clase
        $consulta = $db->consulta("SELECT hc_nombre FROM sw_hora_clase WHERE id_hora_clase = $id_hora_clase");
        $hora_clase = $db->fetch_object($consulta);
        $cadena .= "<td class='text-center'><span style='font-size: 12pt'><strong>$hora_clase->hc_nombre</strong></span></td>\n";
        // Acá obtengo los dias de la semana asociados al docente
        $dias = $db->consulta("SELECT DISTINCT(d.id_dia_semana) 
                                 FROM sw_distributivo di,
                                      sw_horario h,
                                      sw_dia_semana d
                                WHERE di.id_asignatura = h.id_asignatura
                                  AND di.id_paralelo = h.id_paralelo
                                  AND d.id_dia_semana = h.id_dia_semana
                                  AND di.id_usuario = $id_usuario
                                  AND di.id_periodo_lectivo = $id_periodo_lectivo
                                ORDER BY ds_ordinal");
        while ($dia = $db->fetch_object($dias)) {
            $id_dia_semana = $dia->id_dia_semana;
            // Consulto la asignatura del dia y hora correspondientes
            $consulta = $db->consulta("SELECT as_nombre,
                                              cu_shortname,
                                              pa_nombre
                                         FROM sw_distributivo di,
                                              sw_curso c,
                                              sw_paralelo p,
                                              sw_horario ho, 
                                              sw_hora_clase hc, 
                                              sw_asignatura a
                                        WHERE di.id_asignatura = ho.id_asignatura
                                          AND di.id_paralelo = ho.id_paralelo
                                          AND c.id_curso = p.id_curso
                                          AND p.id_paralelo = ho.id_paralelo
                                          AND ho.id_hora_clase = hc.id_hora_clase 
                                          AND ho.id_asignatura = a.id_asignatura 
                                          AND di.id_usuario = $id_usuario
                                          AND di.id_periodo_lectivo = $id_periodo_lectivo
                                          AND ho.id_dia_semana = $id_dia_semana 
                                          AND ho.id_hora_clase = $id_hora_clase");
            $num_reg = $db->num_rows($consulta);
            $asignatura = $db->fetch_object($consulta);
            $cadena .= "<td>\n";
            if ($num_reg > 0) {
                // Despliego el nombre de la asignatura
                $cadena .= "<p>$asignatura->as_nombre</p>\n";
                // Despliego el nombre del curso y paralelo
                $cadena .= "<p><em>".$asignatura->cu_shortname." ".$asignatura->pa_nombre."</em></p>\n";
            }
            else {
                $cadena .= "<p>&nbsp;</p>\n";
                $cadena .= "<p><em>&nbsp;</em></p>\n";
            }
            $cadena .= "</td>\n";
        }
        $cadena .= "</tr>\n";
    }
    echo $cadena;
?>