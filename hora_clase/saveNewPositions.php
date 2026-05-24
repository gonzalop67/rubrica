<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

foreach ($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $query = $db->consulta("UPDATE `sw_hora_clase` SET `hc_ordinal` = $newPosition WHERE `id_hora_clase` = $index");
}
