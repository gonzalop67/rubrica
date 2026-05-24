<?php
    require_once '../scripts/clases/class.mysql.php';
    $id_paralelo = $_POST["id_paralelo"];
    $id_horario_def = $_POST["id_horario_def"];
    $db = new MySQL();
    $dias = $db->consulta("SELECT DISTINCT(d.id_dia_semana) 
							 FROM sw_horario h,
                                  sw_dia_semana d
							WHERE d.id_dia_semana = h.id_dia_semana
                              AND id_paralelo = $id_paralelo 
                              AND h.id_horario_def = $id_horario_def 
							ORDER BY ds_orden");
    $num_total_dias = $db->num_rows($dias);
    $cadena = "";
    if ($num_total_dias > 0) {
        $cadena = "<tr>\n";
        $cadena .= "<th>HORA</th>\n";
        while ($dia = $db->fetch_object($dias)) {
            $id_dia_semana = $dia->id_dia_semana;
            $consulta = $db->consulta("SELECT ds_nombre FROM sw_dia_semana WHERE id_dia_semana = $id_dia_semana");
            $nombre_dia = $db->fetch_object($consulta);
            $cadena .= "<th>$nombre_dia->ds_nombre</th>\n";
        }
        $cadena .= "</tr>\n";
    } else {
        $cadena .= "<tr>\n";
        $cadena .= "<th align='center'>No se han asociado Dias de la semana a este Paralelo...</th>\n";
        $cadena .= "</tr>\n";
    }
    echo $cadena;
?>