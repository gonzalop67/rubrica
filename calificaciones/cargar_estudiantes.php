<?php
session_start();
header('Content-Type: text/html; charset=utf-8'); // Devolvemos HTML puro para el div responsive
require_once '../scripts/clases/class.mysql.php';
$db = new MySQL();

// 1. Capturar variables del POST
$id_paralelo        = isset($_POST['id_paralelo']) ? $db->filtrar($_POST['id_paralelo']) : 0;
$id_asignatura      = isset($_POST['id_asignatura']) ? $db->filtrar($_POST['id_asignatura']) : 0;
$id_tipo_asignatura = isset($_POST['id_tipo_asignatura']) ? (int)$_POST['id_tipo_asignatura'] : 1;
$id_periodo_lectivo = isset($_SESSION['id_periodo_lectivo']) ? (int)$_SESSION['id_periodo_lectivo'] : 0;

if ($id_periodo_lectivo === 0) {
    echo "<div class='alert alert-warning'><i class='icon fa fa-warning'></i> No se detectó un período lectivo activo.</div>";
    exit;
}

// 2. Cargar nómina de estudiantes y notas a la caché (Igual para ambos casos)
$sql_estudiantes = "SELECT e.id_estudiante, CONCAT(e.es_apellidos, ' ', e.es_nombres) AS nombre_completo
                      FROM sw_estudiante e
                      INNER JOIN sw_estudiante_periodo_lectivo ep ON ep.id_estudiante = e.id_estudiante 
                      WHERE ep.activo = 1
                        AND ep.id_paralelo = $id_paralelo 
                     ORDER BY e.es_apellidos ASC, e.es_nombres ASC";
$res_estudiantes = $db->consulta($sql_estudiantes);

if ($db->num_rows($res_estudiantes) == 0) {
    echo "false"; // JavaScript disparará el mensaje de "No hay alumnos matriculados"
    exit;
}

// Cargar la caché de notas en memoria
$cache_notas = [];

if ($id_tipo_asignatura === 1) {
    // 🔢 Cargar desde la tabla cuantitativa tradicional
    $sql_cache = "SELECT id_estudiante, id_rubrica_personalizada, re_calificacion AS nota 
                  FROM sw_rubrica_estudiante 
                  WHERE id_paralelo = $id_paralelo 
                  AND id_asignatura = $id_asignatura";
} else {
    // 🔤 Cargar desde tu OTRA tabla cualitativa oficial
    $sql_cache = "SELECT id_estudiante, id_rubrica_personalizada, rc_calificacion AS nota 
                  FROM sw_rubrica_cualitativa 
                  WHERE id_paralelo = $id_paralelo 
                  AND id_asignatura = $id_asignatura";
}

$res_cache = $db->consulta($sql_cache);
while ($r = $db->fetch_object($res_cache)) {
    // Ambas tablas se unifican bajo la misma llave de caché en memoria
    $cache_notas[$r->id_estudiante][$r->id_rubrica_personalizada] = $r->nota;
}

// 3. Enrutador Maestro: Decidir qué motor de funciones ejecutar
if ($id_tipo_asignatura === 1) {
    // 🔢 MOTOR CUANTITATIVO NUMÉRICO
    $estructura = armarEstructuraCuantitativa($db, $id_periodo_lectivo, $id_paralelo, $id_asignatura);
    generarTablaCuantitativa($db, $estructura, $res_estudiantes, $cache_notas, $id_periodo_lectivo, $id_paralelo, $id_asignatura);
} else {
    // 🔤 MOTOR CUALITATIVO DE LETRAS (Cívica, Comportamiento, etc.)
    $estructura = armarEstructuraCualitativa($db, $id_periodo_lectivo, $id_paralelo, $id_asignatura);
    generarTablaCualitativa($db, $estructura, $res_estudiantes, $cache_notas, $id_periodo_lectivo, $id_paralelo, $id_asignatura);
}

