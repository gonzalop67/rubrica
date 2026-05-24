<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.feriados.php");
    $feriado = new feriados();
    $id_feriado = $_POST['id_feriado'];
    echo $feriado->getFeriado($id_feriado);
?>