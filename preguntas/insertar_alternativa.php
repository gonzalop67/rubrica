<?php
include_once "../scripts/clases/class.mysql.php";

$choice = $_POST['choice'];
$choice = str_replace("'", "''", $choice); // Convierte ' en ''
$id_question = $_POST['id_question'];

$db = new MySQL();

// Validaciones
$query = "SELECT id "
        . " FROM `sw_choices` "
        . "WHERE text = '" . $choice ."'"
        . "  AND id_question = $id_question";

$choices = $db->consulta($query);
$num_choices = $db->num_rows($choices);

if ($num_choices > 0) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "Ya existe el texto de la alternativa para esta pregunta.",
        'estado' => 'error'
    ];
} else {
    try {
        $query = "INSERT INTO `sw_choices` SET id_question = $id_question, is_correct = 0, text = '$choice'";
        $choices = $db->consulta($query);

        $datos = [
            'titulo' => "¡Agregado con éxito!",
            'mensaje' => "Inserción realizada exitosamente.",
            'estado' => 'success'
        ];
    } catch (\Throwable $e) {
        $datos = [
            'titulo' => "¡Error!",
            'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
            'estado' => 'error'
        ];
    }
}

echo json_encode($datos);