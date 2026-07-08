<?php
// 1. Forzar visualización estricta de errores por si falta la dependencia de Composer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función de truncamiento idéntica a tu reporte visual
function truncarDosDecimales(string $valor)
{
    $float = (float)$valor;
    if ($float == 0) return 0;
    $comprobacion = $float + 0.000001;
    return floor($comprobacion * 100) / 100;
}

require_once "../vendor/autoload.php";
require_once "../scripts/clases/class.mysql.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$db = new MySQL;

// Adaptación de $_POST (JS) a $_GET para la descarga nativa por URL
$id_paralelo = isset($_GET['id_paralelo']) ? (int)$_GET['id_paralelo'] : 0;
$id_asignatura = isset($_GET['id_asignatura']) ? (int)$_GET['id_asignatura'] : 0;
$id_periodo_lectivo = isset($_GET['id_periodo_lectivo']) ? (int)$_GET['id_periodo_lectivo'] : 0;

if ($id_paralelo === 0 || $id_asignatura === 0 || $id_periodo_lectivo === 0) {
    die("Acceso directo no permitido o datos incompletos.");
}

// ==========================================
// 1. ARMAR LA ESTRUCTURA DINÁMICA DE PERIODOS
// ==========================================
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

// 2. OBTENER LA NÓMINA DE ESTUDIANTES ACTIVOS
$sql_estudiantes = "SELECT e.id_estudiante, CONCAT(e.es_apellidos, ' ', e.es_nombres) AS nombre_completo
                      FROM sw_estudiante e
                      INNER JOIN sw_estudiante_periodo_lectivo ep ON ep.id_estudiante = e.id_estudiante 
                      WHERE ep.activo = 1
                        AND ep.id_paralelo = $id_paralelo 
                     ORDER BY e.es_apellidos ASC, e.es_nombres ASC";
$res_estudiantes = $db->consulta($sql_estudiantes);

if ($db->num_rows($res_estudiantes) == 0) {
    die("No existen estudiantes matriculados o activos en este paralelo.");
}

// INICIALIZAR HOJA DE EXCEL
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Calificaciones');

// ESTILOS BASE (Mapeando tu CSS original)
$estiloGlobalCabecera = [
    'font' => ['bold' => true, 'size' => 9, 'name' => 'Arial'],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]
];

$estiloCeldaNormal = [
    'font' => ['size' => 9, 'name' => 'Arial'],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]
];

$estiloNotaReprobada = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'DD4B39']] // Clase .rojo
];

// ==============================================
// 3. CONSTRUCCIÓN DE CABECERAS TRIPLES DINÁMICAS
// ==============================================
$sheet->setCellValue('A1', 'Nro.')->mergeCells('A1:A3');
$sheet->setCellValue('B1', 'Nómina')->mergeCells('B1:B3');

$columnaActualNum = 3; // Empezamos en la columna C (Índice 3)

foreach ($estructura as $p) {
    $colInicioBimestre = $columnaActualNum;
    $colFinBimestre = $colInicioBimestre + $p['total_columnas'] - 1;

    // FILA 1: Bimestre/Quimestre
    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colInicioBimestre) . '1', strtoupper($p['nombre']));
    $sheet->mergeCells(
        Coordinate::stringFromColumnIndex($colInicioBimestre) . '1:' .
            Coordinate::stringFromColumnIndex($colFinBimestre) . '1'
    );

    foreach ($p['aportes'] as $a) {
        $colInicioAporte = $columnaActualNum;
        $colspan_aporte = count($a['insumos']);
        if ($a['tipo'] == 1) $colspan_aporte++;
        $colFinAporte = $colInicioAporte + $colspan_aporte - 1;

        // FILA 2: Bloque del Aporte
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colInicioAporte) . '2', strtoupper($a['nombre']));
        $sheet->mergeCells(
            Coordinate::stringFromColumnIndex($colInicioAporte) . '2:' .
                Coordinate::stringFromColumnIndex($colFinAporte) . '2'
        );

        // FILA 3: Insumos individuales
        foreach ($a['insumos'] as $insumo) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnaActualNum) . '3', strtoupper($insumo['nombre']));
            $columnaActualNum++;
        }

        if ($a['tipo'] == 1) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnaActualNum) . '3', 'PROM.');
            $columnaActualNum++;
        }
    }

    // Columna final del Periodo: PROMEDIO BIMESTRAL
    $columnaPromedio = Coordinate::stringFromColumnIndex($columnaActualNum);
    $sheet->setCellValue($columnaPromedio . '2', 'PROMEDIO');
    $sheet->mergeCells($columnaPromedio . '2:' . $columnaPromedio . '3');
    $columnaActualNum++;
}

