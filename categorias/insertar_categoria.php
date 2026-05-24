<?php
include_once "../scripts/clases/class.mysql.php";

$id_usuario = $_POST["id_usuario"];
$category = $_POST["category"];
$exam_time_in_minutes = $_POST["exam_time_in_minutes"];

$db = new MySQL();

$query = "SELECT id "
    . " FROM sw_exam_category "
    . "WHERE category = '" . $category . "' "
    . "  AND id_usuario = $id_usuario";

$consulta = $db->consulta($query);

if ($db->num_rows($consulta) > 0) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "Ya existe el nombre de la categoria en la base de datos.",
        'estado' => 'error'
    ];
} else {
    $qry = "INSERT INTO sw_exam_category (id_usuario, category, exam_time_in_minutes) VALUES (";
    $qry .= $id_usuario . ",'" . $category . "'," . $exam_time_in_minutes . ")";

    try {
        $consulta = $db->consulta($qry);

        $datos = [
            'titulo' => "¡Agregado con éxito!",
            'mensaje' => "Inserción realizada exitosamente.",
            'estado' => 'success'
        ];
    } catch (\Throwable $th) {
        $datos = [
            'titulo' => "¡Error!",
            'mensaje' => "No se pudo realizar la inserción. Error: " . $th->getMessage(),
            'estado' => 'error'
        ];
    }
}

echo json_encode($datos);