function armarEstructuraCualitativa($db, $id_periodo_lectivo, $id_paralelo, $id_asignatura)
{
    $estructura = [];
    $sql_periodo = "SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = {$id_periodo_lectivo} AND id_tipo_periodo = 1";
    $res_periodo = $db->consulta($sql_periodo);

    while ($p = $db->fetch_object($res_periodo)) {
        $periodo_item = ['id' => $p->id_periodo_evaluacion, 'nombre' => $p->pe_nombre, 'aportes' => [], 'total_columnas' => 0];

        // Solo trae aportes cualitativos que tengan al menos un insumo
        $sql_aporte = "SELECT a.id_aporte_evaluacion, a.ap_nombre, a.id_tipo_aporte, a.ap_ponderacion 
                       FROM sw_aporte_evaluacion a
                       WHERE a.id_periodo_evaluacion = {$p->id_periodo_evaluacion}
                       AND EXISTS (SELECT 1 FROM sw_rubrica_evaluacion r WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND r.id_tipo_asignatura != 1)";
        $res_aporte = $db->consulta($sql_aporte);

        while ($a = $db->fetch_object($res_aporte)) {
            // Consultar la pívot de cierres amarrada al paralelo
            $sql_cierre = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = {$a->id_aporte_evaluacion} AND id_paralelo = {$id_paralelo}";
            $res_cierre = $db->consulta($sql_cierre);
            $dat_cierre = $db->fetch_object($res_cierre);
            $estado_final = isset($dat_cierre->ap_estado) ? $dat_cierre->ap_estado : 'A';

            $aporte_item = ['id' => $a->id_aporte_evaluacion, 'nombre' => $a->ap_nombre, 'tipo' => $a->id_tipo_aporte, 'ponderacion' => (float)$a->ap_ponderacion, 'estado' => $estado_final, 'insumos' => []];

            $sql_rubrica = "SELECT id_rubrica_evaluacion, ru_nombre FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = {$a->id_aporte_evaluacion} AND id_tipo_asignatura != 1";
            $res_rubrica = $db->consulta($sql_rubrica);

            while ($r = $db->fetch_object($res_rubrica)) {
                $aporte_item['insumos'][] = ['id' => $r->id_rubrica_evaluacion, 'nombre' => $r->ru_nombre];
                $periodo_item['total_columnas']++;
            }

            // 🎯 REGLA DE OCULTAMIENTO: Solo sumamos la columna de Promedio si tiene MÁS de 1 insumo
            if ($a->id_tipo_aporte == 1 && count($aporte_item['insumos']) > 1) {
                $periodo_item['total_columnas']++;
            }
            $periodo_item['aportes'][] = $aporte_item;
        }

        if ($periodo_item['total_columnas'] == 0) {
            $periodo_item['total_columnas'] = 2; // Failsafe simétrico
        } else {
            $periodo_item['total_columnas']++; // +1 por la columna fija del Promedio Trimestral Cualitativo
        }
        $estructura[] = $periodo_item;
    }
    return $estructura;
}

