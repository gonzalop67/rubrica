<?php
include_once "../scripts/clases/class.mysql.php";

$choice = $_POST['choice'];
$id_choice = $_POST['id'];
$id_question = $_POST['id_question'];

$db = new MySQL();

// Validaciones
$query = "SELECT id "
    . " FROM `sw_choices` "
    . "WHERE text = '" . $choice . "' "
    . "  AND id_question = " . $id_question;


$consulta = $db->consulta($query);

$existeAlternativa = $db->num_rows($consulta) > 0;

$consulta = $db->consulta("SELECT * FROM `sw_choices` WHERE id = " . $id_choice);
$alternativaActual = $db->fetch_object($consulta);

if ($alternativaActual->text != $choice && $existeAlternativa) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "Ya existe el texto de la alternativa para esta pregunta.",
        'estado' => 'error'
    ];
} else {
    //Actualizar
    try {

        $query = 'UPDATE `sw_choices` SET text = "' . trim($choice) . '" WHERE id = ' . trim($id_choice);

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
} 

// //Enviar el objeto en formato json
echo json_encode($datos);
