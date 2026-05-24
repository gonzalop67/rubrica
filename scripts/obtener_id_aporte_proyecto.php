<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

$qry = "SELECT ap.id_aporte_evaluacion 
          FROM `sw_tipo_aporte` ta, 
               `sw_aporte_evaluacion` ap, 
               `sw_periodo_evaluacion` pe 
         WHERE ta.id_tipo_aporte = ap.id_tipo_aporte 
           AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion 
           AND pe.id_periodo_evaluacion = $id_periodo_evaluacion 
           AND pe.id_periodo_lectivo = $id_periodo_lectivo 
           AND ta_descripcion = 'FASE_PRI'";

try {
    $consulta = $db->consulta($qry);
    $num_registros = $db->num_rows($consulta);

    if ($num_registros > 0) {
        $id_aporte_evaluacion = $db->fetch_object($consulta)->id_aporte_evaluacion;
        $data = [
            'errorno' => 0,
            'error' => 'No existe error',
            'id_aporte_evaluacion' => $id_aporte_evaluacion
        ];
    } else {
        $data = [
            'errorno' => 2,
            'error' => 'No se ha definido el aporte de evaluación...',
            'id_aporte_evaluacion' => 0
        ];
    }
} catch (\Throwable $e) {
    $data = [
        'errorno' => 1,
        'error' => $e->getMessage(),
        'id_aporte_evaluacion' => 0
    ];
}

echo json_encode($data);