function generarTablaCualitativa($db, $estructura, $res_estudiantes, $cache_notas, $id_periodo_lectivo, $id_paralelo, $id_asignatura)
{
?>
    <table class="table table-bordered table-striped table-hover text-center align-middle" style="margin-bottom: 0; font-size: 12px;">
        <thead>
            <!-- Fila Cabecera 1: Períodos (Trimestres Cualitativos) -->
            <tr style="background-color: #605ca8; color: #fff;">
                <th scope="col" rowspan="3" style="vertical-align: middle; width: 5%;">Nro.</th>
                <th scope="col" rowspan="3" style="vertical-align: middle; width: 25%; text-align: left;">Nómina de Estudiantes</th>
                <?php foreach ($estructura as $p): ?>
                    <th scope="col" colspan="<?php echo $p['total_columnas']; ?>" style="vertical-align: middle; letter-spacing: 0.5px;">
                        <b><?php echo strtoupper($p['nombre']); ?></b>
                    </th>
                <?php endforeach; ?>
                <th scope="col" rowspan="3" style="vertical-align: middle; background-color: #555299; width: 10%;">EVALUACIÓN FINAL</th>
            </tr>

            <!-- Fila Cabecera 2: Aportes (Parciales Cualitativos) -->
            <tr style="background-color: #f4f4f4; color: #333;">
                <?php foreach ($estructura as $p): ?>
                    <?php if (!empty($p['aportes'])):
                        foreach ($p['aportes'] as $a):
                            $total_insumos_aporte = isset($a['insumos']) ? count($a['insumos']) : 0;
                            $colspan_aporte = $total_insumos_aporte;

                            // 🎯 Solo sumamos 1 al colspan si es tipo 1 Y tiene más de un insumo configurado
                            if ($a['tipo'] == 1 && $total_insumos_aporte > 1) {
                                $colspan_aporte++;
                            }
                    ?>
                            <th colspan="<?php echo $colspan_aporte; ?>" style="font-size: 11px; vertical-align: middle;">
                                <?php echo strtoupper($a['nombre']); ?>
                            </th>
                        <?php endforeach;
                    else: ?>
                        <th style="font-size: 11px; vertical-align: middle; color:#999; font-style: italic;">Sin configurar</th>
                    <?php endif; ?>
                    <th rowspan="2" style="background-color: #e7e7e7; vertical-align: middle; width: 6%;"><b>PROM.</b></th>
                <?php endforeach; ?>
            </tr>

            <!-- Fila Cabecera 3: Insumos o Destrezas Individuales -->
            <tr style="background-color: #fafafa; color: #666; font-size: 10px;">
                <?php foreach ($estructura as $p): ?>
                    <?php if (!empty($p['aportes'])):
                        foreach ($p['aportes'] as $a):
                            foreach ($a['insumos'] as $insumo): ?>
                                <th title="<?php echo $insumo['nombre']; ?>" style="vertical-align: middle;">
                                    <?php echo strtoupper(mb_substr($insumo['nombre'], 0, 5, 'UTF-8')); ?>.
                                </th>
                            <?php endforeach;

                            // 🎯 Ocultar el título de Promedio Parcial si solo hay 1 insumo
                            if ($a['tipo'] == 1 && count($a['insumos']) > 1): ?>
                                <th style="background-color: #eaeaea; color: #333; vertical-align: middle;">P. PARCIAL</th>
                        <?php endif;
                        endforeach;
                    else: ?>
                        <th style="vertical-align: middle; color:#999;">-</th>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $contador_nro = 1;
            while ($estudiante = $db->fetch_object($res_estudiantes)):
                $id_est_actual = $estudiante->id_estudiante;
            ?>
                <tr class="row-alumno-sabana">
                    <!-- Nro Correlativo -->
                    <td style="font-weight: bold; background-color: #f9f9f9; vertical-align: middle;"><?php echo $contador_nro++; ?></td>

                    <!-- Datos Alumno -->
                    <td style="text-align: left; font-weight: 500; vertical-align: middle; white-space: nowrap;">
                        <span class="text-muted small" style="margin-right: 5px;">[<?php echo $id_est_actual; ?>]</span>
                        <b><?php echo $estudiante->nombre_completo; ?></b>
                    </td>

                    <?php
                    foreach ($estructura as $p):
                        foreach ($p['aportes'] as $a):
                            $estado_aporte = isset($a['estado']) ? strtoupper(trim($a['estado'])) : 'A';
                            $esta_cerrado  = ($estado_aporte === 'C');

                            $input_disabled  = $esta_cerrado ? 'disabled' : '';
                            $fondo_bloqueado = $esta_cerrado ? 'background-color: #eeeeee; cursor: not-allowed; color: #777 !important;' : 'background: transparent;';
                            $total_insumos = count($a['insumos']);

                            // Dibujar los selectores cualitativos para cada tarea/destreza
                            foreach ($a['insumos'] as $insumo):
                                $id_rub = $insumo['id'];
                                $nota_letra = isset($cache_notas[$id_est_actual][$id_rub]) ? trim($cache_notas[$id_est_actual][$id_rub]) : "-";

                                // Escala oficial de colores institucionales de Ecuador
                                $escala_ecuador = [
                                    'A+' => ['color' => '#2b542c'],
                                    'A-' => ['color' => '#3c763d'],
                                    'B+' => ['color' => '#245269'],
                                    'B-' => ['color' => '#31708f'],
                                    'C+' => ['color' => '#66512c'],
                                    'C-' => ['color' => '#8a6d3b'],
                                    'D+' => ['color' => '#a94442'],
                                    'D-' => ['color' => '#ce8483'],
                                    'E+' => ['color' => '#d9534f'],
                                    'E-' => ['color' => '#dd4b39']
                                ];
                                $color_letra_actual = isset($escala_ecuador[$nota_letra]) ? $escala_ecuador[$nota_letra]['color'] : '#333';

                                echo "<td style='padding: 0; vertical-align: middle; min-width: 65px; " . ($esta_cerrado ? "background-color: #eeeeee;" : "") . "'>";
                    ?>
                                <select class="excel-cell text-center"
                                    data-estudiante="<?php echo $id_est_actual; ?>"
                                    data-rubrica="<?php echo $id_rub; ?>"
                                    data-aporte="<?php echo $a['id']; ?>"
                                    data-periodo="<?php echo $p['id']; ?>"
                                    <?php echo $input_disabled; ?>
                                    style="width: 100%; height: 34px; border: none; padding: 2px; outline: none; <?php echo $fondo_bloqueado; ?> font-weight: bold; color: <?php echo $color_letra_actual; ?>; text-align-last: center;">
                                    <option value="-">-</option>

                                    <optgroup label="APRENDIZAJE ALCANZADO (DA)" style="color:#3c763d; font-style:normal;">
                                        <option value="A+" <?php echo $nota_letra == 'A+' ? 'selected' : ''; ?> style="color:#2b542c; font-weight:bold;">A+ (Alcanzado Superior)</option>
                                        <option value="A-" <?php echo $nota_letra == 'A-' ? 'selected' : ''; ?> style="color:#3c763d; font-weight:bold;">A- (Alcanzado Alto)</option>
                                    </optgroup>

                                    <optgroup label="APRENDIZAJE EN PROCESO (EP)" style="color:#31708f; font-style:normal;">
                                        <option value="B+" <?php echo $nota_letra == 'B+' ? 'selected' : ''; ?> style="color:#245269; font-weight:bold;">B+ (En Proceso Superior)</option>
                                        <option value="B-" <?php echo $nota_letra == 'B-' ? 'selected' : ''; ?> style="color:#31708f; font-weight:bold;">B- (En Proceso Alto)</option>
                                        <option value="C+" <?php echo $nota_letra == 'C+' ? 'selected' : ''; ?> style="color:#66512c; font-weight:bold;">C+ (En Proceso Medio)</option>
                                        <option value="C-" <?php echo $nota_letra == 'C-' ? 'selected' : ''; ?> style="color:#8a6d3b; font-weight:bold;">C- (En Proceso Básico)</option>
                                    </optgroup>

                                    <optgroup label="APRENDIZAJE INICIADO (EI)" style="color:#a94442; font-style:normal;">
                                        <option value="D+" <?php echo $nota_letra == 'D+' ? 'selected' : ''; ?> style="color:#a94442; font-weight:bold;">D+ (Iniciado Avanzado)</option>
                                        <option value="D-" <?php echo $nota_letra == 'D-' ? 'selected' : ''; ?> style="color:#ce8483; font-weight:bold;">D- (Iniciado Medio)</option>
                                        <option value="E+" <?php echo $nota_letra == 'E+' ? 'selected' : ''; ?> style="color:#d9534f; font-weight:bold;">E+ (Iniciado Básico)</option>
                                        <option value="E-" <?php echo $nota_letra == 'E-' ? 'selected' : ''; ?> style="color:#dd4b39; font-weight:bold;">E- (Intervención Urgente)</option>
                                    </optgroup>
                                </select>
                    <?php
                                echo "</td>";
                            endforeach; // Fin foreach insumos

                            // 🎯 CORRECCIÓN AQUÍ: Ocultar la celda de Promedio de Parcial si solo hay 1 insumo
                            if ($a['tipo'] == 1 && $total_insumos > 1):
                                echo "<td class='promedio-aporte-dinamico text-muted' style='vertical-align: middle; background-color:#f5f5f5;'><b>-</b></td>";
                            endif;

                        endforeach; // Fin foreach aportes

                        // Celda fija del Promedio del Periodo Trimestral Cualitativo (Mantiene la cuadrícula perfecta)
                        echo "<td class='promedio-periodo-dinamico text-muted' style='vertical-align: middle; background-color:#f5f5f5;'><b>-</b></td>";
                    endforeach; // Fin foreach periodos 
                    ?>

                    <!-- Columna Final: Evaluación Final Anual Cualitativa -->
                    <td style='vertical-align: middle; font-weight: bold; background-color: #f5f5f5; color:#777;'>-</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php
}

