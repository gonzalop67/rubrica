<?php
// Reportar todos los errores de PHP
// error_reporting(E_ALL);

// Habilitar la visualización de errores en pantalla
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

session_start();

$id_paralelo = $_POST['id_paralelo'] ?? '';
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

require_once("../scripts/clases/class.mysql.php");
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.periodos_lectivos.php');

// Nombre de la institución educativa
$institucion = new institucion();
$nombreInstitucion = $institucion->obtenerNombreInstitucion();

// Nombre del Periodo Lectivo
$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = $periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuadro Final en PDF</title>
    <style>
        body {
            font-family: Helvetica, sans-serif;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2 class="text-center"><?= $nombreInstitucion ?></h2>
    <?= $nombrePeriodoLectivo ?>
</body>

</html>