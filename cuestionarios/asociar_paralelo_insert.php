<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

session_start();

$id_usuario = $_SESSION['id_usuario'];
$id_categoria = $_POST['id_categoria'];
$id_paralelo = $_POST['id_paralelo'];
$id_asignatura = $_POST['id_asignatura'];

// Primero comprobar si ya existe la asociación en la bd
$consulta = $db->consulta("SELECT * FROM sw_cuestionario_paralelo WHERE id_category = $id_categoria AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");

$existeAsociacion = $db->num_rows($consulta) > 0;

$data = array();

if ($existeAsociacion) {
    $data = array(
        "titulo"       => "Ocurrió un error inesperado.",
        "mensaje"      => "Ya existe la asociacion entre el cuestionario, el paralelo y la asignatura seleccionados.",
        "tipo_mensaje" => "error"
    );
} else {
    try {
        $consulta = $db->consulta("INSERT INTO sw_cuestionario_paralelo SET id_usuario = $id_usuario, id_category = $id_categoria, id_paralelo = $id_paralelo, id_asignatura = $id_asignatura");

        $data = array(
            "titulo"       => "Operación exitosa.",
            "mensaje"      => "La Asociación Cuestionario Paralelo fue guardada correctamente.",
            "tipo_mensaje" => "success"
        );
    } catch (Exception $ex) {
        $data = array(
            "titulo"       => "Operación exitosa.",
            "mensaje"      => "La Asociación Cuestionario Paralelo NO fue guardada correctamente. Error: " . $ex->getMessage(),
            "tipo_mensaje" => "error"
        );
    }
}

echo json_encode($data);
