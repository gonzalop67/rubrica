<?php
include("clases/class.mysql.php");
$db = new mysql();

$id_paralelo = $_POST["id_paralelo"];
$id_estudiante = $_POST["id_estudiante"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

$cadena = "";

$consulta = $db->consulta("SELECT id_tipo_aporte FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = " . $id_aporte_evaluacion);
$aporte_evaluacion = $db->fetch_object($consulta);
$id_tipo_aporte = $aporte_evaluacion->id_tipo_aporte;

if ($id_tipo_aporte == 1) {
    $consulta = $db->consulta("SELECT ru_nombre FROM sw_rubrica_evaluacion WHERE id_tipo_asignatura = 1 AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
    $num_total_registros = $db->num_rows($consulta);
    if ($num_total_registros > 0) {
        //Cabecera de la tabla
        $cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
        $cadena .= "<thead>\n";
        $cadena .= "<tr>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Asignatura</th>\n";
        while ($titulo_rubrica = $db->fetch_assoc($consulta)) {
            $cadena .= "<th>" . $titulo_rubrica["ru_nombre"] . "</th>\n";
        }
        $cadena .= "<th>PROMEDIO</th>\n";
        $cadena .= "</tr>\n";
        $cadena .= "</thead>\n";
        //Cuerpo de la tabla
        $cadena .= "<tbody>\n";
        $asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
        $contador = 0;
        while ($asignatura = $db->fetch_assoc($asignaturas)) {
            $contador++;
            $id_asignatura = $asignatura["id_asignatura"];
            // Aqui se consultan las rubricas de las asignaturas cuantitativas definidas para el aporte de evaluacion elegido
            $rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion AND a.id_tipo_asignatura = 1");
            $num_total_registros = $db->num_rows($rubrica_evaluacion);
            if ($num_total_registros > 0) {
                $cadena .= "<tr>\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>" . $asignatura["as_nombre"] . "</td>\n";
                $suma_rubricas = 0;
                $contador_rubricas = 0;
                while ($rubricas = $db->fetch_assoc($rubrica_evaluacion)) {
                    $contador_rubricas++;
                    $id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
                    $qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
                    $num_total_registros = $db->num_rows($qry);
                    $rubrica_estudiante = $db->fetch_assoc($qry);
                    if ($num_total_registros > 0) {
                        $calificacion = $rubrica_estudiante["re_calificacion"];
                    } else {
                        $calificacion = 0;
                    }
                    $suma_rubricas += $calificacion;
                    $calificacion = $calificacion == 0 ? "" : substr($calificacion, 0, strpos($calificacion, '.') + 3);
                    $cadena .= "<td>" . $calificacion . "</td>\n";
                }
                $promedio = $suma_rubricas / $contador_rubricas;
                $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
                $cadena .= "<td>" . $promedio . "</td>\n";
                $cadena .= "</tr>\n";
            }
        }
        $cadena .= "</tbody>\n";
        $cadena .= "</table>\n";
        // Luego las asignaturas cualitativas
        $consulta = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 2 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
        if ($db->num_rows($consulta) > 0) {
            $cadena .= "<div id='titulo2' class='header2'> ASIGNATURAS CUALITATIVAS </div>\n";
            $consulta = $db->consulta("SELECT ru_nombre FROM sw_rubrica_evaluacion WHERE id_tipo_asignatura = 2 AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
            //Cabecera de la tabla
            $cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
            $cadena .= "<thead>\n";
            $cadena .= "<tr>\n";
            $cadena .= "<th>Nro.</th>\n";
            $cadena .= "<th>Asignatura</th>\n";
            while ($titulo_rubrica = $db->fetch_assoc($consulta)) {
                $cadena .= "<th>" . $titulo_rubrica["ru_nombre"] . "</th>\n";
            }
            $cadena .= "</tr>\n";
            $cadena .= "</thead>\n";
            $cadena .= "<tbody>\n";

            // Aqui van las asignaturas cualitativas...
            $asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 2 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
            $contador = 0;
            while ($asignatura = $db->fetch_assoc($asignaturas)) {
                $contador++;
                $id_asignatura = $asignatura["id_asignatura"];
                // Aqui se consultan las rubricas de las asignaturas cuantitativas definidas para el aporte de evaluacion elegido
                $query = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion";
                $rubrica_evaluacion = $db->consulta($query);
                $num_total_registros = $db->num_rows($rubrica_evaluacion);
                if ($num_total_registros > 0) {
                    $cadena .= "<tr>\n";
                    $cadena .= "<td>$contador</td>\n";
                    $cadena .= "<td>" . $asignatura["as_nombre"] . "</td>\n";
                    //Aqui va la calificacion cualitativa...
                    while ($rubricas = $db->fetch_assoc($rubrica_evaluacion)) {
                        $id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
                        $query = "SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion";
                        $qry = $db->consulta($query);
                        $num_total_registros = $db->num_rows($qry);
                        $rubrica_estudiante = $db->fetch_assoc($qry);
                        if ($num_total_registros > 0) {
                            $calificacion = $rubrica_estudiante["rc_calificacion"];
                        } else {
                            $calificacion = " ";
                        }
                        $cadena .= "<td>" . $calificacion . "</td>\n";
                    }
                    $cadena .= "</tr>\n";
                }
            }
            $cadena .= "</tbody>\n";
            $cadena .= "</table>\n";
        }
    } else {
        $cadena .= "<p class='mensaje'>\n";
        $cadena .= "No se han definido los insumos de evaluaci&oacute;n correspondientes...\n";
        $cadena .= "</p>\n";
    }
} else if ($id_tipo_aporte == 2) {
    // Consultar los id_periodo_evaluacion correspondientes al id_aporte_evaluacion actual
    $consulta = $db->consulta("SELECT pe.id_periodo_evaluacion FROM sw_periodo_evaluacion pe, sw_aporte_evaluacion ae WHERE pe.id_periodo_evaluacion = ae.id_periodo_evaluacion AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
    $num_total_registros = $db->num_rows($consulta);
    if ($num_total_registros > 0) {
        //Cabecera de la tabla
        $registros = $db->fetch_object($consulta);
        $id_periodo_evaluacion = $registros->id_periodo_evaluacion;
        $cadena .= "<table class=\"table table-striped table-hover fuente9\">\n";
        $cadena .= "<thead>\n";
        $cadena .= "<tr>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Asignatura</th>\n";
        // Consultar los id_aporte_evaluacion del id_periodo_evaluacion correspondiente
        $consulta = $db->consulta("SELECT ap_abreviatura, ap_ponderacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
        // Desplegar los títulos de los aportes de evaluación
        while ($aporte_evaluacion = $db->fetch_object($consulta)) {
            $cadena .= "<th>$aporte_evaluacion->ap_abreviatura</th>\n";
            $cadena .= "<th>" . ($aporte_evaluacion->ap_ponderacion * 100) . "%</th>\n";
        }
        $cadena .= "<th>NOTA P.</th>\n";
        $cadena .= "</thead>\n";
        $cadena .= "<tbody>\n";
        //Cuerpo de la tabla
        $cadena .= "<tbody>\n";
        $asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
        $contador = 0;
        //Desplegar los nombres de las asignaturas
        while ($asignatura = $db->fetch_object($asignaturas)) {
            $contador++;
            $id_asignatura = $asignatura->id_asignatura;
            $cadena .= "<tr>\n";
            $cadena .= "<td>$contador</td>";
            $cadena .= "<td>" . $asignatura->as_nombre . "</td>\n";
            // Aqui se calculan los promedios de cada aporte de evaluacion
            $aportes_evaluacion = $db->consulta("SELECT id_aporte_evaluacion, 
                                                        ap_ponderacion 
                                                   FROM sw_periodo_evaluacion p, 
                                                        sw_aporte_evaluacion a 
                                                  WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
                                                    AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
            // Aqui calculo los promedios y desplegar en la tabla
            $suma_ponderados = 0;
            while ($aporte = $db->fetch_object($aportes_evaluacion)) {
                $id_aporte_evaluacion = $aporte->id_aporte_evaluacion;
                $ap_ponderacion = $aporte->ap_ponderacion;
                $consulta = $db->consulta("SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio_aporte");
                $registro = $db->fetch_object($consulta);
                $promedio_aporte = $registro->promedio_aporte;
                $promedio_ponderado = $promedio_aporte * $ap_ponderacion;
                $suma_ponderados += $promedio_ponderado;
                $cadena .= "<td>$promedio_aporte</td>\n";
                $cadena .= "<td>$promedio_ponderado</td>\n";
            }
            //Desplegar la suma de los promedios ponderados
            $cadena .= "<td>$suma_ponderados</td>\n";
            $cadena .= "</tr>\n";
        }
        $cadena .= "</tbody>\n";
        $cadena .= "</table>\n";
    } else {
        $cadena .= "<p class='mensaje'>\n";
        $cadena .= "No se han definido los insumos de evaluaci&oacute;n correspondientes...\n";
        $cadena .= "</p>\n";
    }
}

echo $cadena;
