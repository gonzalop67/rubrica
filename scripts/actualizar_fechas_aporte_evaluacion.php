<?php
    include("clases/class.mysql.php");
    
    $db = new MySQL();
    
    $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$id_paralelo = $_POST["id_paralelo"];
	$ap_nombre = $_POST["ap_nombre"];
	$ap_fecha_apertura = $_POST["fecha_apertura"];
    $ap_fecha_cierre = $_POST["fecha_cierre"];
    
    date_default_timezone_set('America/Guayaquil');
		
    $qry = "UPDATE sw_aporte_paralelo_cierre SET ";
    $qry .= "ap_fecha_apertura = '$ap_fecha_apertura',";
    $qry .= "ap_fecha_cierre = '$ap_fecha_cierre'";
    $qry .= " WHERE id_aporte_evaluacion = $id_aporte_evaluacion";
    $qry .= " AND id_paralelo = $id_paralelo";
    $consulta = $db->consulta($qry);

    // A ver... ahora si voy a actualizar el estado...
    $fechaactual = Date("Y-m-d H:i:s");
    
    if ($fechaactual > $ap_fecha_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
        $qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'A' WHERE id_paralelo = ". $id_paralelo . " AND id_aporte_evaluacion=". $id_aporte_evaluacion;
        $consulta = $db->consulta($qry);
    }

    if ($fechaactual > $ap_fecha_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
        $qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'C' WHERE id_paralelo = ". $id_paralelo . " AND id_aporte_evaluacion=". $id_aporte_evaluacion;
        $consulta = $db->consulta($qry);
    }

    $mensaje = "Aporte de Evaluacion " . $ap_nombre . " actualizado exitosamente...";
    echo $mensaje;
?>
