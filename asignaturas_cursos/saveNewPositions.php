<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

foreach ($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $query = $db->consulta("UPDATE `sw_asignatura_curso` SET `ac_orden` = $newPosition WHERE `id_asignatura_curso` = $index");
}