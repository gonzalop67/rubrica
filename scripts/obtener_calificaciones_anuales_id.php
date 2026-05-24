<?php
include("../scripts/clases/class.mysql.php");

$db = new mysql();

function truncar($numero, $digitos)
{
    $truncar = pow(10, $digitos);
    return intval($numero * $truncar) / $truncar;
}

function existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_perido, $id_periodo_lectivo)
{
    global $db;
    $qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $id_tipo_perido AND p.id_periodo_lectivo = $id_periodo_lectivo");
    $registro = $db->fetch_assoc($qry);
    $id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

    $qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
    return ($db->num_rows($qry) > 0);
}

function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_perido, $id_periodo_lectivo)
{
    global $db;
    $qry = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $id_tipo_perido AND p.id_periodo_lectivo = $id_periodo_lectivo");
    $registro = $db->fetch_assoc($qry);
    $id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

    $qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

    if ($qry) {
        $rubrica_estudiante = $db->fetch_assoc($qry);
        $calificacion = $rubrica_estudiante["re_calificacion"];
    } else {
        $calificacion = 0;
    }

    return $calificacion;
}

function obtenerFechaAperturaSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_perido, $id_periodo_lectivo)
{
    global $db;
    // Obtencion de la fecha de apertura del aporte indicado por el campo id_tipo_perido
    $qry = $db->consulta("SELECT ac.ap_fecha_apertura 
        FROM sw_periodo_evaluacion p, 
             sw_aporte_evaluacion a, 
             sw_aporte_paralelo_cierre ac,  
             sw_paralelo pa 
       WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion 
         AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
         AND pa.id_paralelo = ac.id_paralelo 
         AND pa.id_paralelo = $id_paralelo 
         AND id_tipo_periodo = $id_tipo_perido 
         AND p.id_periodo_lectivo = $id_periodo_lectivo");
    $registro = $db->fetch_assoc($qry);
    return $registro["ap_fecha_apertura"];
}

// Variables POST
$id_estudiante = $_POST["id_estudiante"];
$id_paralelo = $_POST["id_paralelo"];

// Variables de Sesion
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Obtener el id_curso
$consulta = $db->consulta("SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$registro = $db->fetch_object($consulta);
$id_curso = $registro->id_curso;

// Obtener las calificaciones anuales...
$cadena = "";

$asignaturas = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
$num_total_registros = $db->num_rows($asignaturas);

$promedio_general = 0;

if ($num_total_registros > 0) {
    //Cabecera de la tabla
    $cadena .= "<table class=\"table table-striped table-hover fuente8\">\n";
    $cadena .= "<thead>\n";
    $cadena .= "<tr>\n";
    $cadena .= "<th>Nro.</th>\n";
    $cadena .= "<th>Asignatura</th>\n";
    //Leyendas de los periodos de evaluacion
    $consulta = $db->consulta("SELECT pe.pe_abreviatura, pc.pe_ponderacion FROM sw_periodo_evaluacion pe, sw_periodo_evaluacion_curso pc WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion AND pc.id_curso = $id_curso AND id_tipo_periodo IN (1, 7, 8) AND pc.id_periodo_lectivo = $id_periodo_lectivo ORDER BY pc_orden");

    while ($periodo = $db->fetch_assoc($consulta)) {
        $cadena .= "<th>" . $periodo["pe_abreviatura"] . "</th>\n";
        $cadena .= "<th>" . ($periodo["pe_ponderacion"] * 100) . "%</th>\n";
    }

    //Leyendas de los exámenes supletorios, remediales, de gracia
    $cadena .= "<th>NOTA F.</th>\n";
    $cadena .= "<th>OBSERVACION</th>\n";

    $consulta = $db->consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo IN (2, 3) AND id_periodo_lectivo = " . $id_periodo_lectivo);

    while ($periodo = $db->fetch_assoc($consulta)) {
        $cadena .= "<th>" . $periodo["pe_abreviatura"] . "</th>\n";
    }

    $cadena .= "<th>OBS. FINAL</th>\n";
    $cadena .= "</thead>\n";
    $cadena .= "<tbody>\n";
    $contador = 0;
    $num_asignaturas_aprueba = 0;

    /* while ($asignatura = $db->fetch_assoc($asignaturas)) {apertura
        $contador++;
        $contador_sin_examen = 0;
        $id_asignatura = $asignatura["id_asignatura"];
        $nom_asignatura = $asignatura["as_nombre"];
        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>$nom_asignatura</td>\n";
        //Aqui obtenemos las calificaciones quimestrales
        $periodo_evaluacion = $db->consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
        $num_total_registros = $db->num_rows($periodo_evaluacion);
        //echo $num_total_registros;
        if ($num_total_registros > 0) {
            $suma_periodos = 0;
            $contador_periodos = 0;
            while ($periodo = $db->fetch_assoc($periodo_evaluacion)) {
                $contador_periodos++;
                $id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

                $qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
                $aporte_evaluacion = $db->consulta($qry);
                $num_total_registros = $db->num_rows($aporte_evaluacion);
                if ($num_total_registros > 0) {
                    // Aqui calculo los promedios y desplegar en la tabla
                    $suma_aportes = 0;
                    $contador_aportes = 0;
                    $suma_promedios = 0;
                    while ($aporte = $db->fetch_assoc($aporte_evaluacion)) {
                        $contador_aportes++;
                        $rubrica_evaluacion = $db->consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_tipo_asignatura = 1 AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
                        $total_rubricas = $db->num_rows($rubrica_evaluacion);
                        if ($total_rubricas > 0) {
                            $suma_rubricas = 0;
                            $contador_rubricas = 0;
                            while ($rubricas = $db->fetch_assoc($rubrica_evaluacion)) {
                                $contador_rubricas++;
                                $id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
                                $qry = $db->consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
                                $total_registros = $db->num_rows($qry);
                                if ($total_registros > 0) {
                                    $rubrica_estudiante = $db->fetch_assoc($qry);
                                    $calificacion = $rubrica_estudiante["re_calificacion"];
                                } else {
                                    $calificacion = 0;
                                }
                                $suma_rubricas += $calificacion;
                            }
                            // Aqui calculo el promedio del aporte de evaluacion
                            $promedio = $suma_rubricas / $contador_rubricas;
                            if ($contador_aportes <= $num_total_registros - 1) {
                                $suma_promedios += $promedio;
                            } else {
                                $examen_quimestral = $promedio;
                            }
                        }
                    }
                    // Aqui se calculan las calificaciones del periodo de evaluacion
                    if ($examen_quimestral == 0) $contador_sin_examen++;
                    $promedio_aportes = $suma_promedios / ($contador_aportes - 1);
                    $ponderado_aportes = 0.8 * $promedio_aportes;
                    $ponderado_examen = 0.2 * $examen_quimestral;
                    $calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
                    $suma_periodos += $calificacion_quimestral;
                    $cadena .= "<td>" . substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3) . "</td>\n";
                }
            }
            // Calculo la suma y el promedio de los dos quimestres
            $promedio_periodos = $suma_periodos / $contador_periodos;
            $promedio_final = $promedio_periodos;
            $examen_supletorio = 0;
            $examen_remedial = 0;
            $examen_de_gracia = 0;
            $equiv_final = "";

            //Ahora toca revisar lo de examenes supletorios, remediales y de gracia...
            if ($promedio_periodos >= 7) {
                $equiv_final = "APRUEBA";
                $num_asignaturas_aprueba++;
            } else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
                $equiv_final = "SUPLETORIO";
                if (existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
                    // Obtencion de la calificacion del examen supletorio
                    $examen_supletorio = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
                    if ($examen_supletorio >= 7) {
                        $promedio_final = 7;
                        $equiv_final = "APRUEBA";
                        $num_asignaturas_aprueba++;
                    } else {
                        $equiv_final = "REMEDIAL";
                        if (existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
                            // Obtencion de la calificacion del examen remedial
                            $examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                            if ($examen_remedial >= 7) {
                                $promedio_final = 7;
                                $equiv_final = "APRUEBA";
                                $num_asignaturas_aprueba++;
                            } else {
                                $equiv_final = "NO APRUEBA";
                                if (existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
                                    // Obtencion de la calificacion del examen remedial
                                    $examen_de_gracia = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
                                    if ($examen_de_gracia >= 7) {
                                        $promedio_final = 7;
                                        $equiv_final = "APRUEBA";
                                        $num_asignaturas_aprueba++;
                                    } else {
                                        $equiv_final = "NO APRUEBA";
                                    }
                                }
                            }
                        }
                    }
                } else {
                    // Caso contrario se determina si debe dar examen remedial, considerando la fecha de cierre del examen supletorio
                    $fecha_actual = new DateTime("now");
                    $fecha_cierre = new DateTime(obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));
                    if ($fecha_actual < $fecha_cierre) {
                        $equiv_final = "SUPLETORIO";
                    } else {
                        $equiv_final = "REMEDIAL";
                        // Obtencion de la calificacion del examen remedial
                        $examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                        if ($examen_remedial >= 7) {
                            $promedio_final = 7;
                            $equiv_final = "APRUEBA";
                            $num_asignaturas_aprueba++;
                        } else {
                            $examen_de_gracia = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
                            if ($examen_de_gracia >= 7) {
                                $promedio_final = 7;
                                $equiv_final = "APRUEBA";
                                $num_asignaturas_aprueba++;
                            } else {
                                $equiv_final = "NO APRUEBA";
                            }
                        }
                    }
                }
            } else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
                $equiv_final = "REMEDIAL";
                if (existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
                    // Obtencion de la calificacion del examen remedial
                    $examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                    if ($examen_remedial >= 7) {
                        $promedio_final = 7;
                        $equiv_final = "APRUEBA";
                        $num_asignaturas_aprueba++;
                    } else {
                        $equiv_final = "NO APRUEBA";
                        if (existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo)) {
                            // Obtencion de la calificacion del examen remedial
                            $examen_de_gracia = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
                            if ($examen_de_gracia >= 7) {
                                $promedio_final = 7;
                                $equiv_final = "APRUEBA";
                                $num_asignaturas_aprueba++;
                            } else {
                                $equiv_final = "NO APRUEBA";
                            }
                        }
                    }
                } else {
                    // Caso contrario se determina si debe dar examen de gracia, considerando la fecha de cierre del examen remedial
                    $fecha_actual = new DateTime("now");
                    $fecha_cierre = new DateTime(obtenerFechaCierreSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo));
                    if ($fecha_actual < $fecha_cierre) {
                        $equiv_final = "REMEDIAL";
                    } else {
                        $equiv_final = "NO APRUEBA";
                    }
                }
            } else {
                $equiv_final = "NO APRUEBA";
            }

            if ($contador_sin_examen > 0 && $promedio_periodos > 0) $equiv_final = "SIN EXAMEN";
            if ($promedio_periodos == 0) $equiv_final = "SIN CALIFICACIONES";

            //Despliego la suma y promedio de los dos quimestres y demas informacion
            $cadena .= "<td>" . substr($suma_periodos, 0, strpos($suma_periodos, '.') + 3) . "</td>\n"; // Suma
            $cadena .= "<td>" . substr($promedio_periodos, 0, strpos($promedio_periodos, '.') + 3) . "</td>\n"; // Prom. Quim.
            $cadena .= "<td>" . number_format($examen_supletorio, 2) . "</td>\n"; // Supletorio
            $cadena .= "<td>" . number_format($examen_remedial, 2) . "</td>\n"; // Remedial
            $cadena .= "<td>" . number_format($examen_de_gracia, 2) . "</td>\n"; // Gracia
            $cadena .= "<td>" . number_format($promedio_final, 2) . "</td>\n"; // Promedio Final
            $cadena .= "<td width=\"*\" align=\"left\">$equiv_final</td>\n";
            $cadena .= "</tr>\n";
        }
    }
    if ($num_asignaturas_aprueba == $contador) {
        $cadena .= "<tr>\n";
        $cadena .= "<td colspan='11' align='center'>OBSERVACION GENERAL: APRUEBA EL PERIODO LECTIVO</td>";
        $cadena .= "</tr>";
    } else {
        $cadena .= "<tr>\n";
        $cadena .= "<td colspan='11' align='center'>OBSERVACION GENERAL: NO APRUEBA EL PERIODO LECTIVO</td>";
        $cadena .= "</tr>";
    } */

    $suma_promedio_asignaturas = 0;
    $contador_asignaturas = 0;

    while ($asignatura = $db->fetch_object($asignaturas)) {
        $contador_asignaturas++;
        $contador++;

        $id_asignatura = $asignatura->id_asignatura;
        $nom_asignatura = $asignatura->as_nombre;
        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>$nom_asignatura</td>\n";

        // Calcular las notas de bimestres, trimestres o quimestres
        $periodos_evaluacion = $db->consulta("SELECT pc.id_periodo_evaluacion, 
                                                     id_tipo_periodo, 
                                                     pc.pe_ponderacion 
                                                FROM sw_periodo_evaluacion pe, 
                                                     sw_periodo_evaluacion_curso pc 
                                               WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
                                                 AND pc.id_periodo_lectivo = $id_periodo_lectivo 
                                                 AND id_curso = $id_curso
                                                 AND id_tipo_periodo IN (1, 7, 8) 
                                               ORDER BY pc_orden ASC");

        $suma_ponderados = 0;

        while ($periodo = $db->fetch_object($periodos_evaluacion)) {
            $id_periodo_evaluacion = $periodo->id_periodo_evaluacion;
            $query = $db->consulta("SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
            $record = $db->fetch_object($query);
            $promedio_sub_periodo = $record->calificacion;
            $promedio_ponderado = $promedio_sub_periodo * $periodo->pe_ponderacion;
            $suma_ponderados += $promedio_ponderado;

            $promedio_sub_periodo = $promedio_sub_periodo == 0 ? "" : substr($promedio_sub_periodo, 0, strpos($promedio_sub_periodo, '.') + 3);

            $promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio_ponderado, '.') + 3);

            $cadena .= "<td>$promedio_sub_periodo</td>\n";
            $cadena .= "<td>$promedio_ponderado</td>\n";
        }

        $suma_ponderados = $suma_ponderados == 0 ? "" : substr($suma_ponderados, 0, strpos($suma_ponderados, '.') + 3);

        $cadena .= "<td>$suma_ponderados</td>\n";

        // Desplegar la equivalencia final (APRUEBA, NO APRUEBA, SUPLETORIO, REMEDIAL)

        $qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
        $query = $db->consulta($qry);
        $registro = $db->fetch_object($query);
        $nota_minima = $registro->pe_nota_aprobacion;

        $observacion = "";
        $total_registros = 0;

        $examen_supletorio = 0;
        $examen_remedial = 0;

        $puntaje_final = $suma_ponderados;

        if ($puntaje_final >= $nota_minima) {
            $observacion = "APRUEBA";
        } else {
            // Consultar si el estudiante tiene que dar exámenes supletorios o remediales
            $qry = "SELECT * 
                      FROM sw_equivalencia_supletorios 
                     WHERE id_periodo_lectivo = $id_periodo_lectivo 
                     ORDER BY rango_desde DESC";
            $query = $db->consulta($qry);
            $total_registros = $db->num_rows($query);

            while ($registro = $db->fetch_object($query)) {
                $rango_desde = $registro->rango_desde;
                $rango_hasta = $registro->rango_hasta;
                if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
                    $observacion = $registro->nombre_examen;
                }
            }
        }

        $cadena .= "<td align='left'>" . $observacion . "</td>\n";

        // Observación Final (Luego del periodo de evaluación de exámenes supletorios)

        $equiv_final = "";

        if ($observacion != "APRUEBA") {
            if ($observacion == "SUPLETORIO") {
                // Determinar la nota del examen supletorio

                $fecha_actual = new DateTime("now");
                $fecha_apertura = new DateTime(obtenerFechaAperturaSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo));

                if ($fecha_actual >= $fecha_apertura) {
                    $examen_supletorio = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);

                    if ($examen_supletorio >= 7) {
                        // equivalencia final cualitativa
                        $equiv_final = "APRUEBA";
                        $puntaje_final = 7;
                    } else {
                        if ($total_registros == 1) {
                            $equiv_final = "NO APRUEBA";
                        } else {
                            // Antigua LOEI
                            $examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

                            if ($examen_remedial >= 7) {
                                $equiv_final = "APRUEBA";
                                $puntaje_final = 7;
                            } else {
                                $equiv_final = "NO APRUEBA";
                            }
                        }
                    }
                }
            } else if ($observacion == "REMEDIAL") {
                // Antigua LOEI
                $examen_remedial = obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);

                if ($examen_remedial >= 7) {
                    $equiv_final = "APRUEBA";
                    $puntaje_final = 7;
                } else {
                    $equiv_final = "NO APRUEBA";
                }
            } else {
                // No alcanza el puntaje mínimo
                $equiv_final = "NO APRUEBA";
            }
        }

        $nota_supletorio = $examen_supletorio == 0 ? "" : substr($examen_supletorio, 0, strpos($examen_supletorio, '.') + 3);
        $nota_remedial = $examen_remedial == 0 ? "" : substr($examen_remedial, 0, strpos($examen_remedial, '.') + 3);

        if ($total_registros == 1) {
            $cadena .= "<td align='left'>" . $nota_supletorio . "</td>\n";
        } else {
            // Antigua LOEI
            $cadena .= "<td align='left'>" . $nota_supletorio . "</td>\n";
            $cadena .= "<td align='left'>" . $nota_remedial . "</td>\n";
        }

        $cadena .= "<td align='left'>" . $equiv_final . "</td>\n";
        $cadena .= "</tr>\n";

        $suma_promedio_asignaturas += $puntaje_final;
    }

    $promedio_general = $suma_promedio_asignaturas / $contador_asignaturas;

    $cadena .= "</tbody>\n";
    $cadena .= "</table>\n";
} else {
    $cadena .= "<p class='mensaje'>\n";
    $cadena .= "No se han definido las asignaturas asociadas a este paralelo...\n";
    $cadena .= "</p>\n";
}

$data = [
    "cadena" => $cadena,
    "promedio" => truncar($promedio_general, 2)
];

// echo $cadena;
echo json_encode($data);
