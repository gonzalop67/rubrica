<?php
    session_start();
    $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.feriados.php");
    $feriado = new feriados();
    $feriado->id_periodo_lectivo = $id_periodo_lectivo;
    $feriado->fe_fecha = $_POST['fe_fecha'];
    echo $feriado->storeFeriado();
?>