<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.tutores.php");
    $id_paralelo = $_POST["id_paralelo"];
    $id_estudiante = $_POST["id_estudiante"];
    $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
    $tutor = new tutores();
    echo $tutor->obtenerIdComportamientoTutor($id_paralelo, $id_estudiante, $id_aporte_evaluacion);
?>
