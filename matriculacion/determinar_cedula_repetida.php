<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
    $estudiante = new estudiantes();
    $estudiante->es_cedula = $_POST["es_cedula"];
    echo $estudiante->determinarCedulaRepetida();
?>