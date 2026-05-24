<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

foreach ($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $query = $db->consulta("UPDATE `sw_aporte_evaluacion` SET `ap_orden` = $newPosition WHERE `id_aporte_evaluacion` = $index");
}
