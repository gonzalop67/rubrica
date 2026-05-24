<?php
include_once "../scripts/clases/class.mysql.php";

$question = $_POST['question'];

$id_question = $_POST['id_question'];
$id_category = $_POST['id_category'];

$db = new MySQL();

$question = mysqli_real_escape_string($db->conexion, $question); // Escapa el apóstrofo

// Validaciones
$query = "SELECT id "
    . " FROM `sw_questions` "
    . "WHERE question = '" . $question . "' "
    . "  AND id_category = " . $id_category;


$consulta = $db->consulta($query);

$existePregunta = $db->num_rows($consulta) > 0;

$consulta = $db->consulta("SELECT * FROM `sw_questions` WHERE id = " . $id_question);
$preguntaActual = $db->fetch_object($consulta);

//Actualizar
try {

    $query = 'UPDATE `sw_questions` SET question = "' . trim($question) . '" WHERE id = ' . trim($id_question);

    $consulta = $db->consulta($query);

    $datos = [
        'titulo' => "¡Actualizado con éxito!",
        'mensaje' => "Actualización realizada exitosamente.",
        'estado' => 'success'
    ];
} catch (\Throwable $th) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "No se pudo realizar la actualización. Error: " . $th->getMessage(),
        'estado' => 'error'
    ];
}

//Enviar el objeto en formato json
echo json_encode($datos);
