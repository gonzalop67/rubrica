<?php
    include("../scripts/clases/class.mysql.php");
    $db= new mysql();
    $id_dia_semana = $_POST["id_dia_semana"];
    $id_paralelo = $_POST["id_paralelo"];
    $consulta = $db->consulta("SELECT * FROM sw_horario WHERE id_dia_semana = $id_dia_semana AND id_paralelo = $id_paralelo");
    $num_rows = $db->num_rows($consulta);
    echo $num_rows > 0;
?>