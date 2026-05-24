<?php
class Paralelo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function getCursoId($id_paralelo)
    {
        $this->db->query("SELECT id_curso 
                            FROM sw_paralelo
                           WHERE id_paralelo = $id_paralelo");

        return $this->db->registro()->id_curso;
    }

    public function obtenerParalelo($id_paralelo)
    {
        $this->db->query("SELECT * FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
        return $this->db->registro();
    }

    public function obtenerParalelos($id_periodo_lectivo)
    {
        $this->db->query("
            SELECT p.*, 
                   cu_nombre,
                   cu_abreviatura, 
                   es_figura, 
                   es_abreviatura, 
                   jo_nombre 
              FROM sw_paralelo p,
                   sw_curso_subnivel cs,
                   sw_curso c, 
                   sw_especialidad e, 
                   sw_jornada j  
             WHERE cs.id_curso_subnivel = p.curso_subnivel_id
               AND c.id_curso = cs.curso_id  
               AND e.id_especialidad = cs.especialidad_id 
               AND j.id_jornada = p.id_jornada 
               AND p.id_periodo_lectivo = $id_periodo_lectivo 
             ORDER BY pa_orden              
        ");

        return $this->db->registros();
    }

    public function obtenerParalelosDocente($id_usuario, $id_periodo_lectivo)
    {
        $this->db->query("SELECT DISTINCT(p.id_paralelo),
                                 pa_nombre,
                                 cu_abreviatura,
                                 es_abreviatura, 
                                 pa_orden 
                            FROM sw_distributivo d, 
                                 sw_paralelo p, 
                                 sw_curso c,
                                 sw_especialidad e 
                           WHERE e.id_especialidad = c.id_especialidad
                             AND c.id_curso = p.id_curso 
                             AND p.id_paralelo = d.id_paralelo 
                             AND d.id_usuario = $id_usuario
                             AND d.id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY pa_orden");

        return $this->db->registros();
    }

    public function existeNombreParalelo($pa_nombre, $id_curso, $id_jornada)
    {
        $this->db->query("SELECT id_paralelo 
                            FROM sw_paralelo 
                           WHERE pa_nombre = '$pa_nombre' 
                           AND id_curso = $id_curso 
                           AND id_jornada = $id_jornada");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function contarEstudiantesPorGenero($id_paralelo)
    {
        $this->db->query("SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 GROUP BY id_def_genero ORDER BY id_def_genero");
        $registros = $this->db->registros();
        $cadena = "";
        $suma_generos = 0;
        if (count($registros) > 0) {
            foreach ($registros as $registro) {
                $genero = $registro->id_def_genero;
                $numero = $registro->numero ?? 0;
                if ($genero == 1) {
                    $cadena .= "&nbsp;Mujeres: " . $numero . ", ";
                } else {
                    $cadena .= "Hombres: " . $numero;
                }
                $suma_generos += $numero;
            }
            return $cadena . " - Total estudiantes: " . $suma_generos;
        } else {
            return "No se han matriculado estudiantes todavía...";
        }
    }

    public function exportar($id_paralelo)
    {
        header('Content-Type: text/csv; charset=utf-8');
        $fn = "csv_" . uniqid() . ".csv";
        header('Content-Disposition: attachment; filename=' . $fn);
        $output = fopen("php://output", "w");
        fputcsv($output, array('td_nombre', 'es_cedula', 'dn_nombre', 'es_apellidos', 'es_nombres', 'dg_nombre', 'es_email', 'es_sector', 'es_direccion', 'es_telefono', 'es_fec_nacim'));

        $query = "SELECT td_nombre, es_cedula, dn_nombre, es_apellidos, es_nombres, dg_nombre, es_email, es_sector, es_direccion, es_telefono, es_fec_nacim FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep, sw_def_genero dg, sw_tipo_documento td, sw_def_nacionalidad dn WHERE e.id_estudiante = ep.id_estudiante AND dg.id_def_genero = e.id_def_genero AND td.id_tipo_documento = e.id_tipo_documento AND dn.id_def_nacionalidad = e.id_def_nacionalidad AND activo = 1 and id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC";

        $this->db->query($query);

        $result = $this->db->registrosAssoc();

        foreach ($result as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }

    public function obtenerNombreParalelo($id_paralelo)
    {
        $this->db->query("SELECT es_figura, 
								 cu_nombre, 
								 pa_nombre, 
								 jo_nombre 
							FROM sw_especialidad es, 
								 sw_curso cu, 
								 sw_paralelo pa, 
								 sw_jornada jo
						   WHERE pa.id_curso = cu.id_curso 
							 AND cu.id_especialidad = es.id_especialidad 
							 AND jo.id_jornada = pa.id_jornada 
							 AND pa.id_paralelo = $id_paralelo");
        $paralelo = $this->db->registro();
        return $paralelo->cu_nombre . " \"" . $paralelo->pa_nombre . "\" " . $paralelo->es_figura . " - " . $paralelo->jo_nombre;
    }

    public function contarEstudiantesParalelo($id_paralelo)
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $this->db->query("SELECT COUNT(*) AS num_registros FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo AND id_paralelo = $id_paralelo AND activo = 1 AND es_retirado = 'N'");
        return $this->db->registro()->num_registros;
    }

    public function listarEstudiantesParalelo($id_paralelo, $id_asignatura, $id_aporte_evaluacion, $id_curso)
    {
        $this->db->query("SELECT e.id_estudiante, 
								 c.id_curso, 
								 d.id_paralelo, 
								 d.id_asignatura, 
								 e.es_apellidos, 
								 e.es_nombres, 
								 es_retirado, 
								 as_nombre, 
								 cu_nombre, 
								 pa_nombre,
								 id_tipo_asignatura 
							FROM sw_distributivo d, 
								 sw_estudiante_periodo_lectivo ep, 
								 sw_estudiante e, 
								 sw_asignatura a, 
								 sw_curso c, 
								 sw_paralelo p 
						   WHERE d.id_paralelo = ep.id_paralelo 
							 AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
							 AND ep.id_estudiante = e.id_estudiante 
							 AND d.id_asignatura = a.id_asignatura 
							 AND d.id_paralelo = p.id_paralelo 
							 AND p.id_curso = c.id_curso 
							 AND d.id_paralelo = $id_paralelo 
						     AND d.id_asignatura = $id_asignatura 
						     AND es_retirado <> 'S' 
							 AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
        $registros = $this->db->registros();
        $num_estudiantes = $this->db->rowCount();

        $cadena = "<table class='table fuente8'>\n";
        $cadena .= "<thead class='thead-dark fuente9'>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Id.</th>\n";
        $cadena .= "<th>Nómina</th>\n";
        // Acá recupero las abreviaturas de los insumos de calificación
        $this->db->query("SELECT ru_abreviatura,
								 ru_nombre
							FROM sw_rubrica_evaluacion
						   WHERE id_tipo_asignatura = 1
							 AND id_aporte_evaluacion = $id_aporte_evaluacion");
        $rubricas = $this->db->registros();
        $num_registros = $this->db->rowCount();
        foreach ($rubricas as $titulo_rubrica) {
            $cadena .= "<th>$titulo_rubrica->ru_abreviatura</th>\n";
        }
        $cadena .= "<th>PROM</th>\n";
        $num_columnas = 4 + $num_registros;
        $this->db->query("SELECT quien_inserta_comp FROM sw_curso WHERE id_curso = $id_curso");
        $resultado = $this->db->registro();
        if ($resultado->quien_inserta_comp == 0) {
            $cadena .= "<th>COMP</th>\n";
            $num_columnas++;
        }
        $cadena .= "</thead>\n";
        $cadena .= "<tbody>\n";
        $contador = 0;
        if ($num_estudiantes > 0) {
            foreach ($registros as $registro) {
                $contador++;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $cadena .= "<td width='40px' align='left'>$contador</td>\n";
                $cadena .= "<td width='40px' align='left'>$registro->id_estudiante</td>\n";
                $cadena .= "<td width='360px' align='left'>$registro->es_apellidos $registro->es_nombres</td>\n";
                //Aca vamos a obtener el estado del aporte de evaluacion
                $this->db->query("SELECT ac.ap_estado, 
                                     quien_inserta_comp
                                FROM sw_aporte_evaluacion a,
                                     sw_aporte_paralelo_cierre ac,
                                     sw_curso c,
                                     sw_paralelo p
                               WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
                                 AND c.id_curso = p.id_curso
                                 AND ac.id_paralelo = p.id_paralelo
                                 AND p.id_paralelo = $id_paralelo 
                                 AND a.id_aporte_evaluacion = $id_aporte_evaluacion");
                $resultado = $this->db->registro();
                $estado_aporte = $resultado->ap_estado;
                $quien_inserta_comportamiento = $resultado->quien_inserta_comp;
                //Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
                if ($registro->id_tipo_asignatura == 1) { //CUANTITATIVA
                    // Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
                    $this->db->query("SELECT id_rubrica_evaluacion, 
                                             id_tipo_aporte, 
                                             ac.ap_estado 
                                        FROM sw_rubrica_evaluacion r, 
                                             sw_aporte_evaluacion a, 
                                             sw_aporte_paralelo_cierre ac,
                                             sw_asignatura asignatura
                                       WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
                                         AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
                                         AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
                                         AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
                                         AND asignatura.id_asignatura = $id_asignatura
                                         AND r.id_aporte_evaluacion = $id_aporte_evaluacion
                                         AND ac.id_paralelo = $id_paralelo");
                    $rubricas = $this->db->registros();
                    if ($this->db->rowCount() > 0) {
                        $suma_rubricas = 0;
                        $contador_rubricas = 0;
                        foreach ($rubricas as $rubrica) {
                            $contador_rubricas++;
                            $id_rubrica_evaluacion = $rubrica->id_rubrica_evaluacion;
                            $tipo_aporte = $rubrica->id_tipo_aporte;
                            $estado_aporte = $rubrica->ap_estado;
                            // Obtener la calificación si existe
                            $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $registro->id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
                            $rubrica_estudiante = $this->db->registro();
                            if ($this->db->rowCount() > 0) {
                                $calificacion = $rubrica_estudiante->re_calificacion;
                            } else {
                                $calificacion = 0;
                            }
                            $suma_rubricas += $calificacion;
                            $calificacion = $calificacion == 0 ? "" : $calificacion;
                            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_" . $id_rubrica_evaluacion . "_" . $registro->id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $tipo_aporte . "_" . $contador . "\" class=\"inputPequenio nota" . $contador_rubricas . "\" value=\"" . $calificacion . "\"";
                            if ($estado_aporte == 'A' && $registro->es_retirado == 'N') {
                                $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\"  onblur=\"editarCalificacion(this," . $registro->id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $calificacion . ")\" /></td>\n";
                            } else {
                                $cadena .= " disabled /></td>\n";
                            }
                        }
                        $promedio = $suma_rubricas / $contador_rubricas;
                        $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
                        $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio promedio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
                    } else {
                        $cadena .= "<tr>\n";
                        $cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
                        $cadena .= "</tr>\n";
                    }
                } else { //CUALITATIVA
                    // Aqui va el codigo para obtener la calificacion cualitativa
                    $this->db->query("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = $registro->id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion");
                    $cualitativa = $this->db->registro();
                    if ($this->db->rowCount() > 0) {
                        $calificacion = $cualitativa->rc_calificacion;
                    } else {
                        $calificacion = " ";
                    }
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"cualitativa_" . $registro->id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $id_aporte_evaluacion . "_" . $contador . "\" class=\"inputPequenio nota1\" value=\"" . $calificacion . "\"";
                    if ($estado_aporte == 'A' && $registro->es_retirado == 'N') {
                        $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this," . $registro->id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ",'" . $calificacion . "')\" /></td>\n";
                    } else {
                        $cadena .= " disabled /></td>\n";
                    }
                }

                if ($quien_inserta_comportamiento == 0) { // Ingresan los docentes
                    // Aqui va el codigo para obtener el comportamiento
                    $this->db->query("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $registro->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
                    $comportamiento = $this->db->registro();
                    if ($this->db->rowCount() > 0) {
                        $calificacion = $comportamiento->co_cualitativa;
                    } else {
                        $calificacion = '';
                    }
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"comportamiento_" . $contador . "\" class=\"inputPequenio\" value=\"" . $calificacion . "\"";
                    if ($estado_aporte == 'A' && $registro->es_retirado == 'N') {
                        $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionComportamiento(this," . $registro->id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_aporte_evaluacion . ")\" /></td>\n";
                    } else {
                        $cadena .= " disabled /></td>\n";
                    }
                }

                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan=\"$num_columnas\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td>\n";
            $cadena .= "</tr>\n";
        }
        $cadena .= "</tbody>\n";
        $cadena .= "</table>\n";
        return $cadena;
    }

    public function listarCalificacionesParalelo($id_periodo_evaluacion, $id_paralelo, $id_asignatura, $tipo_reporte)
    {
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
        $estudiantes = $this->db->registros();
        $cadena = "<table class='table fuente8'>\n";
        $cadena .= "<thead class='thead-dark fuente9'>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Id.</th>\n";
        $cadena .= "<th>Nómina</th>\n";
        // Recuperar los titulares de los parciales
        $this->db->query("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $id_periodo_evaluacion);
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
                $cadena .= "<th>NOTA Q.</th>\n";
            }
        }
        $cadena .= "</thead>\n";
        // Ahora sí nos dedicamos al cuerpo de la tabla
        $cadena .= "<tbody>\n";
        // Cuerpo de la tabla
        if ($this->db->rowCount() > 0) {
            $contador = 0;
            foreach ($estudiantes as $estudiante) {
                $contador++;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $id_estudiante = $estudiante->id_estudiante;
                $apellidos = $estudiante->es_apellidos;
                $nombres = $estudiante->es_nombres;
                $cadena .= "<td width='40px' align='left'>$contador</td>\n";
                $cadena .= "<td width='40px' align='left'>$id_estudiante</td>\n";
                $cadena .= "<td width='360px' align='left'>" . $apellidos . " " . $nombres . "</td>\n";
                // Aqui se calculan los promedios de cada aporte de evaluacion
                $this->db->query("SELECT a.id_aporte_evaluacion, 
                                         id_tipo_aporte, 
                                         ac.ap_estado 
                                    FROM sw_periodo_evaluacion p, 
                                         sw_aporte_evaluacion a, 
                                         sw_aporte_paralelo_cierre ac 
                                   WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
                                     AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
                                     AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
                                     AND ac.id_paralelo = $id_paralelo");
                $aportes_evaluacion = $this->db->registros();
                // Aqui calculo los promedios y desplegar en la tabla
                $suma_aportes = 0;
                $contador_aportes = 0;
                $suma_promedios = 0;
                foreach ($aportes_evaluacion as $aporte) {
                    $contador_aportes++;
                    $tipo_aporte = $aporte->id_tipo_aporte;
                    $estado_aporte = $aporte->ap_estado;
                    $this->db->query("SELECT id_rubrica_evaluacion 
										FROM sw_rubrica_evaluacion r,
											 sw_asignatura a
									   WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
										 AND a.id_asignatura = $id_asignatura
										 AND id_aporte_evaluacion = " . $aporte->id_aporte_evaluacion);
                    $rubricas_evaluacion = $this->db->registros();
                    $suma_rubricas = 0;
                    $contador_rubricas = 0;
                    foreach ($rubricas_evaluacion as $rubrica) {
                        $contador_rubricas++;
                        $id_rubrica_evaluacion = $rubrica->id_rubrica_evaluacion;
                        $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $estudiante->id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
                        $registro = $this->db->registro();
                        if ($this->db->rowCount() > 0) {
                            $calificacion = $registro->re_calificacion;
                        } else {
                            $calificacion = 0;
                        }
                        $suma_rubricas += $calificacion;
                    }
                    $promedio = $suma_rubricas / $contador_rubricas;

                    if ($contador_aportes < $num_total_registros) {
                        $suma_promedios += $promedio;
                        $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
                        if ($tipo_reporte == 1)
                            $cadena .= "<td width=\"60px\" align=\"right\">" . $promedio . "</td>";
                        else
                            $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
                    } else {
                        $examen_quimestral = $promedio;
                    }
                }
                // Aqui debo calcular el ponderado de los promedios parciales
                $promedio_aportes = $suma_promedios / ($contador_aportes - 1);
                $ponderado_aportes = 0.8 * $promedio_aportes;
                $ponderado_examen = 0.2 * $examen_quimestral;
                $calificacion_quimestral = $ponderado_aportes + $ponderado_examen;

                $promedio_aportes = $promedio_aportes == 0 ? "" : substr($promedio_aportes, 0, strpos($promedio_aportes, '.') + 3);

                $ponderado_aportes = $ponderado_aportes == 0 ? "" : substr($ponderado_aportes, 0, strpos($ponderado_aportes, '.') + 4);

                $examen_quimestral = ($examen_quimestral == 0) ? "" : $examen_quimestral;

                $ponderado_examen = $ponderado_examen == 0 ? "" : substr($ponderado_examen, 0, strpos($ponderado_examen, '.') + 4);

                $calificacion_quimestral = $calificacion_quimestral == 0 ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

                if ($tipo_reporte == 1) {
                    $cadena .= "<td width=\"60px\" align=\"right\">" . $promedio_aportes . "</td>";
                    $cadena .= "<td width=\"60px\" align=\"right\">" . $ponderado_aportes . "</td>";
                    $cadena .= "<td width=\"60px\" align=\"right\">" . $examen_quimestral . "</td>";
                    $cadena .= "<td width=\"60px\" align=\"right\">" . $ponderado_examen . "</td>";
                    $cadena .= "<td width=\"60px\" align=\"right\">" . $calificacion_quimestral . "</td>";
                } else {
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedioaportes_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $promedio_aportes . "\" style=\"color:#666;\" /></td>\n";
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_aportes . "\" style=\"color:#666;\" /></td>\n";
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1\" id=\"examenquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $examen_quimestral . "\"";
                    if ($estado_aporte == 'A') {
                        $cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $examen_quimestral . ")\" /></td>\n";
                    } else {
                        $cadena .= " disabled /></td>\n";
                    }
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_examen . "\" style=\"color:#666;\" /></td>\n";
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $calificacion_quimestral . "\" style=\"color:#666;\" /></td>\n";
                }

                $cadena .= "</tr>\n";
            }
        }
        $cadena .= "</tbody>\n";
        $cadena .= "</table>\n";
        return $cadena;
    }

    public function listarCalificacionesSupletoriosParalelo($id_paralelo, $id_asignatura, $id_periodo_lectivo, $id_tipo_periodo, $id_periodo_evaluacion)
    {
        //Primero elimino los datos anteriores
        $this->db->query('DELETE FROM sw_promedio_anual WHERE id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura');

        //Vincular valores
        $this->db->bind(':id_paralelo', $id_paralelo);
        $this->db->bind(':id_asignatura', $id_asignatura);

        $this->db->execute();

        $this->db->query("CALL sp_calcular_promedio_anual($id_periodo_lectivo, $id_asignatura, $id_paralelo)");
        $this->db->execute();

        $this->db->query("SELECT pa.* FROM `sw_promedio_anual` pa, `sw_estudiante` es  WHERE `id_paralelo` = :id_paralelo AND id_asignatura = :id_asignatura AND es.id_estudiante = pa.id_estudiante ORDER BY es.es_apellidos, es.es_nombres");

        //Vincular valores
        $this->db->bind(':id_paralelo', $id_paralelo);
        $this->db->bind(':id_asignatura', $id_asignatura);

        $registros = $this->db->registros();
        // $num_total_registros = $this->db->rowCount();

        $cadena = "<table class='table fuente8'>\n";
        $cadena .= "<thead class='thead-dark fuente9'>\n";
        $cadena .= "<th>Nro.</th>\n";
        $cadena .= "<th>Id</th>\n";
        $cadena .= "<th>Nómina</th>\n";

        // Recuperar los titulares de los periodos quimestrales
        $this->db->query("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo = 1 AND id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);

        $result = $this->db->registros();
        $contador_abreviaturas = 0;

        foreach ($result as $titulo_periodo) {
            $contador_abreviaturas++;
            $cadena .= "<th>" . $titulo_periodo->pe_abreviatura . "</th>\n";
        }

        $cadena .= "<th>SUMA</th>\n";
        $cadena .= "<th>PROM</th>\n";

        // Consulto la abreviatura del examen supletorio/remedial/gracia según el tipo de periodo (2: supletorio; 3: remedial; 4: de gracia)
        $this->db->query("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo = " . $id_tipo_periodo . " AND id_periodo_lectivo = " . $id_periodo_lectivo);
        $result = $this->db->registro();

        $cadena .= "<th>" . $result->pe_abreviatura . "</th>\n";

        $cadena .= "<th>SUMA</th>\n";
        $cadena .= "<th>PROM</th>\n";
        $cadena .= "<th>OBSERVACION</th>\n";
        $cadena .= "</thead>\n";

        $cadena .= "<tbody>\n";

        $this->db->query("SELECT * FROM sw_config_rangos_supletorios WHERE id_periodo_evaluacion = " . $id_periodo_evaluacion . " AND id_periodo_lectivo = " . $id_periodo_lectivo);

        $result = $this->db->registro();

        $nota_desde = $result->nota_desde;
        $nota_hasta = $result->nota_hasta;

        //Los registros de los estudiantes que se quedaron a supletorio/remedial
        //Primero elimino los datos anteriores
        $this->db->query('DELETE FROM sw_supletorios_temp WHERE id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_tipo_periodo = :id_tipo_periodo');

        //Vincular valores
        $this->db->bind(':id_paralelo', $id_paralelo);
        $this->db->bind(':id_asignatura', $id_asignatura);
        $this->db->bind(':id_tipo_periodo', $id_tipo_periodo);

        $this->db->execute();

        $contador = 0;

        foreach ($registros as $registro) {
            $id_estudiante = $registro->id_estudiante;
            $promedio_anual = $registro->promedio_anual;

            $aprueba = true;

            // Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
            if ($promedio_anual <= $nota_hasta && $promedio_anual >= $nota_desde) {
                $aprueba = false;
            } else if ($id_tipo_periodo == 3) {
                // Se trata del examen remedial
                // Comprobar si da examen supletorio y si aprueba o no
                $tipo_periodo = $id_tipo_periodo - 1;
                // Recuperar el id_periodo_evaluacion del examen supletorio
                $this->db->query("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_tipo_periodo = $tipo_periodo AND id_periodo_lectivo = $id_periodo_lectivo");
                $result = $this->db->registro();
                $id_periodo_evaluacion_supletorio = $result->id_periodo_evaluacion;

                $this->db->query("SELECT * FROM sw_config_rangos_supletorios WHERE id_periodo_evaluacion = " . $id_periodo_evaluacion_supletorio . " AND id_periodo_lectivo = " . $id_periodo_lectivo);

                $result = $this->db->registro();
                // Recuperamos el rango de calificaciones para el supletorio
                $nota_desde_supletorio = $result->nota_desde;
                $nota_hasta_supletorio = $result->nota_hasta;

                // Verificar si tiene que dar el examen supletorio
                if ($promedio_anual <= $nota_hasta_supletorio && $promedio_anual >= $nota_desde_supletorio) {
                    // Verificar si ha rendido el examen supletorio
                    $this->db->query("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $tipo_periodo");
                    $result = $this->db->registro();
                    $id_rubrica_personalizada = $result->id_rubrica_evaluacion;

                    $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
                    $result = $this->db->registro();
                    $num_total_registros = $this->db->rowCount();
                    if ($num_total_registros > 0) {
                        $calificacion = $result->re_calificacion;
                    } else {
                        $calificacion = 0;
                    }

                    $aprueba = $calificacion >= 7;
                }
            }

            if (!$aprueba) {
                $contador++;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $cadena .= "<td width=\"5%\">$contador</td>\n";
                $cadena .= "<td width=\"5%\">$id_estudiante</td>\n";

                $this->db->query("SELECT * FROM `sw_estudiante` WHERE `id_estudiante` = :id_estudiante");

                //Vincular valores
                $this->db->bind(':id_estudiante', $id_estudiante);

                $infoEstudiante = $this->db->registro();
                $nombreEstudiante = $infoEstudiante->es_apellidos . " " . $infoEstudiante->es_nombres;
                $cadena .= "<td width=\"30%\" align=\"left\">$nombreEstudiante</td>\n";

                $this->db->query("SELECT id_periodo_evaluacion 
                                    FROM sw_periodo_evaluacion 
                                   WHERE id_periodo_lectivo = $id_periodo_lectivo 
                                     AND id_tipo_periodo = 1");

                $periodos_evaluacion = $this->db->registros();

                $suma_periodos = 0;
                $contador_periodos = 0;
                foreach ($periodos_evaluacion as $periodo_evaluacion) {
                    $contador_periodos++;
                    $id_periodo_evaluacion = $periodo_evaluacion->id_periodo_evaluacion;

                    // Aqui voy a llamar a la funcion almacenada para obtener la calificacion quimestral correspondiente
                    $qry = "SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
                    $this->db->query($qry);
                    $result = $this->db->registro();
                    $calificacion_quimestral = $result->promedio;

                    $suma_periodos += $calificacion_quimestral;

                    $calificacion_quimestral = floor($calificacion_quimestral * 100) / 100;
                    $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"periodo_" . $contador . "\" disabled value=\"$calificacion_quimestral\" style=\"color:#666;\" /></td>\n";
                }

                // Calculo la suma y el promedio de los dos quimestres
                $promedio_periodos = $suma_periodos / $contador_periodos;
                $promedio_periodos = floor($promedio_periodos * 100) / 100;

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_periodos_" . $contador . "\" disabled value=\"" . number_format($suma_periodos, 2) . "\" style=\"color:#666;\" /></td>\n";

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_periodos_" . $contador . "\" disabled value=\"" . number_format($promedio_periodos, 2) . "\" style=\"color:#666;\" /></td>\n";

                $this->db->query("SELECT id_rubrica_evaluacion, ac.ap_estado FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_aporte_paralelo_cierre ac, sw_periodo_evaluacion p WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_paralelo = $id_paralelo AND  r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $id_tipo_periodo");
                $result = $this->db->registro();
                $id_rubrica_personalizada = $result->id_rubrica_evaluacion;
                $estado_aporte = $result->ap_estado;

                //Insertamos en la tabla sw_supletorios_temp
                $this->db->query("INSERT INTO sw_supletorios_temp SET id_tipo_periodo = :id_tipo_periodo, id_paralelo = :id_paralelo, id_estudiante = :id_estudiante, id_asignatura = :id_asignatura, id_rubrica_personalizada = :id_rubrica_personalizada");

                //Vincular valores
                $this->db->bind(':id_tipo_periodo', $id_tipo_periodo);
                $this->db->bind(':id_paralelo', $id_paralelo);
                $this->db->bind(':id_estudiante', $id_estudiante);
                $this->db->bind(':id_asignatura', $id_asignatura);
                $this->db->bind(':id_rubrica_personalizada', $id_rubrica_personalizada);

                $this->db->execute();

                $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
                $result = $this->db->registro();
                $num_total_registros = $this->db->rowCount();
                if ($num_total_registros > 0) {
                    $calificacion = $result->re_calificacion;
                } else {
                    $calificacion = 0;
                }

                $nota_supletorio = ($calificacion == 0) ? "" : number_format($calificacion, 2);

                // Aqui formo el input para ingresar la calificacion del examen supletorio
                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"supletorio_" . $contador . "\"value=\"" . $nota_supletorio . "\" ";
                if ($estado_aporte == 'A') {
                    $cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_personalizada . ")\" /></td>\n";
                } else {
                    $cadena .= " disabled /></td>\n";
                }

                // Aca calculo la suma del promedio de los quimestres mas el examen supletorio
                $suma_total = $promedio_periodos + $calificacion;

                // Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
                $observacion = "";
                $promedio_final = $promedio_periodos;

                if ($calificacion < 7 && $calificacion > 0) {
                    $suma_total = $suma_periodos;
                    $observacion = ($id_tipo_periodo == 2) ? "REMEDIAL" : "NO APRUEBA";
                } elseif ($calificacion >= 7) {
                    $promedio_final = 7;
                    $observacion = "APRUEBA";
                }

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"suma_total_" . $contador . "\" disabled value=\"" . number_format($suma_total, 2) . "\" style=\"color:#666;\" /></td>\n";

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_final_" . $contador . "\" disabled value=\"" . number_format($promedio_final, 2) . "\" style=\"color:#666;\" /></td>\n";

                $cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"fuente8\" size=\"10\" id=\"observacion_" . $contador . "\" disabled value=\"" . $observacion . "\" style=\"color:#666;\" /></td>\n";

                $cadena .= "</tr>\n";
            }
        }

        if ($contador == 0) {
            $num_columnas = 9 + $contador_abreviaturas;
            $cadena .= "<td colspan='$num_columnas' style='text-align: center; font-size: 12px'>No se encontraron registros coincidentes...</td>";
        }

        $cadena .= "</tbody>\n";
        $cadena .= "</table>\n";

        //Finally it erases the data inserted
        // $this->db->query('DELETE FROM `sw_promedio_anual` WHERE `id_paralelo` = :id_paralelo AND id_asignatura = :id_asignatura');

        //Vincular valores
        $this->db->bind(':id_paralelo', $id_paralelo);
        $this->db->bind(':id_asignatura', $id_asignatura);

        $this->db->execute();

        $datos = [
            'cadena' => $cadena,
            'contador' => $contador
        ];

        return $datos;
    }

    public function insertarParalelo($datos)
    {
        //Obtener el máximo es_orden
        $this->db->query("SELECT MAX(pa_orden) AS maximo 
                            FROM sw_paralelo
                           WHERE id_periodo_lectivo = " . $datos['id_periodo_lectivo']);
        $record = $this->db->registro();
        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $record->maximo + 1;

        $this->db->query('INSERT INTO sw_paralelo (id_periodo_lectivo, id_curso, id_jornada, pa_nombre, pa_orden) VALUES (:id_periodo_lectivo, :id_curso, :id_jornada, :pa_nombre, :pa_orden)');

        //Vincular valores
        $this->db->bind(':id_curso', $datos['id_curso']);
        $this->db->bind(':id_jornada', $datos['id_jornada']);
        $this->db->bind(':id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind(':pa_nombre', $datos['pa_nombre']);
        $this->db->bind(':pa_orden', $maximo_orden);

        $this->db->execute();

        //Obtener el máximo id_paralelo
        $this->db->query("SELECT MAX(id_paralelo) AS max_id FROM `sw_paralelo`");
        $id_paralelo = $this->db->registro()->max_id;

        // Asociar el paralelo creado con los aportes de evaluación creados para el cierre de periodos...
        $qry = "SELECT a.id_aporte_evaluacion,
					   a.ap_fecha_apertura, 
					   a.ap_fecha_cierre  
				  FROM `sw_aporte_evaluacion` a, 
					   `sw_periodo_evaluacion` p  
				 WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion
				   AND p.id_periodo_lectivo = " . $datos['id_periodo_lectivo'];
        $this->db->query($qry);
        $aportes = $this->db->registros();
        foreach ($aportes as $aporte) {
            $id_aporte_evaluacion = $aporte->id_aporte_evaluacion;
            $fecha_apertura = $aporte->ap_fecha_apertura;
            $fecha_cierre = $aporte->ap_fecha_cierre;
            // Insertar la asociación para el futuro cierre...
            $qry = "SELECT * 
					  FROM `sw_aporte_paralelo_cierre` 
					 WHERE id_aporte_evaluacion = $id_aporte_evaluacion 
					   AND id_paralelo = $id_paralelo";
            $this->db->query($qry);
            $this->db->registro();
            if ($this->db->rowCount() > 0) {
                $this->db->query("INSERT INTO `sw_aporte_paralelo_cierre` 
				                          SET id_aporte_evaluacion = :id_aporte_evaluacion, 
                                              id_paralelo = :id_paralelo, 
                                              ap_fecha_apertura = :ap_fecha_apertura, 
                                              ap_fecha_cierre = :ap_fecha_cierre, 
                                              ap_estado = :ap_estado");

                //Vincular valores
                $this->db->bind(':id_aporte_evaluacion', $id_aporte_evaluacion);
                $this->db->bind(':id_paralelo', $id_paralelo);
                $this->db->bind(':ap_fecha_apertura', $fecha_apertura);
                $this->db->bind(':ap_fecha_cierre', $fecha_cierre);
                $this->db->bind(':ap_estado', 'C');

                $this->db->execute();
            }
        }
    }

    public function actualizarParalelo($datos)
    {
        $this->db->query('UPDATE sw_paralelo SET id_curso = :id_curso, id_jornada = :id_jornada, pa_nombre = :pa_nombre WHERE id_paralelo = :id_paralelo');

        //Vincular valores
        $this->db->bind(':id_paralelo', $datos['id_paralelo']);
        $this->db->bind(':id_curso', $datos['id_curso']);
        $this->db->bind(':id_jornada', $datos['id_jornada']);
        $this->db->bind(':pa_nombre', $datos['pa_nombre']);

        return $this->db->execute();
    }

    public function eliminarParalelo($id)
    {
        $this->db->query('DELETE FROM `sw_paralelo` WHERE `id_paralelo` = :id_paralelo');

        //Vincular valores
        $this->db->bind(':id_paralelo', $id);

        return $this->db->execute();
    }

    public function actualizarOrden($id_paralelo, $pa_orden)
    {
        $this->db->query('UPDATE `sw_paralelo` SET `pa_orden` = :pa_orden WHERE `id_paralelo` = :id_paralelo');

        //Vincular valores
        $this->db->bind(':id_paralelo', $id_paralelo);
        $this->db->bind(':pa_orden', $pa_orden);

        echo $this->db->execute();
    }
}