// Columnas de Cierre Finales (Merge con las 3 filas)
$colPromedioFinal = $columnaActualNum;
// Usar setCellValue con coordenadas en vez de setCellValueByColumnAndRow
$colPromedioFinalLetter = Coordinate::stringFromColumnIndex($colPromedioFinal);
$sheet->setCellValue($colPromedioFinalLetter . '1', 'PROMEDIO FINAL');
$sheet->mergeCells($colPromedioFinalLetter . '1:' . $colPromedioFinalLetter . '3');

$colSupletorio = $columnaActualNum + 1;
$colSupletorioLetter = Coordinate::stringFromColumnIndex($colSupletorio);
$sheet->setCellValue($colSupletorioLetter . '1', 'SUPLETORIO');
$sheet->mergeCells($colSupletorioLetter . '1:' . $colSupletorioLetter . '3');

// Aplicar estilos a toda la cabecera calculada (Filas 1 a 3)
$ultimaColumnaLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colSupletorio);
$sheet->getStyle('A1:' . $ultimaColumnaLetra . '3')->applyFromArray($estiloGlobalCabecera);

// ==========================================
// 4. PROCESAMIENTO MATRICIAL DE LAS CALIFICACIONES
// ==========================================
$filaExcelActual = 4; // Los datos del bucle arrancan estrictamente en la fila 4
$contador_nro = 1;

// Estilos de alerta para el promedio final (Mapeo de tus colores Bootstrap/CSS)
$estiloAprobado = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']] // Verde
];
$estiloSupletorio = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF9800']] // Naranja
];
$estiloReprobadoFinal = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DD4B39']] // Rojo
];

