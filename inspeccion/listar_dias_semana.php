<?php
    session_start();
    require_once '../scripts/clases/class.mysql.php';
    $id_usuario = $_POST["id_usuario"];
    $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    $db = new MySQL();
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