function armarEstructuraCuantitativa($db, $id_periodo_lectivo, $id_paralelo, $id_asignatura)
{
    $estructura = [];
    $sql_periodo = "SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = {$id_periodo_lectivo} AND id_tipo_periodo = 1";
    $res_periodo = $db->consulta($sql_periodo);

    while ($p = $db->fetch_object($res_periodo)) {
        $periodo_item = ['id' => $p->id_periodo_evaluacion, 'nombre' => $p->pe_nombre, 'aportes' => [], 'total_columnas' => 0];

        $sql_aporte = "SELECT a.id_aporte_evaluacion, a.ap_nombre, a.id_tipo_aporte, a.ap_ponderacion 
                       FROM sw_aporte_evaluacion a
                       WHERE a.id_periodo_evaluacion = {$p->id_periodo_evaluacion}
                       AND EXISTS (SELECT 1 FROM sw_rubrica_evaluacion r WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND r.id_tipo_asignatura = 1)";
        $res_aporte = $db->consulta($sql_aporte);

        while ($a = $db->fetch_object($res_aporte)) {
            $sql_cierre = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = {$a->id_aporte_evaluacion} AND id_paralelo = {$id_paralelo}";
            $res_cierre = $db->consulta($sql_cierre);
            $dat_cierre = $db->fetch_object($res_cierre);
            $estado_final = isset($dat_cierre->ap_estado) ? $dat_cierre->ap_estado : 'A';

            $aporte_item = ['id' => $a->id_aporte_evaluacion, 'nombre' => $a->ap_nombre, 'tipo' => $a->id_tipo_aporte, 'ponderacion' => (float)$a->ap_ponderacion, 'estado' => $estado_final, 'insumos' => []];

            $sql_rubrica = "SELECT id_rubrica_evaluacion, ru_nombre FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = {$a->id_aporte_evaluacion} AND id_tipo_asignatura = 1";
            $res_rubrica = $db->consulta($sql_rubrica);

            while ($r = $db->fetch_object($res_rubrica)) {
                $aporte_item['insumos'][] = ['id' => $r->id_rubrica_evaluacion, 'nombre' => $r->ru_nombre];
                $periodo_item['total_columnas']++;
            }

            if ($a->id_tipo_aporte == 1) {
                $periodo_item['total_columnas']++; // Promedio Parcial Fijo
            }
            $periodo_item['aportes'][] = $aporte_item;
        }

        if ($periodo_item['total_columnas'] == 0) {
            $periodo_item['total_columnas'] = 2;
        } else {
            $periodo_item['total_columnas']++; // Promedio Trimestral Fijo
        }
        $estructura[] = $periodo_item;
    }
    return $estructura;
}

