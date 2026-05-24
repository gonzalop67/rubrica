<?php
include("scripts/clases/class.mysql.php");
$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];
$id_periodo_lectivo = $_POST["id_periodo_lectivo"];

// Primero consulto las escalas de calificaciones
$query = "SELECT ec_cuantitativa, ec_nota_minima, ec_nota_maxima FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY ec_orden";
$result = $db->consulta($query);
$escala = array();
while ($dato = $db->fetch_array($result)) {
    $escala[] = array(
        'escala' => $dato['ec_cuantitativa'],
        'minima' => $dato['ec_nota_minima'],
        'maxima' => $dato['ec_nota_maxima'],
        'contador' => 0
    );
}

// Aqui va el codigo para calcular el promedio del aporte de cada estudiante
$promedios = array();
$estudiantes = $db->consulta("SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = $id_paralelo AND activo = 1 AND es_retirado = 'N'");
$num_total_estudiantes = $db->num_rows($estudiantes);
if ($num_total_estudiantes > 0) {
    while ($estudiante = $db->fetch_assoc($estudiantes)) {
        $id_estudiante = $estudiante["id_estudiante"];
        //Aqui calculo el promedio general final del estudiante
        $qry = $db->consulta("SELECT calcular_promedio_general(" . $id_periodo_lectivo . "," . $id_estudiante . "," . $id_paralelo . ") AS promedio_general");
        $registro = $db->fetch_assoc($qry);
        $promedio_final = $registro["promedio_general"];
        array_push($promedios, $promedio_final);

        // Calculo de cantidad de estudiantes de acuerdo a la escala de calificaciones
        for ($i = 0; $i < count($escala); $i++) {
            $nota_minima = $escala[$i]['minima'];
            $nota_maxima = $escala[$i]['maxima'];
            if ($promedio_final >= $nota_minima && $promedio_final <= $nota_maxima) {
                $escala[$i]['contador'] = $escala[$i]['contador'] + 1;
            }
        }
    }
}

echo json_encode($escala);
// echo json_encode($promedios);
