<?php
    // session_start();
    include("../scripts/clases/class.mysql.php");
    $id_paralelo = $_POST["id_paralelo"];
    $id_periodo_lectivo = $_POST["id_periodo_lectivo"];
    $db = new MySQL();
    // Primero obtengo la lista de estudiantes del paralelo
    $escala = array();
    $escala[] = array(
        'etiqueta' => 'Aprobados: ',
        'contador' => 0
    );
    $escala[] = array(
        'etiqueta' => 'Reprobados: ',
        'contador' => 0
    );
    $datos = array();
    $query = "SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE activo = 1 AND es_retirado = 'N' AND id_paralelo = $id_paralelo";
    $estudiantes = $db->consulta($query);
    $num_total_estudiantes = $db->num_rows($estudiantes);
    if ($num_total_estudiantes > 0) {
        while($estudiante = $db->fetch_assoc($estudiantes)) {
            $id_estudiante = $estudiante["id_estudiante"];
            $query = "SELECT es_promocionado($id_estudiante,$id_periodo_lectivo,$id_paralelo) AS aprueba";
            $consulta = $db->consulta($query);
            $registro = $db->fetch_assoc($consulta);
            if ($registro["aprueba"]==1)
                $escala[0]['contador'] = $escala[0]['contador'] + 1;
            else
                $escala[1]['contador'] = $escala[1]['contador'] + 1;
        }
    }
    // Calculo de porcentajes de acuerdo a la escala de calificaciones				
    for($i = 0; $i < count($escala); $i++) {
        $datos[] = array('etiqueta' => $escala[$i]['etiqueta'].$escala[$i]['contador'],
                        'porcentaje' => $escala[$i]['contador'] / $num_total_estudiantes * 100);
    }
    echo json_encode($datos);
?>