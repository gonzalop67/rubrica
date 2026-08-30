<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

header('Content-Type: application/json; charset=utf-8');

// Capturamos el número del día que calculó tu función matemática de JS (1 al 5)
$ds_ordinal = isset($_POST['ds_ordinal']) ? intval($_POST['ds_ordinal']) : 0;

if ($ds_ordinal > 0) {
    // Rompemos la dependencia de la tabla sw_dia_semana.
    // Devolvemos el mismo número directo para que calce perfecto con tu columna 'dia_semana'
    echo json_encode([
        "id_dia_semana" => $ds_ordinal
    ]);
} else {
    echo json_encode(false);
}
exit;
?>
