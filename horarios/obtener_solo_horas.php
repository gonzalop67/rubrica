<?php
include("../scripts/clases/class.mysql.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_horario_def'])) {
    
    $db = new MySQL();
    $id_horario_def = $db->filtrar($_POST['id_horario_def']);

    $sqlHoras = "SELECT id_horario_detalle, nombre, hora_inicio, hora_fin 
                 FROM sw_horario_detalles 
                 WHERE id_horario_def = '$id_horario_def' 
                 ORDER BY hora_inicio ASC";
    $resHoras = $db->consulta($sqlHoras);

    $listadoHoras = [];
    while ($row = $db->fetch_assoc($resHoras)) {
        $listadoHoras[] = $row;
    }

    echo json_encode($listadoHoras);
} else {
    echo json_encode([]);
}
