<?php
// calificaciones/reporte_sabana_calificaciones.php

session_start();

require_once "../vendor/autoload.php";
require_once "../scripts/clases/class.mysql.php";
require_once '../scripts/clases/class.institucion.php';
require_once '../scripts/clases/class.periodos_lectivos.php';
require_once '../scripts/clases/class.paralelos.php';
require_once '../scripts/clases/class.asignaturas.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

function truncarDosDecimales(string $valor)
{
    $float = (float)$valor;
    if ($float == 0) return 0;

    // Sumamos una millonésima (0.000001) para corregir el desfase binario de PHP (ej: 7.09999999 -> 7.100000)
    $comprobacion = $float + 0.000001;

    // Desplazamos la coma dos posiciones, truncamos el resto con floor y regresamos la coma
    return floor($comprobacion * 100) / 100;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura de variables seguras enviadas por el formulario
    $idParalelo   = isset($_POST['id_paralelo_excel']) ? intval($_POST['id_paralelo_excel']) : 0;
    $idAsignatura = isset($_POST['id_asignatura_excel']) ? intval($_POST['id_asignatura_excel']) : 0;

    if ($idParalelo === 0 || $idAsignatura === 0) {
        die("Error: Parámetros insuficientes para procesar la sábana de notas.");
    }

    $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    $id_usuario = $_SESSION['id_usuario'];

    $db = new MySQL;

    // ==========================================
    // 1. ARMAR LA ESTRUCTURA DINÁMICA DE PERIODOS
    // ==========================================
    $estructura = [];
    $sql_periodo = "SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = {$id_periodo_lectivo} AND id_tipo_periodo = 1";
    $res_periodo = $db->consulta($sql_periodo);

    $totalColumnasDinamicas = 0; // Llevará el conteo global de todas las columnas dinámicas de la estructura

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
        $periodo_item['total_columnas']++; // Suma la columna del PROMEDIO de este periodo
        $totalColumnasDinamicas += $periodo_item['total_columnas'];
        $estructura[] = $periodo_item;
    }

    // 2. OBTENER LA NÓMINA DE ESTUDIANTES ACTIVOS
    $sql_estudiantes = "SELECT e.id_estudiante, CONCAT(e.es_apellidos, ' ', e.es_nombres) AS nombre_completo
    FROM sw_estudiante e
    INNER JOIN sw_estudiante_periodo_lectivo ep ON ep.id_estudiante = e.id_estudiante 
    WHERE ep.activo = 1
    AND ep.id_paralelo = $idParalelo 
    ORDER BY e.es_apellidos ASC, e.es_nombres ASC";

    $res_estudiantes = $db->consulta($sql_estudiantes);

    if ($db->num_rows($res_estudiantes) == 0) {
        die("No existen estudiantes matriculados o activos en este paralelo.");
    }

    $institucion = new institucion();
    $nombreInstitucion = $institucion->obtenerNombreInstitucion();
    $nombreRector = $institucion->obtenerNombreRector();
    $nombreSecretario = $institucion->obtenerNombreSecretario();
    $jornada = $institucion->obtenerJornada($idParalelo);

    $periodo_lectivo = new periodos_lectivos();
    $nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);

    $paralelo = new paralelos();
    $id_curso = $paralelo->obtenerIdCurso($idParalelo);
    $nombreParalelo = $paralelo->obtenerNombreParalelo($idParalelo);
    
    // Obtener el nombre de la oferta educativa
    $sql_oferta_educativa = "SELECT oe.nombre FROM sw_ofertas_educativas oe, sw_periodo_lectivo pl 
    WHERE oe.id = pl.oferta_educativa_id AND id_periodo_lectivo = $id_periodo_lectivo";
    
    $res_oferta_educativa = $db->consulta($sql_oferta_educativa);

    if ($db->num_rows($res_oferta_educativa) == 0) {
        die("No existe una oferta educativa asociada.");
    } 

    $oferta_educativa = $db->fetch_object($res_oferta_educativa)->nombre;

    // Obtener el nombre de la asignatura
    $asignatura = new asignaturas();
    $nombreAsignatura = $asignatura->obtenerNombreAsignatura($idAsignatura);

    // INICIALIZAR HOJA DE EXCEL
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Calificaciones');

    // Colocación del Logo del Gobierno
    $logoPath = __DIR__ . '/logo-presidencia-noboa.jpg';
    if (file_exists($logoPath)) {
        $drawing = new Drawing();
        $drawing->setName('Logo del Gobierno');
        $drawing->setPath($logoPath);
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
    }

    // Colocación del Logo de la Institución en la última columna del reporte
    $queryString = "SELECT in_logo FROM sw_institucion WHERE id_institucion = 1";
    $consulta = $db->consulta($queryString);
    $registro = $db->fetch_object($consulta);
    $in_logo = $registro->in_logo;
    $logoInstitucionPath = dirname(__DIR__) . '/public/uploads/' . $in_logo;

    if (file_exists($logoInstitucionPath)) {
        $drawing2 = new Drawing();
        $drawing2->setName('Logo de la Institucion');
        $drawing2->setPath($logoInstitucionPath);
        $drawing2->setHeight(90);

        // CÁLCULO EXACTO DE LA ÚLTIMA COLUMNA:
        // Columna A (1) + Columna B (1) + Todas las dinámicas + 2 Columnas de Cierre (Promedio Final y Supletorio)
        $indiceUltimaColumna = 2 + $totalColumnasDinamicas + 2;
        $colUltimaLetraReporte = Coordinate::stringFromColumnIndex($indiceUltimaColumna);

        $drawing2->setCoordinates($colUltimaLetraReporte . '1');
        $drawing2->setWorksheet($sheet);
    }

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

    // ==========================================
    // 2.5. DISEÑO DEL ENCABEZADO INSTITUCIONAL (FILAS 1 A 6)
    // ==========================================
    
    // Calculamos la última letra del reporte para hacer los merges perfectos
    // (A + B + Dinámicas + Promedio Final + Supletorio)
    $indiceUltimaColumna = 2 + $totalColumnasDinamicas + 2;
    $colUltimaLetraReporte = Coordinate::stringFromColumnIndex($indiceUltimaColumna);

    // 1. Nombre de la Institución (Fila 2)
    $sheet->setCellValue('C2', strtoupper($nombreInstitucion));
    $sheet->mergeCells('C2:' . Coordinate::stringFromColumnIndex($indiceUltimaColumna - 2) . '2');
    $sheet->getStyle('C2')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
    ]);

    // 2. Subtítulo del Reporte (Fila 3)
    $sheet->setCellValue('C3', 'REPORTE MATRICIAL DE CALIFICACIONES ANUALES');
    $sheet->mergeCells('C3:' . Coordinate::stringFromColumnIndex($indiceUltimaColumna - 2) . '3');
    $sheet->getStyle('C3')->applyFromArray([
        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '555555']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
    ]);

    // 3. Periodo Lectivo (Fila 4)
    $sheet->setCellValue('C4', 'AÑO LECTIVO: ' . $nombrePeriodoLectivo);
    $sheet->mergeCells('C4:' . Coordinate::stringFromColumnIndex($indiceUltimaColumna - 2) . '4');
    $sheet->getStyle('C4')->applyFromArray([
        'font' => ['italic' => true, 'size' => 10],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
    ]);

    // 4. Bloque de Datos Informativos (Fila 5 y 6)
    // Izquierda: Curso y Paralelo
    $sheet->setCellValue('A5', 'CURSO / PARALELO: ' . strtoupper($nombreParalelo));
    $sheet->getStyle('A5')->getFont()->setBold(true);

    $sheet->setCellValue('A6', 'JORNADA: ' . strtoupper($jornada));
    $sheet->getStyle('A6')->getFont()->setBold(true);

    // Derecha: Asignatura y Tipo de Educación (Alineado a la derecha antes del logo)
    $colInformativaDerecha = Coordinate::stringFromColumnIndex($indiceUltimaColumna - 3);
    
    $sheet->setCellValue($colInformativaDerecha . '5', 'ASIGNATURA: ' . strtoupper($nombreAsignatura));
    $sheet->mergeCells($colInformativaDerecha . '5:' . $colUltimaLetraReporte . '5');
    $sheet->getStyle($colInformativaDerecha . '5')->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
    ]);

    $sheet->setCellValue($colInformativaDerecha . '6', 'OFERTA EDUCATIVA: ' . strtoupper($oferta_educativa));
    $sheet->mergeCells($colInformativaDerecha . '6:' . $colUltimaLetraReporte . '6');
    $sheet->getStyle($colInformativaDerecha . '6')->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
    ]);

    // Ajustar alto de las filas del encabezado para que respiren los textos y logos
    $sheet->getRowDimension('1')->setRowHeight(25);
    $sheet->getRowDimension('2')->setRowHeight(25);
    $sheet->getRowDimension('3')->setRowHeight(18);
    $sheet->getRowDimension('4')->setRowHeight(18);
    $sheet->getRowDimension('5')->setRowHeight(18);
    $sheet->getRowDimension('6')->setRowHeight(18);

    // ==============================================
    // 3. CONSTRUCCIÓN DE CABECERAS TRIPLES DINÁMICAS
    // ==============================================
    $filaInicioCabecera = 7; // Las 6 primeras filas quedan libres. Cabecera ocupa: 7, 8 y 9.

    // Ajustar "Nro." y "Nómina" para que abarquen exactamente las 3 filas de la cabecera (7, 8 y 9)
    $sheet->setCellValue('A' . $filaInicioCabecera, 'Nro.')->mergeCells('A' . $filaInicioCabecera . ':A' . ($filaInicioCabecera + 2));
    $sheet->setCellValue('B' . $filaInicioCabecera, 'Nómina')->mergeCells('B' . $filaInicioCabecera . ':B' . ($filaInicioCabecera + 2));

    $columnaActualNum = 3; // Empezamos en la columna C (Índice 3)

    foreach ($estructura as $p) {
        $colInicioBimestre = $columnaActualNum;
        $colFinBimestre = $colInicioBimestre + $p['total_columnas'] - 1;

        // FILA 1 de Cabecera: Nombre del Periodo (Fila 7)
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colInicioBimestre) . $filaInicioCabecera, strtoupper($p['nombre']));
        $sheet->mergeCells(
            Coordinate::stringFromColumnIndex($colInicioBimestre) . $filaInicioCabecera . ':' .
                Coordinate::stringFromColumnIndex($colFinBimestre) . $filaInicioCabecera
        );

        foreach ($p['aportes'] as $a) {
            $colInicioAporte = $columnaActualNum;
            $colspan_aporte = count($a['insumos']);
            if ($a['tipo'] == 1) $colspan_aporte++;
            $colFinAporte = $colInicioAporte + $colspan_aporte - 1;

            // FILA 2 de Cabecera: Nombre del Aporte (Fila 8)
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colInicioAporte) . ($filaInicioCabecera + 1), strtoupper($a['nombre']));
            $sheet->mergeCells(
                Coordinate::stringFromColumnIndex($colInicioAporte) . ($filaInicioCabecera + 1) . ':' .
                    Coordinate::stringFromColumnIndex($colFinAporte) . ($filaInicioCabecera + 1)
            );

            // FILA 3 de Cabecera: Insumos (Fila 9) - Van sueltos, sin merge vertical
            foreach ($a['insumos'] as $insumo) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnaActualNum) . ($filaInicioCabecera + 2), strtoupper($insumo['nombre']));
                $columnaActualNum++;
            }

            if ($a['tipo'] == 1) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnaActualNum) . ($filaInicioCabecera + 2), 'PROM.');
                $columnaActualNum++;
            }
        }

        // Columna final del Periodo: PROMEDIO BIMESTRAL (Ocupa Fila 8 y 9 para alinearse perfectamente abajo)
        $columnaPromedio = Coordinate::stringFromColumnIndex($columnaActualNum);
        $sheet->setCellValue($columnaPromedio . ($filaInicioCabecera + 1), 'PROMEDIO');
        $sheet->mergeCells($columnaPromedio . ($filaInicioCabecera + 1) . ':' . $columnaPromedio . ($filaInicioCabecera + 2));
        $columnaActualNum++;
    }

    // Columnas de Cierre Finales (Merge vertical completo desde Fila 7 a la 9)
    $colPromedioFinalLetter = Coordinate::stringFromColumnIndex($columnaActualNum);
    $sheet->setCellValue($colPromedioFinalLetter . $filaInicioCabecera, 'PROMEDIO FINAL');
    $sheet->mergeCells($colPromedioFinalLetter . $filaInicioCabecera . ':' . $colPromedioFinalLetter . ($filaInicioCabecera + 2));
    $columnaActualNum++;

    $colSupletorioLetter = Coordinate::stringFromColumnIndex($columnaActualNum);
    $sheet->setCellValue($colSupletorioLetter . $filaInicioCabecera, 'SUPLETORIO');
    $sheet->mergeCells($colSupletorioLetter . $filaInicioCabecera . ':' . $colSupletorioLetter . ($filaInicioCabecera + 2));

    // Aplicar estilos a toda la cabecera calculada (Filas 7 a 9)
    $ultimaColumnaLetra = $colSupletorioLetter;
    $sheet->getStyle('A' . $filaInicioCabecera . ':' . $ultimaColumnaLetra . ($filaInicioCabecera + 2))->applyFromArray($estiloGlobalCabecera);

    // ==========================================
    // 4. PROCESAMIENTO MATRICIAL DE LAS CALIFICACIONES
    // ==========================================
    // CORRECCIÓN: Como la cabecera ahora solo ocupa las filas 7, 8 y 9, los datos arrancan exactamente en la fila 10 (7 + 3)
    $filaExcelActual = $filaInicioCabecera + 3;
    $contador_nro = 1;

    // Estilos de alerta para el promedio final...
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
                    WHERE id_estudiante = $id_est_actual AND id_paralelo = $idParalelo 
                    AND id_asignatura = $idAsignatura AND id_rubrica_personalizada = {$insumo['id']} LIMIT 1";
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

        // CORRECCIÓN: Asignación dinámica basada en la posición real del cursor de columnas ($columnaDataNum)
        // Promedio Final General Anual
        $promedio_final = 0;
        if ($total_periodos_contados > 0) {
            $promedio_final = truncarDosDecimales($suma_promedios_periodos / $total_periodos_contados);
        }

        $promedio_final_texto = ($promedio_final == 0) ? "-" : number_format($promedio_final, 2);
        $celdaFinal = Coordinate::stringFromColumnIndex($columnaDataNum) . $filaExcelActual; // CORRECCIÓN: Usa $columnaDataNum
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

        $columnaDataNum++; // Desplazar cursor para el supletorio

        // Ejecución de la función almacenada SQL para obtener el examen supletorio real
        $qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_est_actual, $idParalelo, $idAsignatura, 2) AS supletorio";
        $resultado_supletorio = $db->consulta($qry);
        $calificacion = $db->fetch_assoc($resultado_supletorio);
        $supletorio_val = $calificacion["supletorio"];

        $supletorio_texto = ($supletorio_val == 0) ? "" : number_format((float)$supletorio_val, 2);
        $celdaSupletorio = Coordinate::stringFromColumnIndex($columnaDataNum) . $filaExcelActual; // CORRECCIÓN: Usa $columnaDataNum
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
    $sheet->getStyle('A4:' . $ultimaColumnaLetra . $totalFilasInsertadas)->applyFromArray($estiloCellNormal ?? $estiloCeldaNormal);
    $sheet->getStyle('B4:B' . $totalFilasInsertadas)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    // CORRECCIÓN: El autoancho ahora itera hasta la última columna real procesada dinámicamente ($columnaDataNum)
    for ($i = 1; $i <= $columnaDataNum; $i++) {
        $letra = Coordinate::stringFromColumnIndex($i);
        $sheet->getColumnDimension($letra)->setAutoSize(true);
    }

    // ==========================================
    // 6. BLOQUE DE FIRMA DE RESPONSABILIDAD (DOCENTE)
    // ==========================================
    // Dejamos 4 filas libres después del último estudiante para el espacio de la firma
    $filaFirmaSeparador = $totalFilasInsertadas + 4;
    $filaFirmaTexto = $filaFirmaSeparador + 1;

    // Calculamos el centro aproximado del reporte para ubicar la firma de forma estética
    // Tomamos como base la columna C o D para que quede alineado hacia la izquierda/centro
    $colFirmaInicio = 'B';
    $colFirmaFin = 'E'; 

    // Línea para firmar
    $sheet->setCellValue($colFirmaInicio . $filaFirmaSeparador, '________________________________________');
    $sheet->mergeCells($colFirmaInicio . $filaFirmaSeparador . ':' . $colFirmaFin . $filaFirmaSeparador);
    $sheet->getStyle($colFirmaInicio . $filaFirmaSeparador)->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);

    // Obtener el nombre del docente
    $sql_docente = "SELECT us_shortname FROM sw_usuario WHERE id_usuario = $id_usuario";
    $res_docente = $db->consulta($sql_docente);

    $nombre_docente = $db->fetch_object($res_docente)->us_shortname;

    // Texto de identificación del docente
    $textoDocente = "DOCENTE: " . mb_strtoupper($nombre_docente ?? 'DOCENTE DE LA ASIGNATURA', 'UTF-8'); 
    
    $sheet->setCellValue($colFirmaInicio . $filaFirmaTexto, $textoDocente);
    $sheet->mergeCells($colFirmaInicio . $filaFirmaTexto . ':' . $colFirmaFin . $filaFirmaTexto);
    $sheet->getStyle($colFirmaInicio . $filaFirmaTexto)->applyFromArray([
        'font' => ['bold' => true, 'size' => 10],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);

    // Asegurar que las filas de la firma no se vean comprimidas
    $sheet->getRowDimension($filaFirmaSeparador)->setRowHeight(20);
    $sheet->getRowDimension($filaFirmaTexto)->setRowHeight(20);

        // ==========================================
    // 7. CONFIGURACIÓN DE IMPRESIÓN Y PÁGINA
    // ==========================================
    $pageSetup = $sheet->getPageSetup();

    // 1. Forzar orientación horizontal (apaisada) - CORREGIDO EL NAMESPACE
    $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    // 2. Definir tamaño de papel estándar - CORREGIDO EL NAMESPACE
    $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);

    // 3. Activar el ajuste manual de escala
    $pageSetup->setFitToPage(true);
    
    // 4. Forzar a que ocupe EXACTAMENTE 1 página de ancho
    $pageSetup->setFitToWidth(1);
    
    // 5. Dejar el alto en 0 para que use las páginas verticales que necesite según el número de alumnos
    $pageSetup->setFitToHeight(0);

    // 6. Configurar márgenes limpios y estrechos (valores en pulgadas)
    $pageMargins = $sheet->getPageMargins();
    $pageMargins->setTop(0.5);
    $pageMargins->setBottom(0.5);
    $pageMargins->setLeft(0.4);
    $pageMargins->setRight(0.4);

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
}
