<?php
	include("../scripts/clases/class.mysql.php");
	$db = new MySQL();

    // Variables POST
    $id_estudiante = $_POST["id_estudiante"];
    $id_paralelo = $_POST["id_paralelo"];
    $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
    $estado_autorizado = $_POST["autorizado"];

    // Variable de sesión para el id del usuario
    session_start();
    $id_usuario = $_SESSION["id_usuario"];

    // Seteamos la zona horaria
    date_default_timezone_set('America/Guayaquil');

    // Fecha actual
    $fecha_actual = date("Y-m-d H:i:s");

    if ($estado_autorizado == "S") {
        // Si el estado es "S" se procede a insertar el registro en la tabla de autorizaciones
        $sql = "INSERT INTO sw_autorizacion (id_estudiante, id_paralelo, id_aporte_evaluacion, id_usuario, fecha_autorizacion) VALUES ($id_estudiante, $id_paralelo, $id_aporte_evaluacion, $id_usuario, '$fecha_actual')";
    } else {
        // Si el estado es "N" se procede a eliminar el registro de la tabla de autorizaciones
        $sql = "DELETE FROM sw_autorizacion WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_aporte_evaluacion = $id_aporte_evaluacion";
    }
    $db->consulta($sql);
?>