<?php

//delete_comment.php

require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$error = '';
$id_comentario = $_POST["comment_id"];

$query = "SELECT COUNT(*) AS respuestas FROM `sw_comentario` WHERE id_comentario_padre = $id_comentario";
$consulta = $db->consulta($query);
$respuestas = $db->fetch_object($consulta)->respuestas;
if ($respuestas > 0) {
    $titulo = "Infórmese!";
    $mensaje = "No se puede eliminar este comentario porque tiene respuestas asociadas...";
    $estado = "warning";
} else {
    $query = "DELETE FROM `sw_comentario` WHERE id_comentario = $id_comentario";
    $consulta = $db->consulta($query);
    if ($consulta) {
        $titulo = "Eliminación exitosa.";
        $mensaje = "Comentario Eliminado!";
        $estado = "success";
    } else {
        $titulo = "ERROR EN LA CONSULTA...";
        $error .= "No se pudo eliminar el comentario...Error: " . mysqli_error($db->conexion) . "<br />Consulta: " . $query;
        $estado = "danger";
    }
}

$data = array(
    'titulo'  => $titulo,
    'mensaje' => $mensaje,
    'estado'  => $tipo_mensaje
);

echo json_encode($data);
