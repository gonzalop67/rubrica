<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.estudiantes.php");
    session_start();
    $estudiante = new estudiantes();
    $estudiante->code = $_POST['id_estudiante'];
    $estudiante->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	if($estudiante->existenCalificaciones()){
        $data = array(
            'error' => true,
        );
    }else{
        $data = array(
            'error' => false
        );
    }
    echo json_encode($data);
?>