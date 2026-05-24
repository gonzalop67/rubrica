<?php
include_once "../scripts/clases/class.mysql.php";

$id_alternativa = $_POST['id_alternativa'];
$is_correct = $_POST['is_correct'];
$id_pregunta = $_POST['id_pregunta'];

$db = new MySQL();

$consulta = $db->consulta("UPDATE `sw_choices` SET is_correct = '$is_correct' WHERE id = $id_alternativa");
if ($consulta) echo "Alternativa seteada correctamente.";
else echo "No se pudo setear la alternativa. Error: " . mysqli_error($this->conexion);

if ($is_correct == "1") {
    $qry = "UPDATE `sw_questions` SET is_correct = '$id_alternativa' WHERE id = $id_pregunta";
    $consulta = $db->consulta($qry);
    if ($consulta) echo "Pregunta seteada correctamente.";
    else echo "No se pudo setear la pregunta. Error: " . mysqli_error($this->conexion);
    echo "\n$id_alternativa\n$id_pregunta\n$qry";
} else {
    $qry = "UPDATE `sw_questions` SET is_correct = 0 WHERE id = $id_pregunta";
    $consulta = $db->consulta($qry);
    if ($consulta) echo "Pregunta seteada correctamente.";
    else echo "No se pudo setear la pregunta. Error: " . mysqli_error($this->conexion);
    echo "\n0\n$id_pregunta\n$qry";
}
