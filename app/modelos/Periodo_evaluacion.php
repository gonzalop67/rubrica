<?php
class Periodo_evaluacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerPeriodoEvaluacion($id)
    {
        $this->db->query("SELECT * FROM sw_sub_periodo_evaluacion WHERE id_sub_periodo_evaluacion = $id");
        return $this->db->registro();
    }

    public function obtenerPeriodosEvaluacion($id_periodo_lectivo)
    {
        $this->db->query("SELECT sp.* FROM sw_sub_periodo_evaluacion sp, sw_periodo_lectivo_sub_periodo ps WHERE sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion AND ps.id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registros();
    }

    public function obtenerPeriodosPrincipales($id_periodo_lectivo)
    {
        $this->db->query("SELECT sp.* FROM sw_sub_periodo_evaluacion sp, sw_periodo_lectivo_sub_periodo ps WHERE sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion AND id_tipo_periodo = 1 AND ps.id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registros();
    }

    public function obtenerPeriodosSupletorios($id_periodo_lectivo)
    {
        $this->db->query("SELECT sp.* FROM sw_periodo_evaluacion sp, sw_periodo_lectivo_sub_periodo ps WHERE id_tipo_periodo <> 1 AND ps.id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registros();
    }

    public function obtenerIdAporteEvaluacionSupRemGracia($id_sub_periodo_evaluacion)
    {
        $this->db->query("SELECT a.id_aporte_evaluacion FROM sw_aporte_evaluacion a, sw_sub_periodo_evaluacion p WHERE p.id_sub_periodo_evaluacion = a.id_sub_periodo_evaluacion AND p.id_sub_periodo_evaluacion = $id_sub_periodo_evaluacion");
        return json_encode($this->db->registro());
    }

    public function obtenerSubPeriodosEvaluacion()
    {
        $this->db->query("SELECT * FROM sw_sub_periodo_evaluacion ORDER BY pe_orden");
        return $this->db->registros();
    }

    public function existeNombrePeriodo($pe_nombre, $id_periodo_lectivo)
    {
        $query = "SELECT sp.id_sub_periodo_evaluacion "
            . " FROM `sw_sub_periodo_evaluacion` sp, "
            . " `sw_periodo_lectivo_sub_periodo` ps "
            . " WHERE sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion "
            . " AND `pe_nombre` = '" . $pe_nombre . "'"
            . " AND `id_periodo_lectivo` = $id_periodo_lectivo";

        $this->db->query($query);
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function existeAbreviaturaPeriodo($pe_abreviatura, $id_periodo_lectivo)
    {
        $query = "SELECT sp.id_sub_periodo_evaluacion "
            . " FROM `sw_sub_periodo_evaluacion` sp, "
            . " `sw_periodo_lectivo_sub_periodo` ps "
            . " WHERE sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion "
            . " AND `pe_abreviatura` = '" . $pe_abreviatura . "'"
            . " AND ps.`id_periodo_lectivo` = $id_periodo_lectivo";

        $this->db->query($query);
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function mostrarTitulosPeriodos($datos)
    {
        // Tipo de periodo (2: supletorio; 3: remedial; 4: de gracia)

        $mensaje = "<table class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
        $mensaje .= "<tr class=\"cabeceraTabla\">\n";
        $mensaje .= "<td width=\"5%\">Nro.</td>\n";
        $mensaje .= "<td>Id.</td>\n";
        $mensaje .= "<td>N&oacute;mina</td>\n";

        //Aqui despliego las abreviaturas de los periodos principales

        $this->db->query("SELECT pe_abreviatura FROM sw_sub_periodo_evaluacion sp, sw_periodo_lectivo_sub_periodo ps WHERE sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion AND id_tipo_periodo = 1 AND id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $datos['id_periodo_lectivo']);

        $result = $this->db->registros();

        if ($this->db->rowCount() > 0) {
            foreach ($result as $titulo_periodo) {
                $mensaje .= "<td width=\"60px\" align=\"" . $datos['alineacion'] . "\">" . $titulo_periodo->pe_abreviatura . "</td>\n";
            }
        }

        $mensaje .= "</tr>\n";
        $mensaje .= "</table>\n";

        return "$mensaje\n";
    }

    public function obtenerAbreviaturasPeriodos($id_periodo_lectivo)
    {
        // Recuperar los titulares de los periodos quimestrales
        $this->db->query("SELECT pe_abreviatura FROM sw_sub_periodo_evaluacion sp, sw_periodo_lectivo_sub_periodo ps WHERE sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion AND id_tipo_periodo = 1 AND id_periodo_lectivo = $id_periodo_lectivo");

        return $this->db->registros();
    }

    public function obtenerDetallePeriodoEvaluacion($id_paralelo, $id_asignatura, $id_sub_periodo_evaluacion)
    {
        //Cabecera de la tabla
        $cadena = "<table class=\"table table-striped table-hover fuente9\">\n";
        $cadena .= "<thead class=\"thead-dark fuente9\">\n";
        $cadena .= "<tr>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Id</th>\n";
        $cadena .= "<th>Nómina</th>\n";

        //Cabeceras de los parciales...
        $this->db->query("SELECT ap_abreviatura 
                            FROM sw_aporte_evaluacion 
                           WHERE id_sub_periodo_evaluacion = $id_sub_periodo_evaluacion
                        ");
        $registros = $this->db->registros();
        $num_total_registros = $this->db->rowCount();
        $contador_aportes = 0;

        foreach ($registros as $titulo_aporte) {
            $contador_aportes++;
            if ($contador_aportes < $num_total_registros)
                $cadena .= "<th>" . $titulo_aporte->ap_abreviatura . "</th>\n";
            else {
                $cadena .= "<th>PROM.</th>\n";
                $cadena .= "<th>80%</th>\n";
                $cadena .= "<th>" . $titulo_aporte->ap_abreviatura . "</th>\n";
                $cadena .= "<th>20%</th>\n";
                $this->db->query("SELECT pe_abreviatura FROM sw_sub_periodo_evaluacion WHERE id_sub_periodo_evaluacion = $id_sub_periodo_evaluacion");
                $record = $this->db->registro();
                $cadena .= "<th>" . $record->pe_abreviatura . "</th>\n";
            }
        }

        $num_cols = $contador_aportes + 7;

        $cadena .= "</thead>\n";
        $cadena .= "<tbody>\n";

        //Aqui van las calificaciones de cada estudiante...
        $this->db->query("SELECT e.id_estudiante, 
                                 c.id_curso, 
                                 di.id_paralelo, 
                                 di.id_asignatura, 
                                 e.es_apellidos, 
                                 e.es_nombres, 
                                 as_nombre, 
                                 cu_nombre, 
                                 pa_nombre,
                                 id_tipo_asignatura 
                            FROM sw_distributivo di, 
                                 sw_estudiante_periodo_lectivo ep, 
                                 sw_estudiante e, 
                                 sw_asignatura a, 
                                 sw_curso c, 
                                 sw_paralelo p 
                           WHERE di.id_paralelo = ep.id_paralelo 
                             AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
                             AND ep.id_estudiante = e.id_estudiante 
                             AND di.id_asignatura = a.id_asignatura 
                             AND di.id_paralelo = p.id_paralelo 
                             AND p.id_curso = c.id_curso 
                             AND di.id_paralelo = $id_paralelo
                             AND di.id_asignatura = $id_asignatura
                             AND es_retirado <> 'S'
                             AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
        $registros = $this->db->registros();
        $num_total_registros = $this->db->rowCount();

        if ($num_total_registros > 0) {
            $contador = 0;
            foreach ($registros as $row) {
                $contador++;
                $id_estudiante = $row->id_estudiante;
                $apellidos = $row->es_apellidos;
                $nombres = $row->es_nombres;
                $id_curso = $row->id_curso;
                $id_paralelo = $row->id_paralelo;
                $id_asignatura = $row->id_asignatura;
                $cadena .= "<tr>\n";
                $cadena .= "<td class=\"text-left\">$contador</td>\n";
                $cadena .= "<td class=\"text-left\">$id_estudiante</td>\n";
                $cadena .= "<td class=\"text-left\">" . $apellidos . " " . $nombres . "</td>\n";
                // Aqui se calculan los promedios de cada aporte de evaluacion
                $this->db->query("SELECT a.id_aporte_evaluacion,
                                         a.id_tipo_aporte, 
										 ac.ap_estado 
									FROM sw_sub_periodo_evaluacion sp, 
										 sw_aporte_evaluacion a, 
										 sw_aporte_paralelo_cierre ac 
								   WHERE sp.id_sub_periodo_evaluacion = a.id_sub_periodo_evaluacion 
									 AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
									 AND p.id_sub_periodo_evaluacion = $id_sub_periodo_evaluacion 
									 AND ac.id_paralelo = $id_paralelo");
                $aportes = $this->db->registros();
                $num_total_registros = $this->db->rowCount();
                if ($num_total_registros > 0) {
                    // Aqui calculo los promedios y desplegar en la tabla
                    $contador_aportes = 0;
                    $suma_promedios = 0;
                    foreach ($aportes as $aporte) {
                        $contador_aportes++;
                        $tipo_aporte = $aporte->id_tipo_aporte;
                        $this->db->query("SELECT id_rubrica_evaluacion 
                                            FROM sw_rubrica_evaluacion r,
                                                 sw_asignatura a
                                           WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
                                             AND a.id_asignatura = $id_asignatura
                                             AND id_aporte_evaluacion = " . $aporte->id_aporte_evaluacion);
                        $rubricas = $this->db->registros();
                        $total_rubricas = $this->db->rowCount();
                        if ($total_rubricas > 0) {
                            $suma_rubricas = 0;
                            $contador_rubricas = 0;
                            foreach ($rubricas as $rubrica) {
                                $contador_rubricas++;
                                $id_rubrica_evaluacion = $rubrica->id_rubrica_evaluacion;
                                $this->db->query("SELECT re_calificacion 
                                                    FROM sw_rubrica_estudiante 
                                                   WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
                                $rubrica_estudiante = $this->db->registro();
                                $total_registros = $this->db->rowCount();
                                if ($total_registros > 0) {
                                    $calificacion = $rubrica_estudiante->re_calificacion;
                                } else {
                                    $calificacion = 0;
                                }
                                $suma_rubricas += $calificacion;
                            }
                        }
                        //$promedio = $this->truncar($suma_rubricas / $contador_rubricas,2);
                        $promedio = $suma_rubricas / $contador_rubricas;
                        if ($contador_aportes < $num_total_registros) {
                            if ($tipo_aporte == 1)
                                $cadena .= "<td class=\"text-left\">" . substr($promedio, 0, strpos($promedio, '.') + 3) . "</td>";
                            $suma_promedios += $promedio;
                        } else {
                            $examen_quimestral = $promedio;
                        }
                    }
                    // Aqui debo calcular el ponderado de los promedios parciales
                    $promedio_aportes = $suma_promedios / ($contador_aportes - 1);
                    $ponderado_aportes = 0.8 * $promedio_aportes;
                    $ponderado_examen = 0.2 * $examen_quimestral;
                    $calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
                    $cadena .= "<td class=\"text-left\">" . substr($promedio_aportes, 0, strpos($promedio_aportes, '.') + 3) . "</td>";
                    $cadena .= "<td class=\"text-left\">" . substr($ponderado_aportes, 0, strpos($ponderado_aportes, '.') + 4) . "</td>";
                    $examen_quimestral = ($examen_quimestral == 0) ? "" : number_format($examen_quimestral, 2);
                    $cadena .= "<td class=\"text-left\">" . $examen_quimestral . "</td>";
                    $cadena .= "<td class=\"text-left\">" . substr($ponderado_examen, 0, strpos($ponderado_examen, '.') + 4) . "</td>";
                    $cadena .= "<td class=\"text-left\">" . substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3) . "</td>";
                }
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td class=\"text-center\" colspan=\"$num_cols\">";
            $cadena .= "No se han encontrado estudiantes...";
            $cadena .= "</td>\n";
            $cadena .= "</tr>\n";
        }

        $cadena .= "</tbody>\n";
        $cadena .= "</table>\n";

        return $cadena;
    }

    public function insertarPeriodoEvaluacion($datos)
    {
        $this->db->query('INSERT INTO sw_sub_periodo_evaluacion (id_tipo_periodo, pe_nombre, pe_abreviatura) VALUES (:id_tipo_periodo, :pe_nombre, :pe_abreviatura)');

        //Vincular valores
        $this->db->bind(':id_tipo_periodo', $datos['id_tipo_periodo']);
        $this->db->bind(':pe_nombre', $datos['pe_nombre']);
        $this->db->bind(':pe_abreviatura', $datos['pe_abreviatura']);

        return $this->db->execute();
    }

    public function actualizarPeriodoEvaluacion($datos)
    {
        $this->db->query('UPDATE sw_sub_periodo_evaluacion SET id_tipo_periodo = :id_tipo_periodo, pe_nombre = :pe_nombre, pe_abreviatura = :pe_abreviatura WHERE id_sub_periodo_evaluacion = :id_sub_periodo_evaluacion');

        //Vincular valores
        $this->db->bind(':id_sub_periodo_evaluacion', $datos['id_sub_periodo_evaluacion']);
        $this->db->bind(':id_tipo_periodo', $datos['id_tipo_periodo']);
        $this->db->bind(':pe_nombre', $datos['pe_nombre']);
        $this->db->bind(':pe_abreviatura', $datos['pe_abreviatura']);

        return $this->db->execute();
    }

    public function eliminarPeriodoEvaluacion($id)
    {
        $this->db->query('DELETE FROM `sw_sub_periodo_evaluacion` WHERE `id_sub_periodo_evaluacion` = :id_sub_periodo_evaluacion');

        //Vincular valores
        $this->db->bind(':id_sub_periodo_evaluacion', $id);

        return $this->db->execute();
    }

    public function actualizarOrden($id_sub_periodo_evaluacion, $pe_orden)
    {
        $this->db->query('UPDATE `sw_sub_periodo_evaluacion` SET `pe_orden` = :pe_orden WHERE `id_sub_periodo_evaluacion` = :id_sub_periodo_evaluacion');

        //Vincular valores
        $this->db->bind(':id_sub_periodo_evaluacion', $id_sub_periodo_evaluacion);
        $this->db->bind(':pe_orden', $pe_orden);

        echo $this->db->execute();
    }
}
