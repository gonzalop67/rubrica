<?php
class Curso
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerCurso($id_curso)
    {
        $this->db->query("SELECT * FROM sw_curso WHERE id_curso = $id_curso");
        return $this->db->registro();
    }

    public function obtenerCursos($id_periodo_lectivo)
    {
        $this->db->query("SELECT c.*, 
                                 es_figura, 
                                 n.nombre
                            FROM sw_curso c, 
                                 sw_especialidad e,
                                 sw_sub_nivel_educacion n,
                                 sw_periodo_nivel pn 
                           WHERE e.id_especialidad = c.id_especialidad 
                             AND n.id_nivel_educacion = e.id_nivel_educacion 
                             AND n.id_nivel_educacion = pn.id_nivel_educacion 
                             AND pn.id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY cu_orden");
        return $this->db->registros();
    }

    public function obtenerNombreCurso($id_paralelo)
    {
        $this->db->query("SELECT cu_nombre
                            FROM sw_curso c, 
                                 sw_paralelo p 
                           WHERE c.id_curso = p.id_curso 
                             AND id_paralelo = $id_paralelo");
        return $this->db->registro()->cu_nombre;
    }

    public function getNombreCurso($id_curso, $tipo)
    {
        $this->db->query("SELECT cu_nombre, es_nombre, es_figura FROM sw_curso cu, sw_especialidad es WHERE cu.id_especialidad = es.id_especialidad AND cu.id_curso = $id_curso");
        $resultado = $this->db->registro();
        if ($tipo == 1) {
            return $resultado->cu_nombre . " DE " . $resultado->es_figura;
        } else {
            return $resultado->cu_nombre . " DE " . $resultado->es_nombre . ": " . $resultado->es_figura;
        }
    }

    public function existeNombreCurso($cu_nombre, $id_especialidad)
    {
        $this->db->query("SELECT id_curso FROM sw_curso WHERE cu_nombre = '$cu_nombre' AND id_especialidad = $id_especialidad");

        $this->db->registro();
        return $this->db->rowCount() > 0;
    }

    public function existeNombreCortoCurso($cu_shortname, $id_especialidad)
    {
        $this->db->query("SELECT id_curso FROM sw_curso WHERE cu_shortname = '$cu_shortname' AND id_especialidad = $id_especialidad");

        $this->db->registro();
        return $this->db->rowCount() > 0;
    }

    public function existeAbreviaturaCurso($cu_abreviatura, $id_especialidad)
    {
        $this->db->query("SELECT id_curso FROM sw_curso WHERE cu_abreviatura = '$cu_abreviatura' AND id_especialidad = $id_especialidad");

        $this->db->registro();
        return $this->db->rowCount() > 0;
    }

    public function insertarCurso($datos)
    {
        //Obtener el máximo es_orden
        $this->db->query("SELECT MAX(cu_orden) AS maximo 
                            FROM sw_curso c,
                                 sw_especialidad e,
                                 sw_sub_nivel_educacion n, 
                                 sw_periodo_nivel pn 
                           WHERE n.id_nivel_educacion = e.id_nivel_educacion 
                             AND e.id_especialidad = c.id_especialidad 
                             AND n.id_nivel_educacion = pn.id_nivel_educacion 
                             AND pn.id_periodo_lectivo = " . $datos['id_periodo_lectivo']);
        $record = $this->db->registro();
        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $record->maximo + 1;

        $this->db->query('INSERT INTO sw_curso (id_especialidad, cu_nombre, cu_shortname, cu_abreviatura, es_bach_tecnico, es_intensivo,  cu_orden) VALUES (:id_especialidad, :cu_nombre, :cu_shortname, :cu_abreviatura, :es_bach_tecnico, :es_intensivo, :cu_orden)');

        //Vincular valores
        $this->db->bind(':id_especialidad', $datos['id_especialidad']);
        $this->db->bind(':cu_nombre', $datos['cu_nombre']);
        $this->db->bind(':cu_shortname', $datos['cu_shortname']);
        $this->db->bind(':cu_abreviatura', $datos['cu_abreviatura']);
        $this->db->bind(':es_bach_tecnico', $datos['es_bach_tecnico']);
        $this->db->bind(':es_intensivo', $datos['es_intensivo']);
        $this->db->bind(':cu_orden', $maximo_orden);

        return $this->db->execute();
    }

    public function actualizarCurso($datos)
    {
        $this->db->query("UPDATE sw_curso SET cu_nombre = :cu_nombre, cu_shortname = :cu_shortname, cu_abreviatura = :cu_abreviatura, es_intensivo = :es_intensivo, es_bach_tecnico = :es_bach_tecnico, id_especialidad = :id_especialidad WHERE id_curso = :id_curso");

        //Vincular valores
        $this->db->bind(':id_curso', trim($datos['id_curso']));
        $this->db->bind(':cu_nombre', trim($datos['cu_nombre']));
        $this->db->bind(':cu_shortname', trim($datos['cu_shortname']));
        $this->db->bind(':cu_abreviatura', trim($datos['cu_abreviatura']));
        $this->db->bind(':es_intensivo', $datos['es_intensivo']);
        $this->db->bind(':es_bach_tecnico', $datos['es_bach_tecnico']);
        $this->db->bind(':id_especialidad', $datos['id_especialidad']);

        return $this->db->execute();
    }

    public function eliminarCurso($id)
    {
        $this->db->query('DELETE FROM `sw_curso` WHERE `id_curso` = :id_curso');

        //Vincular valores
        $this->db->bind(':id_curso', $id);

        return $this->db->execute();
    }

    public function actualizarOrden($id_curso, $cu_orden)
    {
        $this->db->query('UPDATE `sw_curso` SET `cu_orden` = :cu_orden WHERE `id_curso` = :id_curso');

        //Vincular valores
        $this->db->bind(':id_curso', $id_curso);
        $this->db->bind(':cu_orden', $cu_orden);

        echo $this->db->execute();
    }
}
