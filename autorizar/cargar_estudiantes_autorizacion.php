<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables POST
$id_paralelo = $_POST["id_paralelo"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

// Primero consultamos la fecha de cierre del aporte de evaluación para verificar si se encuentra vigente o no
$consulta_fecha_cierre = $db->consulta("SELECT ap_fecha_cierre FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo");
$resultado_fecha_cierre = $db->fetch_array($consulta_fecha_cierre);

$fecha_actual = new DateTime("now");
$fecha_cierre = new DateTime($resultado_fecha_cierre["ap_fecha_cierre"]);

$cadena = "";

if ($fecha_cierre < $fecha_actual) {
    // Se procede a cargar los estudiantes del aporte de evaluación seleccionado
    $cadena .= "<table class='table table-bordered table-striped table-hover'>";
    $cadena .= "<thead><tr><th>Código</th><th>Estudiante</th><th class='text-center'>Autorizar</th><th>Estado</th></tr></thead>";
    $cadena .= "<tbody>";

    // Consulta para recuperar los estudiantes del aporte de evaluación seleccionado
    $sql = "SELECT e.id_estudiante, CONCAT(e.es_apellidos, ' ', e.es_nombres) AS estudiante FROM sw_estudiante e INNER JOIN sw_estudiante_periodo_lectivo ep ON e.id_estudiante = ep.id_estudiante WHERE ep.id_paralelo = $id_paralelo";

    $consulta_estudiantes = $db->consulta($sql);

    while ($row = $db->fetch_array($consulta_estudiantes)) {
        $cadena .= "<tr>";
        $cadena .= "<td class='text-left'>" . $row["id_estudiante"] . "</td>";
        $cadena .= "<td class='text-left'>" . $row["estudiante"] . "</td>";
        // Consulta para verificar si el estudiante ya se encuentra autorizado o no
        $consulta_autorizacion = $db->consulta("SELECT * FROM sw_autorizacion WHERE id_estudiante = " . $row["id_estudiante"] . " AND id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo");
        
        if ($db->num_rows($consulta_autorizacion) == 0) {
            $autorizado = '';
        } else {
            $autorizado = 'checked';
        }

        $cadena .= "<td><input type=\"checkbox\" name=\"chkautorizar_" . $row["id_estudiante"] . "\" onclick=\"actualizar_estado_autorizado(this," . $row["id_estudiante"] . ", " . $id_paralelo . ", " . $id_aporte_evaluacion . ")\" $autorizado></td>\n";
        $cadena .= "<td class='text-left'>" . ($autorizado == 'checked' ? "Autorizado" : "No autorizado") . "</td>";
        $cadena .= "</tr>";
    }

    $cadena .= "</tbody>";
    $cadena .= "</table>";
} else {
    $cadena = "<div class='alert alert-danger' role='alert'>El aporte de evaluación seleccionado se encuentra vigente, no es posible autorizar cambios de calificación...</div>";
}

echo $cadena;