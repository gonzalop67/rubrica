<?php
include_once "../scripts/clases/class.mysql.php";

$id = $_POST['id_choice'];

$db = new MySQL();

try {
    $qry = "DELETE FROM `sw_choices` WHERE id = " . $id;
    $consulta = $db->consulta($qry);
    $data = [
        "titulo"       => "Operación exitosa.",
        "mensaje"      => "La Alternativa se ha eliminado exitosamente...",
        "tipo_mensaje" => "success"
    ];
} catch (\Throwable $ex) {
    $data = [
        "titulo"       => "Ocurrió un error inesperado.",
        "mensaje"      => "La Alternativa no se pudo eliminar...Error: " . $ex->getMessage(),
        "tipo_mensaje" => "error"
    ];
}

echo json_encode($data);