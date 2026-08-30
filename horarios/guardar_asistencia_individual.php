<?php
// session_start();
include_once "../scripts/clases/class.mysql.php"; 

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_estudiante'])) {
    $db = new MySQL();

    $id_estudiante        = $db->filtrar($_POST['id_estudiante']);
    $id_horario_def       = $db->filtrar($_POST['id_horario_def']);
    $id_paralelo          = $db->filtrar($_POST['id_paralelo']);
    $id_asignatura        = $db->filtrar($_POST['id_asignatura']);
    $id_horario_detalle   = $db->filtrar($_POST['id_horario_detalle']);
    $fecha                = $db->filtrar($_POST['fecha']);
    $id_tipo_inasistencia = $db->filtrar($_POST['id_tipo_inasistencia']);

    try {
        // Inserta o actualiza el tipo exacto de inasistencia gracias al UNIQUE KEY
        $sql = "INSERT INTO sw_asistencia_estudiante (
                    id_estudiante, id_horario_def, id_paralelo, id_asignatura, id_horario_detalle, ae_fecha, id_tipo_inasistencia
                ) VALUES (
                    '$id_estudiante', '$id_horario_def', '$id_paralelo', '$id_asignatura', '$id_horario_detalle', '$fecha', '$id_tipo_inasistencia'
                )
                ON DUPLICATE KEY UPDATE id_tipo_inasistencia = '$id_tipo_inasistencia'";
        
        $db->consulta($sql);
        echo json_encode(["success" => true, "mensaje" => "Asistencia guardada."]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "mensaje" => $e->getMessage()]);
    }
}
?>
