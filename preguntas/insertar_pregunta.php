<?php
include_once "../scripts/clases/class.mysql.php";

$question = $_POST['question'];
$question = str_replace("'", "''", $question); // Convierte ' en ''
$id_category = $_POST['id_category'];

$db = new MySQL();

// Validaciones
$query = "SELECT id "
        . " FROM `sw_questions` "
        . "WHERE question = '" . $question ."'"
        . "  AND id_category = $id_category";

$questions = $db->consulta($query);
$num_questions = $db->num_rows($questions);

if ($num_questions > 0) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "Ya existe el texto de la pregunta para esta categoria.",
        'estado' => 'error'
    ];
} else {
    try {
        $query = "SELECT COUNT(*) AS numero_preguntas FROM `sw_questions` WHERE id_category = $id_category";
        $consulta = $db->consulta($query);
        $registro = $db->fetch_object($consulta);
        $maximo = ($registro->numero_preguntas == 0) ? 1 : $registro->numero_preguntas + 1;

        $query = "INSERT INTO `sw_questions` SET id_category = $id_category, question_no = $maximo, question = '$question'";
        $questions = $db->consulta($query);

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