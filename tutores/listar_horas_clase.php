<?php
    require_once '../scripts/clases/class.mysql.php';
    $id_paralelo = $_POST["id_paralelo"];
    $id_horario_def = $_POST["id_horario_def"];
    $db = new MySQL();
    //                               AND hc_tipo = 'C'
    $horas = $db->consulta("SELECT DISTINCT(hc.id_hora_clase)
                              FROM sw_horario ho, 
                                   sw_hora_clase hc
                             WHERE ho.id_hora_clase = hc.id_hora_clase
                               AND id_paralelo = $id_paralelo 
                               AND ho.id_horario_def = $id_horario_def 
                             ORDER BY hc_orden");
    $cadena = "";
    while ($hora = $db->fetch_object($horas)) {
        $cadena .= "<tr>\n";
        $id_hora_clase = $hora->id_hora_clase;
        // Consulto el nombre de la hora clase
        $consulta = $db->consulta("SELECT hc_nombre FROM sw_hora_clase WHERE id_hora_clase = $id_hora_clase");
        $hora_clase = $db->fetch_object($consulta);
        $cadena .= "<td class='text-center'><span style='font-size: 12pt'><strong>$hora_clase->hc_nombre</strong></span></td>\n";
        // Acá obtengo los dias de la semana asociados al paralelo
        $dias = $db->consulta("SELECT DISTINCT(d.id_dia_semana) 
                                 FROM sw_horario h,
                                      sw_dia_semana d
                                WHERE d.id_dia_semana = h.id_dia_semana
                                  AND id_paralelo = $id_paralelo 
                                  AND h.id_horario_def = $id_horario_def 
                                ORDER BY ds_orden");
        while ($dia = $db->fetch_object($dias)) {
            $id_dia_semana = $dia->id_dia_semana;
            // Consulto la asignatura del dia y hora correspondientes
            $consulta = $db->consulta("SELECT a.id_asignatura,
                                                 as_nombre
                                            FROM sw_horario ho, 
                                                 sw_hora_clase hc, 
                                                 sw_asignatura a
                                           WHERE ho.id_hora_clase = hc.id_hora_clase 
                                             AND ho.id_asignatura = a.id_asignatura 
                                             AND ho.id_dia_semana = $id_dia_semana 
                                             AND ho.id_hora_clase = $id_hora_clase 
                                             AND id_paralelo = $id_paralelo");
            $asignatura = $db->fetch_object($consulta);
            if($asignatura){
              $id_asignatura = $asignatura->id_asignatura;
              $cadena .= "<td>\n";
              $cadena .= "<p>$asignatura->as_nombre</p>\n";
              // Obtengo el docente que imparte esta asignatura
              $query = $db->consulta("SELECT us_shortname 
                                        FROM sw_asignatura a, 
                                             sw_distributivo d,
                                             sw_usuario u
                                       WHERE a.id_asignatura = d.id_asignatura
                                         AND u.id_usuario = d.id_usuario
                                         AND d.id_asignatura = $id_asignatura
                                         AND d.id_paralelo = $id_paralelo");
              $docente = $db->fetch_object($query);
              $cadena .= "<p><em>$docente->us_shortname</em></p>\n";
              $cadena .= "</td>\n";
            }else{
              $cadena .= "<td>&nbsp;</td>\n";
            }
        }
        $cadena .= "</tr>\n";
    }
    echo $cadena;
?>