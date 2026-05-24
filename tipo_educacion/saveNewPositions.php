<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

foreach ($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $query = $db->consulta("UPDATE `sw_tipo_educacion` SET `te_orden` = $newPosition WHERE `id_tipo_educacion` = $index");
}
