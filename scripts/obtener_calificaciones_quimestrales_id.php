<?php
include("../scripts/clases/class.mysql.php");
$db = new mysql();
$id_estudiante = $_POST["id_estudiante"];
$id_paralelo = $_POST["id_paralelo"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

$cadena = "";

$consulta = "SELECT * 
                   FROM sw_aporte_evaluacion 
                  WHERE id_periodo_evaluacion = $id_periodo_evaluacion 
                    AND id_tipo_aporte = 2";

$existe_examen = false;

$asignaturas = $db->consulta("
    SELECT as_nombre, 
           a.id_asignatura 
      FROM sw_asignatura a, 
           sw_asignatura_curso ac, 
           sw_paralelo p 
     WHERE a.id_asignatura = ac.id_asignatura 
       AND p.id_curso = ac.id_curso 
       AND a.id_tipo_asignatura = 1 
       AND p.id_paralelo = $id_paralelo 
     ORDER BY ac_orden
    ");
$num_total_registros = $db->num_rows($asignaturas);
if ($num_total_registros > 0) {
    //Cabecera de la tabla
    $cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
    $cadena .= "<thead>\n";
    $cadena .= "<tr>\n";
    $cadena .= "<th>Nro.</th>\n";
    $cadena .= "<th>Asignatura</th>\n";
    //Cabeceras de los parciales...
    $consulta = $db->consulta("
        SELECT ap_abreviatura, 
               ap_ponderacion  
          FROM sw_aporte_evaluacion 
         WHERE id_periodo_evaluacion = $id_periodo_evaluacion
        ");
    $num_total_registros = $db->num_rows($consulta);
    while ($titulo_aporte = $db->fetch_assoc($consulta)) {
        $cadena .= "<th>" . $titulo_aporte["ap_abreviatura"] . "</th>\n";
        $cadena .= "<th>" . ($titulo_aporte["ap_ponderacion"] * 100) . "%</th>\n";
    }
    $cadena .= "<th>NOTA P.</th>\n";
    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";
    //Consultar las calificaciones de cada asignatura...
    $contador = 0;
    while ($asignatura = $db->fetch_assoc($asignaturas)) {
        $contador++;
        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>" . $asignatura["as_nombre"] . "</td>\n";
        //Consultar las calificaciones de cada parcial...
        $id_asignatura = $asignatura["id_asignatura"];
        $aporte_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, 
                                                   ap_ponderacion 
                                              FROM sw_periodo_evaluacion p, 
                                                   sw_aporte_evaluacion a 
                                             WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
                                               AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
        if ($db->num_rows($aporte_evaluacion)) {
            // Aqui calculo los promedios y desplegar en la tabla
            $suma_ponderados = 0;
            while ($aporte = $db->fetch_object($aporte_evaluacion)) {
                $id_aporte_evaluacion = $aporte->id_aporte_evaluacion;
                $ap_ponderacion = $aporte->ap_ponderacion;
                //
                $consulta = $db->consulta("SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_aporte");
                $registro = $db->fetch_object($consulta);
                $promedio_aporte = $registro->promedio_aporte;
                $promedio_ponderado = $promedio_aporte * $ap_ponderacion;
                $suma_ponderados += $promedio_ponderado;
                $promedio_aporte = $promedio_aporte == 0 ? "" : substr($promedio_aporte, 0, strpos($promedio_aporte, '.') + 3);
                $promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio_ponderado, '.') + 3);
                $cadena .= "<td>$promedio_aporte</td>\n";
                $cadena .= "<td>$promedio_ponderado</td>\n";
            }
            // Aqui desplegar la nota de sub periodo
            $suma_ponderados = $suma_ponderados == 0 ? "" : substr($suma_ponderados, 0, strpos($suma_ponderados, '.') + 3);
            $cadena .= "<td>$suma_ponderados</td>\n";
        }
        $cadena .= "</tr>\n";
    }
    $cadena .= "</tbody>\n";
    $cadena .= "</table>\n";
}
echo $cadena;
