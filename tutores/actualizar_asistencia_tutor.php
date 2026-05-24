<?php
    include("../scripts/clases/class.mysql.php");
    $db = new MySQL();

    // Variables POST
    $id_estudiante= $_POST["id_estudiante"];
    $id_paralelo= $_POST["id_paralelo"];
    $ae_fecha= $_POST["ae_fecha"];
    $id_inasistencia= $_POST["id_inasistencia"];

    $query = "UPDATE sw_asistencia_tutor SET id_inasistencia = $id_inasistencia WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND at_fecha = '$ae_fecha'";
    $consulta = $db->consulta($query);

    $observacion = "";

    if ($id_inasistencia == 1) {
        $observacion = "ASISTE";
    } else {
        $observacion = "FALTA INJUSTIFICADA";
    }

    $query = "SELECT COUNT(*) AS asistentes FROM sw_asistencia_tutor WHERE id_paralelo = $id_paralelo AND at_fecha = '$ae_fecha' AND id_inasistencia = 1";
    $registro = $db->consulta($query)->fetch_object();
    $asistentes = $registro->asistentes;

    $query = "SELECT COUNT(*) AS ausentes FROM sw_asistencia_tutor WHERE id_paralelo = $id_paralelo AND at_fecha = '$ae_fecha' AND id_inasistencia != 1";
    $registro = $db->consulta($query)->fetch_object();
    $ausentes = $registro->ausentes;

    $datos = [
        'asistentes' => $asistentes,
        'ausentes' => $ausentes,
        'observacion' => $observacion
    ];

    echo json_encode($datos);
?>