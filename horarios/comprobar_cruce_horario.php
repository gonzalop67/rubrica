<?php
include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.horarios.php");

// Forzar respuesta JSON limpia y evitar problemas de caché o caracteres
header('Content-Type: application/json; charset=utf-8');

$horario = new horarios();

$id_paralelo    = $_POST["id_paralelo"];
$id_asignatura  = $_POST["id_asignatura"];
$id_dia_semana  = $_POST["id_dia_semana"];
$id_hora_clase  = $_POST["id_hora_clase"];
$id_horario_def = $_POST["id_horario_def"];

// EJECUCIÓN ÚNICA: Guardamos el código (0, 1 o 2) en una variable
$codigo_error = $horario->comprobarCruceDeHorario($id_paralelo, $id_asignatura, $id_dia_semana, $id_hora_clase, $id_horario_def);

// Estructuramos la respuesta según el código obtenido
if ($codigo_error == 2) {
    echo json_encode(array('error' => true, 'errorno' => 2));
} else if ($codigo_error == 1) {
    echo json_encode(array('error' => true, 'errorno' => 1));
} else {
    echo json_encode(array('error' => false, 'errorno' => 0));
}
exit; // Asegura que no se imprima nada más después del JSON
?>

