<?php
include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.horarios.php");

header('Content-Type: application/json; charset=utf-8');

$horario = new horarios();

$id_paralelo   = $_POST["id_paralelo"] ?? 0;
$id_horario_def = $_POST["id_horario_def"] ?? 0;

if ($id_paralelo > 0 && $id_horario_def > 0) {
    echo $horario->cargarHorarioMatriz($id_paralelo, $id_horario_def);
} else {
    echo json_encode(array());
}
exit;
?>
