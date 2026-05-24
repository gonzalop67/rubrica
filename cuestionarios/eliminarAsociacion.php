<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$id_cuestionario_paralelo = $_POST['id'];

try {
    $consulta = $db->consulta("DELETE FROM sw_cuestionario_paralelo WHERE id = $id_cuestionario_paralelo");

    $data = array(
        "titulo"       => "Operación exitosa.",
        "mensaje"      => "La Asociación Cuestionario Paralelo fue eliminada correctamente.",
        "tipo_mensaje" => "success"
    );
} catch (Exception $ex) {
    $data = array(
        "titulo"       => "Operación exitosa.",
        "mensaje"      => "La Asociación Cuestionario Paralelo NO fue eliminada correctamente. Error: " . $ex->getMessage(),
        "tipo_mensaje" => "error"
    );
}

echo json_encode($data);
