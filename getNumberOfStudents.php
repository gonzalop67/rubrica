<?php
    require_once("scripts/clases/class.mysql.php");
    require_once("scripts/clases/class.estudiantes.php");
    $id_periodo_lectivo = $_POST["id_periodo_lectivo"];
    $id_jornada = $_POST["id_jornada"];
    $estudiante = new estudiantes();
    $resultado = $estudiante->getNumeroEstudiantesPorParalelo($id_periodo_lectivo, $id_jornada);
    echo $resultado;
?>