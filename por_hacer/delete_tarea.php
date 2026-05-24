<?php
require_once("../scripts/clases/class.mysql.php");

$db = new MySQL();

$id = $_POST['id'];

try {
    $qry = "DELETE FROM sw_por_hacer WHERE id = " . $id;
    $consulta = $db->consulta($qry);
    $data = array(
        "titulo"       => "Operacion exitosa.",
        "mensaje"      => "La Tarea se ha eliminado exitosamente...",
        "tipo_mensaje" => "success"
    );
} catch (Exception $ex) {
    $data = array(
        "titulo"       => "Ocurrio un error inesperado.",
        "mensaje"      => "No se pudo eliminar la Tarea...Error: " . $ex->getMessage(),
        "tipo_mensaje" => "error"
    );
}

echo json_encode($data);
