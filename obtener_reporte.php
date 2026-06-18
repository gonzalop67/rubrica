<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function truncarDosDecimales($valor)
{
    $float = (float)$valor;
    if ($float == 0) return 0;

    // Sumamos una millonésima (0.000001) para corregir el desfase binario de PHP (ej: 7.09999999 -> 7.100000)
    $comprobacion = $float + 0.000001;

    // Desplazamos la coma dos posiciones, truncamos el resto con floor y regresamos la coma
    return floor($comprobacion * 100) / 100;
}

if (!isset($_POST['id_paralelo']) || empty($_POST['id_paralelo']) || !isset($_POST['id_asignatura']) || empty($_POST['id_asignatura'])) {
    echo "<p style='color: #dd4b39;'>Acceso directo no permitido o datos incompletos.</p>";
    exit;
}

require_once("scripts/clases/class.mysql.php");
$db = new MySQL;

$id_paralelo = (int)$_POST['id_paralelo'];
$id_asignatura = (int)$_POST['id_asignatura'];
$id_periodo_lectivo = (int)$_POST['id_periodo_lectivo'];

// 1. Armar la estructura dinámica de periodos
$estructura = [];
$sql_periodo = "SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = {$id_periodo_lectivo} AND id_tipo_periodo = 1";
$res_periodo = $db->consulta($sql_periodo);

while ($p = $db->fetch_object($res_periodo)) {
    $periodo_item = [
        'id' => $p->id_periodo_evaluacion,
        'nombre' => $p->pe_nombre,
        'aportes' => [],
        'total_columnas' => 0
    ];

    $sql_aporte = "SELECT id_aporte_evaluacion, ap_nombre, id_tipo_aporte, ap_ponderacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = {$p->id_periodo_evaluacion}";
    $res_aporte = $db->consulta($sql_aporte);

    while ($a = $db->fetch_object($res_aporte)) {
        $aporte_item = [
            'id' => $a->id_aporte_evaluacion,
            'nombre' => $a->ap_nombre,
            'tipo' => $a->id_tipo_aporte,
            'ponderacion' => (float)$a->ap_ponderacion,
            'insumos' => []
        ];

        $sql_rubrica = "SELECT id_rubrica_evaluacion, ru_nombre 
                          FROM sw_rubrica_evaluacion 
                         WHERE id_aporte_evaluacion = {$a->id_aporte_evaluacion} 
                           AND id_tipo_asignatura = 1";
        $res_rubrica = $db->consulta($sql_rubrica);

        while ($r = $db->fetch_object($res_rubrica)) {
            $aporte_item['insumos'][] = [
                'id' => $r->id_rubrica_evaluacion,
                'nombre' => $r->ru_nombre
            ];
            $periodo_item['total_columnas']++;
        }

        if ($a->id_tipo_aporte == 1) {
            $periodo_item['total_columnas']++;
        }

        $periodo_item['aportes'][] = $aporte_item;
    }

    $periodo_item['total_columnas']++;
    $estructura[] = $periodo_item;
}

// 2. Obtener la nómina de estudiantes activos
$sql_estudiantes = "SELECT e.id_estudiante, CONCAT(e.es_apellidos, ' ', e.es_nombres) AS nombre_completo
                      FROM sw_estudiante e
                      INNER JOIN sw_estudiante_periodo_lectivo ep ON ep.id_estudiante = e.id_estudiante 
                      WHERE ep.activo = 1
                        AND ep.id_paralelo = $id_paralelo 
                     ORDER BY e.es_apellidos ASC, e.es_nombres ASC";
$res_estudiantes = $db->consulta($sql_estudiantes);

if ($db->num_rows($res_estudiantes) == 0) {
    echo "<p style='color: #666;'>No existen estudiantes matriculados o activos en este paralelo.</p>";
    exit;
}
?>

