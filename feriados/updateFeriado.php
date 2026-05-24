<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.feriados.php");
    $feriado = new feriados();
    $feriado->code = $_POST['id_feriado'];
    $feriado->fe_fecha = $_POST['fe_fecha'];
    echo $feriado->updateFeriado();
?>