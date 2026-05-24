<?php
    include("../scripts/clases/class.mysql.php");
    
    $db = new MySQL();
    
    foreach($_POST['positions'] as $position) {
        $index = $position[0];
        $newPosition = $position[1];

        $consulta = $db->consulta("UPDATE sw_escala_calificaciones SET ec_orden = $newPosition WHERE id_escala_calificaciones = $index");
    }

    echo 'success';
?>