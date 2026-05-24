<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.estudiantes.php");
    session_start();
    $estudiante = new estudiantes();
    $estudiante->code = $_POST['id_estudiante'];
    $estudiante->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	if($estudiante->verificarEstudianteMatriculado()){
        $data = array(
            'error' => true,
            'mensaje' => 'El estudiante ya se encuentra matriculado en el presente periodo lectivo...'
        );
    }else{
        $data = array(
            'error' => false
        );
    }
    echo json_encode($data);
?>