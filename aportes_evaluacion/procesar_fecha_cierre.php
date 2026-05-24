<?php
include("../scripts/clases/class.mysql.php");
$db = new mysql();
$contador = 0;
if (isset($_POST["checkbox_value"])) {
    $fecha_cierre = $_POST["fecha_cierre"];
    for ($count = 0; $count < count($_POST["checkbox_value"]); $count++) {
        // Actualizar la fecha de cierre
        $qry = "UPDATE sw_aporte_paralelo_cierre SET ap_fecha_cierre = '$fecha_cierre' WHERE id_aporte_paralelo_cierre = '" . $_POST['checkbox_value'][$count] . "'";
        $query = $db->consulta($qry);
        // A ver... ahora si voy a actualizar el estado...
        $fechaactual = Date("Y-m-d H:i:s");
        $query = $db->consulta("SELECT ap_fecha_apertura FROM sw_aporte_paralelo_cierre WHERE id_aporte_paralelo_cierre = '" . $_POST['checkbox_value'][$count] . "'");
        $registro = $db->fetch_object($query);
        $ap_fecha_apertura = $registro->ap_fecha_apertura;
        if ($fechaactual > $ap_fecha_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
            $qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'A' WHERE id_aporte_paralelo_cierre = '" . $_POST['checkbox_value'][$count] . "'";
            $consulta = $db->consulta($qry);
        }
        if ($fechaactual > $fecha_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
            $qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'C' WHERE id_aporte_paralelo_cierre = '" . $_POST['checkbox_value'][$count] . "'";
            $consulta = $db->consulta($qry);
        }
        $contador++;
    }
}
$datos = [
    'contador' => $contador
];
echo json_encode($datos);