while ($estudiante = $db->fetch_object($res_estudiantes)) {
    $id_est_actual = $estudiante->id_estudiante;
    $columnaDataNum = 3; // El cursor de celdas numéricas vuelve a la columna C por estudiante
    
    $sheet->setCellValue('A' . $filaExcelActual, $contador_nro++);
    $sheet->setCellValue('B' . $filaExcelActual, $estudiante->nombre_completo);

    $suma_promedios_periodos = 0;
    $total_periodos_contados = 0;

    foreach ($estructura as $p) {
        $promedio_ponderado_bimestre = 0;
        $suma_ponderaciones_efectivas = 0;

        foreach ($p['aportes'] as $a) {
            $suma_insumos_aporte = 0;
            $cantidad_insumos_aporte = 0;

            foreach ($a['insumos'] as $insumo) {
                $cantidad_insumos_aporte++;

                $sql_nota = "SELECT re_calificacion FROM sw_rubrica_estudiante 
                              WHERE id_estudiante = $id_est_actual AND id_paralelo = $id_paralelo 
                                AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = {$insumo['id']} LIMIT 1";
                $res_nota = $db->consulta($sql_nota);
                $celdaActual = Coordinate::stringFromColumnIndex($columnaDataNum) . $filaExcelActual;

                if ($db->num_rows($res_nota) > 0) {
                    $data_nota = $db->fetch_object($res_nota);
                    $nota = (float)$data_nota->re_calificacion;

                    $sheet->setCellValue($celdaActual, number_format($nota, 2));
                    if ($nota < 7) {
                        $sheet->getStyle($celdaActual)->applyFromArray($estiloNotaReprobada);
                    }
                    $suma_insumos_aporte += $nota;
                } else {
                    $sheet->setCellValue($celdaActual, "-");
                    $sheet->getStyle($celdaActual)->applyFromArray($estiloNotaReprobada);
                    $suma_insumos_aporte += 0.00;
                }
                $columnaDataNum++;
            }

            $promedio_aporte = 0;
            if ($cantidad_insumos_aporte > 0) {
                $promedio_aporte = truncarDosDecimales($suma_insumos_aporte / $cantidad_insumos_aporte);
            }

            if ($a['tipo'] == 1) {
                $promedio_texto_aportes = ($promedio_aporte == 0) ? "-" : number_format($promedio_aporte, 2);
                $celdaActual = Coordinate::stringFromColumnIndex($columnaDataNum) . $filaExcelActual;
                $sheet->setCellValue($celdaActual, $promedio_texto_aportes);
                if ($promedio_aporte < 7) {
                    $sheet->getStyle($celdaActual)->applyFromArray($estiloNotaReprobada);
                }
                $columnaDataNum++;
            }

            // Sincronización del factor de ponderación exacto de tu reporte
            $factor_ponderacion = ($a['ponderacion'] > 1) ? ($a['ponderacion'] / 100) : $a['ponderacion'];
            $promedio_ponderado_bimestre += ($promedio_aporte * $factor_ponderacion);
            $suma_ponderaciones_efectivas += $factor_ponderacion;
        }

        // Promedio final del periodo (Quimestre o Trimestre) ajustado con ponderaciones efectivas
        $promedio_periodo = 0;
        if ($suma_ponderaciones_efectivas > 0) {
            $promedio_periodo = truncarDosDecimales($promedio_ponderado_bimestre / $suma_ponderaciones_efectivas);
        }

        $texto_prom_periodo = ($promedio_periodo == 0) ? "-" : number_format($promedio_periodo, 2);
        $celdaActual = Coordinate::stringFromColumnIndex($columnaDataNum) . $filaExcelActual;
        $sheet->setCellValue($celdaActual, $texto_prom_periodo);
        if ($promedio_periodo < 7) {
            $sheet->getStyle($celdaActual)->applyFromArray($estiloNotaReprobada);
        }

        $suma_promedios_periodos += $promedio_periodo;
        $total_periodos_contados++;
        $columnaDataNum++;
    }

    // Promedio Final General Anual
    $promedio_final = 0;
    if ($total_periodos_contados > 0) {
        $promedio_final = truncarDosDecimales($suma_promedios_periodos / $total_periodos_contados);
    }

    $promedio_final_texto = ($promedio_final == 0) ? "-" : number_format($promedio_final, 2);
    $celdaFinal = Coordinate::stringFromColumnIndex($colPromedioFinal) . $filaExcelActual;
    $sheet->setCellValue($celdaFinal, $promedio_final_texto);

    // Aplicación estricta de las alertas de color en base al reglamento de rangos
    $estiloCeldaFinalActual = $sheet->getStyle($celdaFinal);
    if ($promedio_final >= 0 && $promedio_final <= 4) {
        $estiloCeldaFinalActual->applyFromArray($estiloReprobadoFinal);
    } elseif ($promedio_final > 4 && $promedio_final < 7) {
        $estiloCeldaFinalActual->applyFromArray($estiloSupletorio);
    } elseif ($promedio_final >= 7) {
        $estiloCeldaFinalActual->applyFromArray($estiloAprobado);
    }

    // Ejecución de la función almacenada SQL para obtener el examen supletorio real
    $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_est_actual, $id_paralelo, $id_asignatura, 2) AS supletorio";
    $resultado_supletorio = $db->consulta($qry);
    $calificacion = $db->fetch_assoc($resultado_supletorio);
    $supletorio_val = $calificacion["supletorio"];

    $supletorio_texto = ($supletorio_val == 0) ? "" : number_format((float)$supletorio_val, 2);
    $celdaSupletorio = Coordinate::stringFromColumnIndex($colSupletorio) . $filaExcelActual;
    $sheet->setCellValue($celdaSupletorio, $supletorio_texto);
    if ((float)$supletorio_val < 7 && $supletorio_val != 0) {
        $sheet->getStyle($celdaSupletorio)->applyFromArray($estiloNotaReprobada);
    }

    $filaExcelActual++;
}


// ==========================================
// 5. AUTOAJUSTE DE CELDAS Y CONFIGURACIÓN DE SALIDA
// ==========================================
$totalFilasInsertadas = $filaExcelActual - 1;

// Aplicar estilos a la rejilla completa de datos
$sheet->getStyle('A4:' . $ultimaColumnaLetra . $totalFilasInsertadas)->applyFromArray($estiloCeldaNormal);
$sheet->getStyle('B4:B' . $totalFilasInsertadas)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Forzar autoancho por columna para evitar textos recortados (###)
for ($i = 1; $i <= $colSupletorio; $i++) {
    $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($letra)->setAutoSize(true);
}

// Limpiar buffers de salida para evitar interferencias
if (ob_get_length()) ob_end_clean();

// Encabezados HTTP oficiales para descarga de Excel XLSX
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Calificaciones.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Fecha pasada para evitar cache del proxy
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Pragma: public');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
