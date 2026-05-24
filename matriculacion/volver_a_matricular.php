<?php
    include("../scripts/clases/class.mysql.php");
    $db = new MySQL();
    $id_estudiante_periodo_lectivo = $_POST['id_estudiante_periodo_lectivo'];
    try {
        $query = $db->consulta("UPDATE sw_estudiante_periodo_lectivo SET activo = 1 WHERE id_estudiante_periodo_lectivo = $id_estudiante_periodo_lectivo");
        $data = array(
            "titulo"       => "Operación exitosa.",
            "mensaje"      => "El estudiante fue matriculado nuevamente de manera exitosa.",
            "tipo_mensaje" => "success"
        );
        echo json_encode($data);
    } catch (\Exception $e) {
        $data = array(
            "titulo"       => "Ocurrió un error al tratar de volver a matricular al estudiante.",
            "mensaje"      => "Error...: " . $e->getMessage(),
            "tipo_mensaje" => "error"
        );
        echo json_encode($data);
    }
?>