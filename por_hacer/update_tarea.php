<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id = $_POST['id_tarea'];
$tarea = $_POST['tarea'];

$consulta = $db->consulta("UPDATE sw_por_hacer SET tarea = '$tarea' WHERE id = $id");

$datos = [];

if ($consulta) {
    //Mensaje de operación exitosa
    $datos = [
        'titulo' => "Actualizado con éxito!",
        'mensaje' => "Actualización realizada exitosamente.",
        'tipo_mensaje' => 'success'
    ];
} else {
    //Mensaje de operación fallida
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "No se pudo realizar la actualización. Error: " . mysqli_error($db->conexion),
        'tipo_mensaje' => 'error'
    ];
} 

echo json_encode($datos);