<table>
    <thead>
        <tr class="fila-cabecera-1">
            <th scope="col" rowspan="3" class="col-fija-1">Nro.</th>
            <th scope="col" rowspan="3" class="col-fija-2">Nómina</th>
            <?php foreach ($estructura as $p): ?>
                <th scope="col" colspan="<?php echo $p['total_columnas']; ?>"><?php echo strtoupper($p['nombre']); ?></th>
            <?php endforeach; ?>
            <th scope="col" rowspan="3">PROMEDIO FINAL</th>
            <th scope="col" rowspan="3">SUPLETORIO</th>
        </tr>
        <tr class="fila-cabecera-2">
            <?php foreach ($estructura as $p): ?>
                <?php foreach ($p['aportes'] as $a): ?>
                    <?php
                    $colspan_aporte = count($a['insumos']);
                    if ($a['tipo'] == 1) $colspan_aporte++;
                    ?>
                    <th colspan="<?php echo $colspan_aporte; ?>"><?php echo strtoupper($a['nombre']); ?></th>
                <?php endforeach; ?>
                <th rowspan="2">PROMEDIO</th>
            <?php endforeach; ?>
        </tr>
        <tr class="fila-cabecera-3">
            <?php foreach ($estructura as $p): ?>
                <?php foreach ($p['aportes'] as $a): ?>
                    <?php foreach ($a['insumos'] as $insumo): ?>
                        <th><?php echo strtoupper($insumo['nombre']); ?></th>
                    <?php endforeach; ?>
                    <?php if ($a['tipo'] == 1): ?>
                        <th>PROM.</th>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tr>
    </thead>

    <tbody>
        <?php
        $contador_nro = 1;
        while ($estudiante = $db->fetch_object($res_estudiantes)):
            $id_est_actual = $estudiante->id_estudiante;
            $suma_promedios_periodos = 0;
            $total_periodos_contados = 0;
        ?>
            <tr>
                <td class="col-fija-1"><?php echo $contador_nro++; ?></td>
                <th scope="row" class="col-fija-2" style="text-align: left;"><?php echo $estudiante->nombre_completo; ?></th>

                <?php
                foreach ($estructura as $p):
                    $promedio_ponderado_bimestre = 0;
                    $suma_ponderaciones_efectivas = 0;

                    foreach ($p['aportes'] as $a):
                        $suma_insumos_aporte = 0;
                        $cantidad_insumos_aporte = 0;

                        foreach ($a['insumos'] as $insumo):
                            // El divisor (cantidad de insumos esperados) siempre sumará, haya o no nota
                            $cantidad_insumos_aporte++;

                            $sql_nota = "SELECT re_calificacion 
                                           FROM sw_rubrica_estudiante 
                                          WHERE id_estudiante = $id_est_actual 
                                            AND id_paralelo = $id_paralelo 
                                            AND id_asignatura = $id_asignatura 
                                            AND id_rubrica_personalizada = {$insumo['id']} 
                                          LIMIT 1";

                            $res_nota = $db->consulta($sql_nota);

                            if ($db->num_rows($res_nota) > 0) {
                                $data_nota = $db->fetch_object($res_nota);
                                $nota = (float)$data_nota->re_calificacion;
                                $texto_nota = number_format($nota, 2);
                                $clase_rojo = ($nota < 7) ? 'style="color: #dd4b39; font-weight: bold;"' : '';

                                $suma_insumos_aporte += $nota;
                            } else {
                                // CORRECCIÓN: Visualmente se puede mostrar vacío o con 0.00, pero matemáticamente suma 0
                                $texto_nota = "-";
                                $clase_rojo = 'style="color: #dd4b39; font-weight: bold;"';
                                $suma_insumos_aporte += 0.00;
                            }

                            echo "<td $clase_rojo>$texto_nota</td>";
                        endforeach;

                        // Promedio del Aporte (Parcial) tomando en cuenta los casilleros vacíos
                        $promedio_aporte = 0;
                        if ($cantidad_insumos_aporte > 0) {
                            // Truncamos el resultado de la división directamente
                            $promedio_aporte = truncarDosDecimales($suma_insumos_aporte / $cantidad_insumos_aporte);
                        }

                        if ($a['tipo'] == 1):
                            $clase_prom_aporte = ($promedio_aporte < 7) ? 'style="color: #dd4b39;"' : '';
                            // Formateamos visualmente a 2 decimales para la tabla
                            $promedio_texto_aportes = ($promedio_aporte == 0) ? "-" : number_format($promedio_aporte, 2);
                            echo "<td $clase_prom_aporte><b>" . $promedio_texto_aportes . "</b></td>";
                        endif;

                        // Acumulación Ponderada Obligatoria del Aporte
                        $factor_ponderacion = ($a['ponderacion'] > 1) ? ($a['ponderacion'] / 100) : $a['ponderacion'];
                        $promedio_ponderado_bimestre += ($promedio_aporte * $factor_ponderacion);
                        $suma_ponderaciones_efectivas += $factor_ponderacion;
                    endforeach;

                    // Promedio final del periodo (Quimestre o Trimestre)
                    $promedio_periodo = 0;
                    if ($suma_ponderaciones_efectivas > 0) {
                        // Truncamos el cálculo ponderado acumulado
                        $promedio_periodo = truncarDosDecimales($promedio_ponderado_bimestre / $suma_ponderaciones_efectivas);
                    }

                    $clase_prom_periodo = ($promedio_periodo < 7) ? 'style="color: #dd4b39; font-weight: bold; background-color: #f9f9f9;"' : 'style="font-weight: bold; background-color: #f9f9f9;"';
                    $texto_prom_periodo = ($promedio_periodo == 0) ? "-" : number_format($promedio_periodo, 2);
                    echo "<td $clase_prom_periodo>$texto_prom_periodo</td>";

                    $suma_promedios_periodos += $promedio_periodo;
                    $total_periodos_contados++;
                endforeach;

                // Promedio Final General
                $promedio_final = 0;
                if ($total_periodos_contados > 0) {
                    $promedio_final = truncarDosDecimales($suma_promedios_periodos / $total_periodos_contados);
                }

                $clase_final = '';

                if ($promedio_final >= 0 && $promedio_final <= 4) {
                    // Rango 0 a 4: No aprueba (Rojo)
                    $clase_final = 'style="color: #ffffff; background-color: #dd4b39;"';
                } elseif ($promedio_final > 4 && $promedio_final < 7) {
                    // Mayor que 4 y menor que 7: Supletorio (Naranja)
                    $clase_final = 'style="color: #ffffff; background-color: #ff9800;"';
                } elseif ($promedio_final >= 7) {
                    // Mayor o igual que 7: Aprobado (Verde)
                    $clase_final = 'style="color: #ffffff; background-color: #4caf50;"';
                }

                $promedio_final == 0 ? $promedio_final_texto = "-" : $promedio_final_texto = (string)$promedio_final;
                ?>
                <td <?php echo $clase_final; ?>><b><?php echo $promedio_final_texto; ?></b></td>
                <?php
                // Obtener la calificacion del examen supletorio
                $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_est_actual, $id_paralelo, $id_asignatura, 2) AS supletorio";
                $resultado = $db->consulta($qry);
                $calificacion = $db->fetch_assoc($resultado);
                $supletorio = $calificacion["supletorio"];

                $supletorio = ($supletorio == 0) ? "" : $supletorio;

                $clase_rojo = ($supletorio < 7) ? 'style="color: #dd4b39; font-weight: bold;"' : '';
                echo "<td $clase_rojo>$supletorio</td>";
                ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>