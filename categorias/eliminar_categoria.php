<?php
include_once "../scripts/clases/class.mysql.php";
$db = new MySQL();

$id_categoria = $_POST['id_categoria'];

try {
    $qry = "DELETE FROM sw_exam_category WHERE id = " . $id_categoria;
    $consulta = $db->consulta($qry);
    $data = array(
        "titulo"       => "Operación exitosa.",
        "mensaje"      => "La Categoria se ha eliminado exitosamente...",
        "estado" => "success"
    );
} catch (Exception $ex) {
    $data = array(
        "titulo"       => "Ocurrió un error inesperado.",
        "mensaje"      => "La Categoria no se pudo eliminar...Error: " . $ex->getMessage(),
        "estado" => "error"
    );
}

echo json_encode($data);