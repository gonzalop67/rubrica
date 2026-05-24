<?php
	include("../scripts/clases/class.mysql.php");

    $db = new MySQL();

    // Variables POST
    $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
    $id_paralelo = $_POST['id_paralelo'];

    // Consulto la fecha de apertura del aporte de evaluación
    $consulta = $db->consulta("SELECT ap_fecha_apertura FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = " . $id_aporte_evaluacion . " AND id_paralelo = " . $id_paralelo);
    if ($consulta) {
        $aporte = $db->fetch_assoc($consulta);
        $fecha = $aporte["ap_fecha_apertura"];
        // Comparar la fecha actual con la fecha de apertura
        date_default_timezone_set('America/Guayaquil');
        $fecha_actual = Date("Y-m-d H:i:s");
        if ($fecha_actual > $fecha) {
            $estado = "1";
        } else {
            $estado = "2";
        }
    } else {
        $estado = "3";
        $fecha_apertura = "";
    }

    echo json_encode([
        'estado' => $estado,
        'fecha_apertura' => $fecha
    ]);
?>