function generarTablaCuantitativa($db, $estructura, $res_estudiantes, $cache_notas, $id_periodo_lectivo, $id_paralelo, $id_asignatura)
{
?>
    <table class="table table-bordered table-striped table-hover text-center align-middle" style="margin-bottom: 0; font-size: 12px;">
        <thead>
            <!-- Cabecera 1: Períodos -->
            <tr style="background-color: #3c8dbc; color: #fff;">
                <th rowspan="3" style="vertical-align: middle; width: 5%;">Nro.</th>
                <th rowspan="3" style="vertical-align: middle; width: 25%; text-align: left;">Nómina de Estudiantes (Numérica)</th>
                <?php foreach ($estructura as $p): ?>
                    <th colspan="<?php echo $p['total_columnas']; ?>" style="vertical-align: middle;"><b><?php echo strtoupper($p['nombre']); ?></b></th>
                <?php endforeach; ?>
                <th rowspan="3" style="vertical-align: middle; background-color: #337ab7; width: 8%;">PROMEDIO FINAL</th>
                <th rowspan="3" style="vertical-align: middle; background-color: #605ca8; width: 7%;">SUPLETORIO</th>
                <th rowspan="3" style="vertical-align: middle; background-color: #337ab7; width: 8%;">ESTADO</th>
            </tr>
            <!-- Cabecera 2: Parciales -->
            <tr style="background-color: #f4f4f4; color: #333;">
                <?php foreach ($estructura as $p): ?>
                    <?php if (!empty($p['aportes'])):
                        foreach ($p['aportes'] as $a):
                            $colspan = count($a['insumos']);
                            if ($a['tipo'] == 1) $colspan++;
                    ?>
                            <th colspan="<?php echo $colspan; ?>" style="font-size: 11px; vertical-align: middle;"><?php echo strtoupper($a['nombre']); ?></th>
                        <?php endforeach;
                    else: ?>
                        <th style="font-size: 11px; vertical-align: middle; color:#999;">Sin configurar</th>
                    <?php endif; ?>
                    <th rowspan="2" style="background-color: #e7e7e7; vertical-align: middle; width: 6%;"><b>PROM.</b></th>
                <?php endforeach; ?>
            </tr>
            <!-- Cabecera 3: Insumos -->
            <tr style="background-color: #fafafa; color: #666; font-size: 10px;">
                <?php foreach ($estructura as $p): ?>
                    <?php if (!empty($p['aportes'])):
                        foreach ($p['aportes'] as $a):
                            foreach ($a['insumos'] as $insumo): ?>
                                <th style="vertical-align: middle;"><?php echo strtoupper(mb_substr($insumo['nombre'], 0, 5, 'UTF-8')); ?>.</th>
                            <?php endforeach;
                            if ($a['tipo'] == 1): ?>
                                <th style="background-color: #eaeaea; color: #333; vertical-align: middle;">P. PARCIAL</th>
                        <?php endif;
                        endforeach;
                    else: ?>
                        <th style="vertical-align: middle; color:#999;">-</th>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php

            // =========================================================================
            // 🎯 CONTROL DE CONFIGURACIÓN GENERAL DEL EXAMEN SUPLETORIO (AL INICIO)
            // =========================================================================
            $id_aporte_evaluacion = 0;
            $supletorio_esta_cerrado = false;
            $configuracion_supletorio_existe = false;

            // Consultamos si existe el aporte de tipo 3 (Supletorio) para este año lectivo
            $qry_verificar = "SELECT a.id_aporte_evaluacion 
                  FROM sw_aporte_evaluacion a, sw_periodo_evaluacion p 
                  WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
                    AND a.id_tipo_aporte = 3 
                    AND p.id_periodo_lectivo = {$id_periodo_lectivo} 
                  LIMIT 1";

            $resultado_verificar = $db->consulta($qry_verificar);

            if ($resultado_verificar) {
                $result_verificar = $db->fetch_object($resultado_verificar);

                // 🛡️ Si la consulta trajo datos y el objeto existe, confirmamos que está configurado
                if ($result_verificar !== null && isset($result_verificar->id_aporte_evaluacion)) {
                    $id_aporte_evaluacion = (int)$result_verificar->id_aporte_evaluacion;
                    $configuracion_supletorio_existe = true; // 👈 ¡Aquí se activa la bandera!

                    // Ahora que sabemos que existe el aporte, obtenemos su estado de cierre real
                    $qry_cierre = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = {$id_aporte_evaluacion} AND id_paralelo = {$id_paralelo} LIMIT 1";
                    $resultado_cierre = $db->consulta($qry_cierre);

                    if ($resultado_cierre) {
                        $result_cierre = $db->fetch_object($resultado_cierre);

                        if ($result_cierre !== null && isset($result_cierre->ap_estado)) {
                            $estado = strtoupper(trim($result_cierre->ap_estado));
                            $supletorio_esta_cerrado = ($estado == "C"); // 👈 Aquí se define el cierre real
                        }
                    }
                }
            }

            $nro = 1;
            while ($est = $db->fetch_object($res_estudiantes)):
                $id_est = $est->id_estudiante;
                $suma_promedios_periodos = 0;
                $total_periodos_contados = 0;
            ?>
                <tr class="row-alumno-sabana">
                    <!-- Nro Correlativo -->
                    <td style="font-weight: bold; background-color: #f9f9f9; vertical-align: middle;"><?php echo $nro++; ?></td>

                    <!-- Datos Alumno -->
                    <td style="text-align: left; font-weight: 500; vertical-align: middle; white-space: nowrap;">
                        <span class="text-muted small" style="margin-right: 5px;">[<?php echo $id_est; ?>]</span> <b><?php echo $est->nombre_completo; ?></b>
                    </td>

                    <?php
                    // 🎯 CONTROL DE CONFIGURACIÓN: Inicializa en true solo si la estructura tiene datos reales
                    $todos_los_trimestres_cerrados = (!empty($estructura)) ? true : false;

                    // 🎯 NUEVO CONTROL: Inicializamos la bandera de calificaciones para el estudiante
                    $tiene_calificaciones = false;

                    // 1. RECORRER PERIODOS (Trimestres)
                    foreach ($estructura as $p):
                        $prom_ponderado_bim = 0;
                        $suma_pond_efectiva = 0;

                        // 🎯 NUEVO CONTROL: Asumimos inicialmente que todos los aportes de este trimestre están cerrados
                        $todos_los_aportes_cerrados = true;

                        // 2. RECORRER APORTES (Parciales de este Trimestre)
                        foreach ($p['aportes'] as $a):
                            $estado_aporte = isset($a['estado']) ? strtoupper(trim($a['estado'])) : 'A';
                            $esta_cerrado   = ($estado_aporte === 'C');
                            $disabled       = $esta_cerrado ? 'disabled' : '';
                            $fondo          = $esta_cerrado ? 'background-color: #eeeeee; cursor: not-allowed;' : 'background: transparent;';

                            // 🎯 Si encontramos TAN SOLO UN aporte abierto ('A'), apagamos las banderas de cierre
                            if (!$esta_cerrado) {
                                $todos_los_aportes_cerrados = false;
                                $todos_los_trimestres_cerrados = false; // Bloquea también el promedio final anual automáticamente
                            }

                            $suma_insumos = 0;
                            $cant_insumos = 0;

                            // 3. RECORRER INSUMOS/TAREAS DE ESTE PARCIAL
                            foreach ($a['insumos'] as $insumo):
                                $cant_insumos++;
                                $id_rub = $insumo['id'];
                                $nota_num = "-";

                                if (isset($cache_notas[$id_est][$id_rub])) {
                                    $nota_num = number_format((float)$cache_notas[$id_est][$id_rub], 2);
                                    $suma_insumos += (float)$cache_notas[$id_est][$id_rub];

                                    // 🎯 ¡SI ENCONTRAMOS AL MENOS UNA NOTA EN TODO EL AÑO, SÍ TIENE CALIFICACIONES!
                                    $tiene_calificaciones = true;
                                }

                                $input_color = ($nota_num !== "-" && (float)$nota_num < 7) ? 'color: #dd4b39; font-weight: bold;' : '';

                                echo "<td style='padding: 0; vertical-align: middle; min-width: 65px; " . ($esta_cerrado ? "background-color: #eeeeee;" : "") . "'>";
                                echo "<input type='text' class='excel-cell text-center' value='{$nota_num}' data-estudiante='{$id_est}' data-rubrica='{$id_rub}' data-aporte='{$a['id']}' data-periodo='{$p['id']}' maxlength='5' placeholder='-' {$disabled} style='width: 100%; height: 34px; border: none; padding: 5px; outline: none; {$fondo} {$input_color}'>";
                                echo "</td>";
                            endforeach;

                            // Calcular Promedio del Aporte (Parcial)
                            $prom_aporte = $cant_insumos > 0 ? truncarDosDecimales($suma_insumos / $cant_insumos) : 0;

                            // 🎯 CELDA DEL PROMEDIO PARCIAL (Dentro del bucle de aportes)
                            if ($a['tipo'] == 1):
                                $clase_prom = ($prom_aporte < 7) ? 'color: #dd4b39; background-color: #f2dede;' : 'color: #3c763d; background-color: #dff0d8;';
                                $texto_prom_aporte = ($prom_aporte == 0) ? "-" : number_format($prom_aporte, 2);

                                // Añadimos pointer-events: none y user-select: none para blindarla al 100%
                                echo "<td class='promedio-aporte-dinamico' data-estudiante='{$id_est}' data-aporte='{$a['id']}' 
                                        data-periodo='{$p['id']}' style='vertical-align: middle; font-weight: bold; pointer-events: none; user-select: none; cursor: default; {$clase_prom}'>
                                    <b>{$texto_prom_aporte}</b>
                                </td>";
                            endif;

                            $factor = ($a['ponderacion'] > 1) ? ($a['ponderacion'] / 100) : $a['ponderacion'];
                            $prom_ponderado_bim += ($prom_aporte * $factor);
                            $suma_pond_efectiva += $factor;

                        endforeach; // Cierre de Aportes

                        // =========================================================================
                        // PROCESAMIENTO CONDICIONADO DEL PROMEDIO TRIMESTRAL
                        // =========================================================================
                        $prom_periodo = $suma_pond_efectiva > 0 ? truncarDosDecimales($prom_ponderado_bim / $suma_pond_efectiva) : 0;

                        // 🎯 REGLA DE NEGOCIO SEGURO: Solo se muestra si TODOS los aportes del trimestre están cerrados
                        if ($todos_los_aportes_cerrados && $prom_periodo > 0) {
                            $clase_per = ($prom_periodo < 7) ? 'color: #dd4b39; font-weight: bold; background-color: #f2dede;' : 'color: #3c763d; font-weight: bold; background-color: #dff0d8;';
                            $texto_prom_periodo = number_format($prom_periodo, 2);

                            // Sumamos al acumulado anual únicamente si el trimestre está clausurado colectivamente
                            $suma_promedios_periodos += $prom_periodo;
                            $total_periodos_contados++;
                        } else {
                            // Formato gris oculto si hay algún parcial abierto o en edición activa
                            $clase_per = 'background-color: #f5f5f5; color: #999;';
                            $texto_prom_periodo = "-";
                        }

                        // 2. 🎯 CELDA DEL PROMEDIO TRIMESTRAL (Al final del bucle de aportes)
                        $texto_prom_periodo = ($prom_periodo == 0) ? "-" : number_format($prom_periodo, 2);
                        echo "<td class='promedio-periodo-dinamico' data-estudiante='{$id_est}' data-periodo='{$p['id']}' 
                                style='vertical-align: middle; font-weight: bold; pointer-events: none; user-select: none; cursor: default; {$clase_per}'>
                                <b>{$texto_prom_periodo}</b>
                            </td>";

                    endforeach; // Cierre de Periodos

                    // =========================================================================
                    // 🎯 PARTE 1: PROMEDIO FINAL ANUAL CONDICIONADO CON FILTRO DE DESERTOR
                    // =========================================================================
                    $texto_prom_final = "-";
                    $estilo_f = 'background-color: #f5f5f5; color: #999;'; // Por defecto gris si está abierto
                    $prom_final_calculado = null; // Control limpio para evitar errores de lógica

                    // Solo calculamos promedios si tiene calificaciones, todo está cerrado y hay periodos reales
                    if ($tiene_calificaciones && $todos_los_trimestres_cerrados && $total_periodos_contados > 0 && !empty($estructura)) {
                        $prom_final_calculado = truncarDosDecimales($suma_promedios_periodos / $total_periodos_contados);
                        $texto_prom_final = number_format($prom_final_calculado, 2);

                        // Aplicación de alertas semánticas pasteles según el promedio original
                        if ($prom_final_calculado > 0 && $prom_final_calculado <= 4) {
                            $estilo_f = 'color: #dd4b39; background-color: #f2dede;'; // Reprobado directo (Rojo)
                        } elseif ($prom_final_calculado > 4 && $prom_final_calculado < 7) {
                            $estilo_f = 'color: #8a6d3b; background-color: #fcf8e3;'; // En Supletorio (Amarillo)
                        } else {
                            $estilo_f = 'color: #3c763d; background-color: #dff0d8;'; // Aprobado directo (Verde)
                        }
                    }

                    // =========================================================================
                    // 🎯 PARTE 2: EXAMEN SUPLETORIO NUMÉRICO INTERACTIVO (ESTRUCTURA INTEGRADA TOTAL)
                    // =========================================================================
                    $sup_valor = "-";
                    $sup_disabled = "disabled"; // Por defecto bloqueado
                    $fondo_sup = "background-color: #f5f5f5; color: #999; cursor: not-allowed;";
                    $input_color_sup = "";
                    $nota_supletorio_num = 0;

                    // 1. CONDICIÓN DE APERTURA (EDICIÓN ACTIVA)
                    // Se desbloquea solo si: tiene notas, la rúbrica existe, el año cerró, está en rango Y EL ACTA ESTÁ ABIERTA
                    if ($tiene_calificaciones && $configuracion_supletorio_existe && $todos_los_trimestres_cerrados && $prom_final_calculado !== null && $prom_final_calculado < 7 && $prom_final_calculado >= 4 && !$supletorio_esta_cerrado) {

                        $sup_disabled = ""; // ¡Se desbloquea la celda para edición!
                        $fondo_sup = "background: transparent; cursor: text;";

                        if ($id_periodo_lectivo > 0 && $id_est > 0 && $id_paralelo > 0 && $id_asignatura > 0) {
                            $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_est, $id_paralelo, $id_asignatura, 2) AS supletorio";
                            $res_supletorio = mysqli_query($db->conexion, $qry);

                            if ($res_supletorio) {
                                $calif = mysqli_fetch_assoc($res_supletorio);
                                if (isset($calif["supletorio"]) && $calif["supletorio"] > 0) {
                                    $nota_supletorio_num = (float)$calif["supletorio"];
                                    $sup_valor = number_format($nota_supletorio_num, 2);

                                    if ($nota_supletorio_num < 7) {
                                        $input_color_sup = "color: #dd4b39; font-weight: bold;";
                                        $fondo_sup = "background-color: #f2dede; cursor: text;";
                                    } else {
                                        $input_color_sup = "color: #3c763d; font-weight: bold;";
                                        $fondo_sup = "background-color: #dff0d8; cursor: text;";

                                        $texto_prom_final = "7.00";
                                        $estilo_f = 'color: #3c763d; background-color: #dff0d8; font-weight: bold;';
                                    }
                                }
                            }
                        }
                    }
                    // 2. CONDICIÓN DE LECTURA PROTEGIDA (ACTA CERRADA)
                    // Se ejecuta la consulta fija SOLO si el alumno ameritaba supletorio Y el acta está efectivamente cerrada
                    elseif ($tiene_calificaciones && $configuracion_supletorio_existe && $todos_los_trimestres_cerrados && $prom_final_calculado !== null && $prom_final_calculado < 7 && $prom_final_calculado >= 4 && $supletorio_esta_cerrado) {

                        $sup_disabled = "disabled"; // Forzar bloqueo
                        $fondo_sup = "background-color: #eeeeee; color: #999; cursor: not-allowed;";

                        if ($id_periodo_lectivo > 0 && $id_est > 0 && $id_paralelo > 0 && $id_asignatura > 0) {
                            $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_est, $id_paralelo, $id_asignatura, 2) AS supletorio";
                            $res_supletorio = mysqli_query($db->conexion, $qry);

                            if ($res_supletorio) {
                                $calif = mysqli_fetch_assoc($res_supletorio);
                                if (isset($calif["supletorio"]) && $calif["supletorio"] > 0) {
                                    $nota_supletorio_num = (float)$calif["supletorio"];
                                    $sup_valor = number_format($nota_supletorio_num, 2);

                                    if ($nota_supletorio_num < 7) {
                                        $input_color_sup = "color: #dd4b39; font-weight: bold;";
                                        $texto_prom_final = number_format($prom_final_calculado, 2); // Mantiene nota original baja
                                    } else {
                                        $input_color_sup = "color: #3c763d; font-weight: bold;";
                                        $texto_prom_final = "7.00"; // Fuerza el 7.00 de aprobación definitiva
                                        $estilo_f = 'color: #3c763d; background-color: #dff0d8; font-weight: bold;';
                                    }
                                }
                            }
                        }
                    }
                    // 3. CASO GENERAL / INACTIVIDAD SEGURA
                    // Desertores, alumnos ya aprobados directo, reprobados directos o si no hay configuración en la BD
                    else {
                        $sup_disabled = "disabled";
                        $fondo_sup = "background-color: #eeeeee; color: #999; cursor: not-allowed;";
                        $sup_valor = "-"; // Guion limpio sin consultas pesadas a la BD
                    }

                    // =========================================================================
                    // 🎯 PARTE 3: RENDERIZADO HTML DE LAS CELDAS DE PROMEDIO FINAL Y SUPLETORIO
                    // =========================================================================

                    // Renderizamos la celda de PROMEDIO FINAL (actualizada según los bloques anteriores)
                    echo "<td class='promedio-final-dinamico' data-estudiante='{$id_est}' style='font-weight: bold; vertical-align: 
                            middle; font-size: 13px; pointer-events: none; user-select: none; cursor: default; {$estilo_f}'>
                            <b>{$texto_prom_final}</b>
                        </td>";

                    // Renderizamos la celda del INPUT SUPLETORIO (Bloqueado con disabled si cumple las restricciones)
                    $p_holder = isset($placeholder_sup) ? $placeholder_sup : '-';
                    echo "<td style='padding: 0; vertical-align: middle; min-width: 65px; " . ($sup_disabled ? "background-color: #eeeeee;" : "") . "'>";
                    echo "<input type='text' 
                             class='excel-cell text-center' 
                             value='{$sup_valor}' 
                             data-estudiante='{$id_est}' 
                             data-rubrica='SUPLETORIO' 
                             data-aporte='0' 
                             data-periodo='0' 
                             maxlength='5' 
                             placeholder='{$p_holder}' 
                             {$sup_disabled} 
                             style='width: 100%; height: 34px; border: none; padding: 5px; outline: none; {$fondo_sup} {$input_color_sup}'>";
                    echo "</td>";

                    // =========================================================================
                    // 🎯 PARTE 4: REGLA DE NEGOCIO INTEGRADA PARA LA COLUMNA "ESTADO" (CORREGIDA)
                    // =========================================================================
                    $texto_estado = "-";
                    $estilo_estado = "background-color: #f5f5f5; color: #999;";

                    if ($todos_los_trimestres_cerrados) {

                        // 1. PRIMERA REGLA ABSOLUTA: Si no tiene calificaciones, es DESERTOR
                        if (!$tiene_calificaciones) {
                            $texto_estado = "DESERTOR";
                            $estilo_estado = "background-color: #e0e0e0; color: #666666; font-weight: bold;"; // Gris neutro
                        }
                        // 2. Solo si SÍ tiene calificaciones, evaluamos su rendimiento académico
                        elseif ($prom_final_calculado !== null && $prom_final_calculado > 0) {

                            // Caso Supletorio
                            if ($prom_final_calculado > 4 && $prom_final_calculado < 7) {
                                if (!$configuracion_supletorio_existe) {
                                    $texto_estado = "SUPLETORIO";
                                    $estilo_estado = "background-color: #fcf8e3; color: #8a6d3b; font-weight: bold;";
                                } elseif ($nota_supletorio_num >= 7) {
                                    $texto_estado = "APROBADO";
                                    $estilo_estado = "background-color: #dff0d8; color: #3c763d; font-weight: bold;";
                                } else {
                                    $texto_estado = "REPITENCIA";
                                    $estilo_estado = "background-color: #f2dede; color: #dd4b39; font-weight: bold;";
                                }
                            }
                            // Caso Aprobado Directo
                            elseif ($prom_final_calculado >= 7) {
                                $texto_estado = "APROBADO";
                                $estilo_estado = "background-color: #dff0d8; color: #3c763d; font-weight: bold;";
                            }
                            // Caso Repitencia Directa (Notas mayores a 0 pero menores o iguales a 4)
                            elseif ($prom_final_calculado <= 4) {
                                $texto_estado = "REPITENCIA";
                                $estilo_estado = "background-color: #f2dede; color: #dd4b39; font-weight: bold;";
                            }
                        }
                        // 3. Si tiene el flag activo pero el promedio final inexplicablemente dio 0
                        else {
                            $texto_estado = "REPITENCIA";
                            $estilo_estado = "background-color: #f2dede; color: #dd4b39; font-weight: bold;";
                        }
                    }

                    // Renderizamos la celda final de ESTADO
                    echo "<td class='estado-alumno-dinamico' data-estudiante='{$id_est}' style='vertical-align: middle; font-size: 11px; pointer-events: none; user-select: none; cursor: default; {$estilo_estado}'>";
                    echo "<b>{$texto_estado}</b>";
                    echo "</td>";

                    ?>
                </tr>
            <?php
            endwhile; // Fin del bucle principal 'while' de los estudiantes
            ?>
        </tbody>
    </table>
<?php
} // Fin definitivo de la función generarTablaCuantitativa

/**
 * 🎯 FUNCIÓN MAESTRA DE TRUNCADO EN PHP
 * Corta un número estrictamente a dos decimales sin redondear, 
 * tal como lo requiere tu sistema escolar.
 */
function truncarDosDecimales($numero)
{
    $factor = pow(10, 2);
    return (($numero * $factor) >= 0) ? (floor($numero * $factor) / $factor) : (ceil($numero * $factor) / $factor);
}
