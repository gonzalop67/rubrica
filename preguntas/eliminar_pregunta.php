<?php

include_once "../scripts/clases/class.mysql.php";

$id = $_POST['id_question'];
$id_category = $_POST['id_category'];

$db = new MySQL();

$qry = "SELECT * FROM `sw_choices` WHERE id_question = $id";
$consulta = $db->consulta($qry);
$num_rows = $db->num_rows($consulta);

if ($num_rows > 0) {
    $data = array(
        "titulo"       => "Ocurrió un error inesperado.",
        "mensaje"      => "La Pregunta no se puede eliminar porque tiene alternativas...",
        "tipo_mensaje" => "error"
    );
} else {
    try {
        $qry = "DELETE FROM `sw_questions` WHERE id = " . $id;
        $consulta = $db->consulta($qry);
    
        // Re-enumerar las preguntas
        $questions = $db->consulta("SELECT * FROM sw_questions WHERE id_category = $id_category ORDER BY id ASC");
        $count = $db->num_rows($questions);
    
        $loop = 0;
    
        if ($count > 0) {
            while ($v = $db->fetch_object($questions)) {
                $loop++;
                $consulta = $db->consulta("UPDATE sw_questions SET question_no = $loop WHERE id = $v->id");
            }
        }
    
        $data = array(
            "titulo"       => "Operación exitosa.",
            "mensaje"      => "La Pregunta se ha eliminado exitosamente...",
            "tipo_mensaje" => "success"
        );
    } catch (Exception $ex) {
        $data = array(
            "titulo"       => "Ocurrió un error inesperado.",
            "mensaje"      => "La Pregunta no se pudo eliminar...Error: " . $ex->getMessage(),
            "tipo_mensaje" => "error"
        );
    }
}

echo json_encode($data);
