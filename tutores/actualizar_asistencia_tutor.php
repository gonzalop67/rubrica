<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// 1. Recibir y desinfectar variables POST (Seguridad ante todo)
$id_estudiante   = $db->filtrar($_POST["id_estudiante"]);
$id_paralelo     = $db->filtrar($_POST["id_paralelo"]);
$ae_fecha        = $db->filtrar($_POST["ae_fecha"]);
$id_inasistencia = $db->filtrar($_POST["id_inasistencia"]);

// 2. Ejecutar la actualización del estado que el tutor seleccionó en el checkbox
$query = "UPDATE sw_asistencia_tutor 
              SET id_inasistencia = $id_inasistencia 
              WHERE id_estudiante = $id_estudiante 
                AND id_paralelo = $id_paralelo 
                AND at_fecha = '$ae_fecha'";

$db->consulta($query);

// 3. Definir dinámicamente la observación de texto que se pintará en caliente en el HTML
$observacion = "";
if (intval($id_inasistencia) === 1) {
    $observacion = "ASISTE";
} else {
    $observacion = "FALTA INJUSTIFICADA";
}

// 4. Recalcular el número acumulado de alumnos ASISTENTES hoy en el paralelo
$queryAsis = "SELECT COUNT(*) AS asistentes FROM sw_asistencia_tutor 
                  WHERE id_paralelo = $id_paralelo 
                    AND at_fecha = '$ae_fecha' 
                    AND id_inasistencia = 1";

$resAsis = $db->consulta($queryAsis);
$regAsis = $db->fetch_assoc($resAsis); // CORREGIDO: Adaptado a tu método nativo de la clase
$asistentes = $regAsis['asistentes'];

// 5. Recalcular el número acumulado de alumnos AUSENTES hoy en el paralelo
$queryAus = "SELECT COUNT(*) AS ausentes FROM sw_asistencia_tutor 
                 WHERE id_paralelo = $id_paralelo 
                   AND at_fecha = '$ae_fecha' 
                   AND id_inasistencia != 1";

$resAus = $db->consulta($queryAus);
$regAus = $db->fetch_assoc($resAus); // CORREGIDO: Adaptado a tu método nativo de la clase
$ausentes = $regAus['ausentes'];

// 6. Empaquetar y responder en el formato JSON idéntico que espera tu script jQuery
$datos = [
    'asistentes' => $asistentes,
    'ausentes' => $ausentes,
    'observacion' => $observacion
];

echo json_encode($datos);
