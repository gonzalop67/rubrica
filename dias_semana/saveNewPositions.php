<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

foreach ($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $query = $db->consulta("UPDATE `sw_dia_semana` SET `ds_ordinal` = $newPosition WHERE `id_dia_semana` = $index");
}
