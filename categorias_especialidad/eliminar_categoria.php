<?php
include_once "../scripts/clases/class.mysql.php";
$db = new MySQL();

$id_categoria = $_POST['id_categoria'];

try {
    $qry = "SELECT id_especialidad FROM sw_especialidad WHERE categoria_id = $id_categoria";
    $consulta = $db->consulta($qry);
    if ($db->num_rows($consulta) > 0) {
        $data = array(
            "titulo" => "Ocurrió un error inesperado.",
            "mensaje" => "La Categoria de Especialidad tiene Especialidades relacionadas...",
            "estado" => "error"
        );
    } else {
        $qry = "DELETE FROM sw_categoria_especialidad WHERE id_categoria = " . $id_categoria;
        $consulta = $db->consulta($qry);
        $data = array(
            "titulo"  => "Operación exitosa.",
            "mensaje" => "La Categoria de Especialidad se ha eliminado exitosamente...",
            "estado"  => "success"
        );
    }
} catch (Exception $ex) {
    $data = array(
        "titulo"  => "Ocurrió un error inesperado.",
        "mensaje" => "La Categoria de Especialidad no se pudo eliminar...Error: " . $ex->getMessage(),
        "estado"  => "error"
    );
}

echo json_encode($data);
