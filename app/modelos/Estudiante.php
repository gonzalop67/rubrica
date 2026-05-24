<?php
class Estudiante
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerEstudiante($id_estudiante)
    {
        $this->db->query("SELECT * FROM sw_estudiante WHERE id_estudiante = $id_estudiante");

        return $this->db->registro();
    }

    public function existeNombreEstudiante($apellidos, $nombres)
    {
        $this->db->query("SELECT * 
                            FROM sw_estudiante 
                           WHERE es_apellidos = '$apellidos'
                             AND es_nombres = '$nombres'
        ");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeNroCedula($es_cedula)
    {
        $this->db->query("SELECT * 
                            FROM sw_estudiante 
                           WHERE es_cedula = '$es_cedula'
        ");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeEstudiantePeriodoLectivo($id_estudiante, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * 
                            FROM sw_estudiante_periodo_lectivo 
                           WHERE id_estudiante = $id_estudiante 
                             AND id_periodo_lectivo = $id_periodo_lectivo
        ");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function obtenerIdEstudiante($es_cedula)
    {
        $this->db->query("SELECT id_estudiante FROM sw_estudiante WHERE es_cedula = '$es_cedula'");
        $registro = $this->db->registro();

        return $registro->id_estudiante;
    }

    public function obtenerNombreEstudiante($id_estudiante)
    {
        $this->db->query("SELECT * FROM sw_estudiante WHERE id_estudiante = $id_estudiante");
        $registro = $this->db->registro();

        return $registro->es_apellidos . " " . $registro->es_nombres;
    }

    public function obtenerNumeroMatricula($id_estudiante, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_estudiante_periodo_lectivo WHERE id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");

        return $this->db->registro()->nro_matricula;
    }

    function CalculaEdad($fecha)
    {
        list($Y, $m, $d) = explode("-", $fecha);
        return (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
    }

    public function listarEstudiantesParalelo($id_paralelo)
    {
        $this->db->query("SELECT e.*, 
                                 ep.id_estudiante_periodo_lectivo, 
                                 nro_matricula, 
                                 dg_nombre, 
                                 pe_descripcion, 
                                 es_retirado 
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep, 
                                 sw_periodo_lectivo p, 
                                 sw_periodo_estado pe, 
                                 sw_def_genero dg 
                           WHERE e.id_estudiante = ep.id_estudiante 
                             AND p.id_periodo_lectivo = ep.id_periodo_lectivo 
                             AND pe.id_periodo_estado = p.id_periodo_estado 
                             AND dg.id_def_genero = e.id_def_genero 
                             AND ep.id_paralelo = $id_paralelo 
                             AND activo = 1 
                           ORDER BY es_apellidos, es_nombres ASC
        ");
        $registros = $this->db->registros();
        $num_rows = count($registros);
        $cadena = "";
        if ($num_rows > 0) {
            $contador = 0;
            foreach ($registros as $row) {
                $contador++;
                $codigo = $row->id_estudiante;
                $id_estudiante_periodo_lectivo = $row->id_estudiante_periodo_lectivo;
                $apellidos = $row->es_apellidos;
                $nombres = $row->es_nombres;
                $nro_matricula = $row->nro_matricula;
                $cedula = $row->es_cedula;
                $fec_nacim = $row->es_fec_nacim;
                $edad = $this->CalculaEdad($fec_nacim);
                $genero = $row->dg_nombre;
                $retirado = $row->es_retirado;
                $checked = ($retirado == "N") ? "" : "checked";
                $estado = $row->pe_descripcion;
                $disabled = ($estado == "TERMINADO") ? "disabled" : "";
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>$codigo</td>\n";
                $cadena .= "<td>$nro_matricula</td>\n";
                $cadena .= "<td>$apellidos</td>\n";
                $cadena .= "<td>$nombres</td>\n";
                $cadena .= "<td>$cedula</td>\n";
                $cadena .= "<td>$fec_nacim</td>\n";
                $cadena .= "<td>$edad</td>\n";
                $cadena .= "<td>$genero</td>\n";
                $cadena .= "<td><input type=\"checkbox\" name=\"chkretirado_" . $contador . "\" $checked $disabled onclick=\"actualizar_estado_retirado(this," . $codigo . ")\"></td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class='btn-group'>\n";
                $cadena .= "<a href='javascript:;' class='btn btn-warning btn-sm item-edit' data=" . $codigo . " title='Editar'><span class='fa fa-pencil'></span></a>\n";
                if ($estado != "TERMINADO") {
                    $cadena .= "<a href='" . RUTA_URL . "/matriculacion/delete' class='btn btn-danger btn-sm item-delete' data=" . $codigo . " title='Quitar'><span class='fa fa-remove'></span></a>\n";
                    $cadena .= "<a href='" . RUTA_URL . "/matriculacion/certificado/$id_estudiante_periodo_lectivo' target='_blank' class='btn btn-info btn-sm' title='Certificado'><span class='fa fa-file-text'></span></a>\n";
                }
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='11' align='center'>No se han matriculado estudiantes para este paralelo...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function listarEstudiantesInactivados($id_paralelo)
    {
        $this->db->query("SELECT ep.id_estudiante_periodo_lectivo,
                                 e.id_estudiante,  
                                 es_apellidos,
                                 es_nombres, 
                                 nro_matricula 
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep
                           WHERE e.id_estudiante = ep.id_estudiante 
                             AND ep.id_paralelo = $id_paralelo 
                             AND activo = 0 
                           ORDER BY es_apellidos, es_nombres ASC
        ");
        $registros = $this->db->registros();
        $num_rows = count($registros);
        $cadena = "";
        if ($num_rows > 0) {
            $contador = 0;
            foreach ($registros as $row) {
                $contador++;
                $codigo = $row->id_estudiante_periodo_lectivo;
                $id_estudiante = $row->id_estudiante;
                $apellidos = $row->es_apellidos;
                $nombres = $row->es_nombres;
                $nro_matricula = $row->nro_matricula;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>$id_estudiante</td>\n";
                $cadena .= "<td>$nro_matricula</td>\n";
                $cadena .= "<td>$apellidos</td>\n";
                $cadena .= "<td>$nombres</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class='btn-group'>\n";
                $cadena .= "<a href='javascript:;' class='btn btn-info btn-sm item-deleted' data=" . $codigo . " title='Volver a matricular'><span class='fa fa-undo'></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='6' align='center'>No se han des-matriculado estudiantes de este paralelo..</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function contarEstudiantes($id_periodo_lectivo)
    {
        $this->db->query("SELECT COUNT(*) AS nro_estudiantes
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep
                           WHERE e.id_estudiante = ep.id_estudiante
                             AND activo = 1 
                             AND id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registro()->nro_estudiantes;
    }

    public function buscarEstudiantes($patron)
    {
        $query = "SELECT id_estudiante, 
                         CONCAT(es_apellidos,' ',es_nombres) AS nombre 
                    FROM sw_estudiante 
                   WHERE es_nombres LIKE '%$patron%' 
                      OR es_apellidos LIKE '%$patron%'";

        $this->db->query($query);

        $resultado = array();

        foreach ($this->db->registros() as $row) {
            $datos = array(
                'id' => $row->id_estudiante,
                'value' => $row->nombre
            );
            array_push($resultado, $datos);
        }

        return json_encode($resultado);
    }

    public function getNumeroEstudiantesPorParalelo($id_periodo_lectivo, $id_jornada)
    {
        $query = "SELECT ep.id_paralelo, 
						 CONCAT(cu_abreviatura, pa_nombre, ' ', es_abreviatura) AS paralelo, 
						 COUNT(*) AS numero 
				    FROM sw_estudiante_periodo_lectivo ep, 
						 sw_paralelo p, 
						 sw_curso c, 
						 sw_especialidad e, 
						 sw_jornada j 
				   WHERE p.id_paralelo = ep.id_paralelo 
					 AND c.id_curso = p.id_curso 
					 AND e.id_especialidad = c.id_especialidad 
					 AND j.id_jornada = p.id_jornada 
					 AND ep.activo = 1
					 AND ep.id_periodo_lectivo = $id_periodo_lectivo 
					 AND p.id_jornada = $id_jornada 
				GROUP BY ep.id_paralelo 
				ORDER BY pa_orden";
        $this->db->query($query);
        $datos = array();

        foreach ($this->db->registros() as $dato) {
            $id_paralelo = $dato->id_paralelo;
            // Obtener el numero de mujeres
            $query = "SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND id_def_genero = 1 GROUP BY id_def_genero ORDER BY id_def_genero";
            $this->db->query($query);
            $registro = $this->db->registro();
            $numero_mujeres = $registro->numero;
            // Obtener el numero de hombres
            $query = "SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND id_def_genero = 2 GROUP BY id_def_genero ORDER BY id_def_genero";
            $this->db->query($query);
            $registro = $this->db->registro();
            $numero_hombres = $registro->numero;

            $datos[] = array(
                'paralelo' => $dato->paralelo,
                'numero_mujeres' => $numero_mujeres,
                'numero_hombres' => $numero_hombres
            );
        }

        return json_encode($datos);
    }

    public function asociarEstudiantePeriodoLectivo($id_estudiante, $id_periodo_lectivo)
    {
        $this->db->query("SELECT MAX(nro_matricula) AS max_nro_matricula FROM sw_estudiante_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
        $record = $this->db->registro();
        $max_nro_matricula = ($this->db->rowCount() == 0) ? 1 : $record->max_nro_matricula + 1;

        $datos = [
            'id_estudiante' => $id_estudiante,
            'id_periodo_lectivo' => $id_periodo_lectivo,
            'id_paralelo' => $_POST['id_paralelo'],
            'es_estado' => 'N',
            'es_retirado' => 'N',
            'nro_matricula' => $max_nro_matricula,
            'activo' => 1
        ];

        $this->db->query("INSERT INTO sw_estudiante_periodo_lectivo
                                  SET id_estudiante = :id_estudiante,
                                      id_periodo_lectivo = :id_periodo_lectivo,
                                      id_paralelo = :id_paralelo,
                                      es_estado = :es_estado,
                                      es_retirado = :es_retirado,
                                      nro_matricula = :nro_matricula,
                                      activo = :activo");

        // Vincular valores
        $this->db->bind('id_estudiante', $datos['id_estudiante']);
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('es_estado', $datos['es_estado']);
        $this->db->bind('es_retirado', $datos['es_retirado']);
        $this->db->bind('nro_matricula', $datos['nro_matricula']);
        $this->db->bind('activo', $datos['activo']);

        $this->db->execute();
    }

    public function insertarEstudiante($datos)
    {
        $this->db->query("INSERT INTO sw_estudiante
                                  SET id_tipo_documento = :id_tipo_documento,
                                      id_def_genero = :id_def_genero,
                                      id_def_nacionalidad = :id_def_nacionalidad,
                                      es_apellidos = :es_apellidos,
                                      es_nombres = :es_nombres,
                                      es_nombre_completo = :es_nombre_completo,
                                      es_cedula = :es_cedula,
                                      es_email = :es_email,
                                      es_sector = :es_sector,
                                      es_direccion = :es_direccion,
                                      es_telefono = :es_telefono,
                                      es_fec_nacim = :es_fec_nacim");

        // Vincular valores
        $this->db->bind('id_tipo_documento', $datos['id_tipo_documento']);
        $this->db->bind('id_def_genero', $datos['id_def_genero']);
        $this->db->bind('id_def_nacionalidad', $datos['id_def_nacionalidad']);
        $this->db->bind('es_apellidos', $datos['es_apellidos']);
        $this->db->bind('es_nombres', $datos['es_nombres']);
        $this->db->bind('es_nombre_completo', $datos['es_nombre_completo']);
        $this->db->bind('es_cedula', $datos['es_cedula']);
        $this->db->bind('es_email', $datos['es_email']);
        $this->db->bind('es_sector', $datos['es_sector']);
        $this->db->bind('es_direccion', $datos['es_direccion']);
        $this->db->bind('es_telefono', $datos['es_telefono']);
        $this->db->bind('es_fec_nacim', $datos['es_fec_nacim']);

        $this->db->execute();

        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        //Insertar en la tabla sw_estudiante_periodo_lectivo
        $this->db->query("SELECT MAX(id_estudiante) AS insertID FROM sw_estudiante");
        $registro = $this->db->registro();

        $id_estudiante = $registro->insertID;

        $this->db->query("SELECT MAX(nro_matricula) AS max_nro_matricula FROM sw_estudiante_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
        $record = $this->db->registro();
        $max_nro_matricula = ($this->db->rowCount() == 0) ? 1 : $record->max_nro_matricula + 1;

        $datos = [
            'id_estudiante' => $id_estudiante,
            'id_periodo_lectivo' => $id_periodo_lectivo,
            'id_paralelo' => $_POST['id_paralelo'],
            'es_estado' => 'N',
            'es_retirado' => 'N',
            'nro_matricula' => $max_nro_matricula,
            'activo' => 1
        ];

        $this->db->query("INSERT INTO sw_estudiante_periodo_lectivo
                                  SET id_estudiante = :id_estudiante,
                                      id_periodo_lectivo = :id_periodo_lectivo,
                                      id_paralelo = :id_paralelo,
                                      es_estado = :es_estado,
                                      es_retirado = :es_retirado,
                                      nro_matricula = :nro_matricula,
                                      activo = :activo");

        // Vincular valores
        $this->db->bind('id_estudiante', $datos['id_estudiante']);
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('es_estado', $datos['es_estado']);
        $this->db->bind('es_retirado', $datos['es_retirado']);
        $this->db->bind('nro_matricula', $datos['nro_matricula']);
        $this->db->bind('activo', $datos['activo']);

        $this->db->execute();
    }

    public function actualizarEstudiante($datos)
    {
        $this->db->query("UPDATE sw_estudiante
                             SET id_tipo_documento = :id_tipo_documento,
                                 id_def_genero = :id_def_genero,
                                 id_def_nacionalidad = :id_def_nacionalidad,
                                 es_apellidos = :es_apellidos,
                                 es_nombres = :es_nombres,
                                 es_nombre_completo = :es_nombre_completo,
                                 es_cedula = :es_cedula,
                                 es_email = :es_email,
                                 es_sector = :es_sector,
                                 es_direccion = :es_direccion,
                                 es_telefono = :es_telefono,
                                 es_fec_nacim = :es_fec_nacim
                           WHERE id_estudiante = :id_estudiante");

        // Vincular valores
        $this->db->bind('id_estudiante', $datos['id_estudiante']);
        $this->db->bind('id_tipo_documento', $datos['id_tipo_documento']);
        $this->db->bind('id_def_genero', $datos['id_def_genero']);
        $this->db->bind('id_def_nacionalidad', $datos['id_def_nacionalidad']);
        $this->db->bind('es_apellidos', $datos['es_apellidos']);
        $this->db->bind('es_nombres', $datos['es_nombres']);
        $this->db->bind('es_nombre_completo', $datos['es_nombre_completo']);
        $this->db->bind('es_cedula', $datos['es_cedula']);
        $this->db->bind('es_email', $datos['es_email']);
        $this->db->bind('es_sector', $datos['es_sector']);
        $this->db->bind('es_direccion', $datos['es_direccion']);
        $this->db->bind('es_telefono', $datos['es_telefono']);
        $this->db->bind('es_fec_nacim', $datos['es_fec_nacim']);

        $this->db->execute();
    }
}
