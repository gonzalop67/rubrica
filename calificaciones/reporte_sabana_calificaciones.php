<?php
// calificaciones/reporte_sabana_calificaciones.php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura de variables seguras enviadas por el formulario
    $idParalelo   = isset($_POST['id_paralelo_excel']) ? intval($_POST['id_paralelo_excel']) : 0;
    $idAsignatura = isset($_POST['id_asignatura_excel']) ? intval($_POST['id_asignatura_excel']) : 0;

    if ($idParalelo === 0 || $idAsignatura === 0) {
        die("Error: Parámetros insuficientes para procesar la sábana de notas.");
    }

    echo $idAsignatura;
    echo "<br>" . $idParalelo;
}