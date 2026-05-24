<?php
class Asignatura_curso
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarAsignaturasAsociadas($id_curso)
    {
        $this->db->query("SELECT *
                            FROM sw_asignatura_curso ac,
                                 sw_curso c,
                                 sw_especialidad e,   
                                 sw_asignatura a 
                           WHERE c.id_curso = ac.id_curso
                             AND e.id_especialidad = c.id_especialidad 
                             AND a.id_asignatura = ac.id_asignatura
                             AND ac.id_curso = $id_curso 
                           ORDER BY ac_orden");

        return $this->db->registros();
    }

    public function AsignaturasAsociadasParalelo($id_paralelo)
    {
        $this->db->query("
            SELECT a.*
            FROM sw_asignatura a,
                 sw_asignatura_curso ac, 
                 sw_paralelo p 
            WHERE a.id_asignatura = ac.id_asignatura
              AND p.id_curso = ac.id_curso
              AND p.id_paralelo = $id_paralelo 
            ORDER BY ac_orden              
        ");

        return $this->db->registros();
    }

    public function existeAsociacion($id_curso, $id_asignatura)
    {
        $this->db->query("SELECT * FROM sw_asignatura_curso WHERE id_curso = $id_curso AND id_asignatura = $id_asignatura");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        //Obtener el máximo ac_orden
        $this->db->query("SELECT MAX(ac_orden) AS maximo 
                            FROM sw_asignatura_curso
                           WHERE id_curso = " . $datos['id_curso']);
        $record = $this->db->registro();

        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $record->maximo + 1;

        $this->db->query("INSERT INTO sw_asignatura_curso SET id_curso = :id_curso, id_asignatura = :id_asignatura, id_periodo_lectivo = :id_periodo_lectivo, ac_orden = :ac_orden");

        //Vincular los valores
        $this->db->bind('id_curso', $datos['id_curso']);
        $this->db->bind('id_asignatura', $datos['id_asignatura']);
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind('ac_orden', $maximo_orden);

        $this->db->execute();
    }

    public function eliminarAsociacion($id_asignatura_curso)
    {
        $this->db->query("DELETE FROM sw_asignatura_curso WHERE id_asignatura_curso = $id_asignatura_curso");
        return $this->db->execute();
    }

    public function actualizarOrden($id_asignatura_curso, $ac_orden)
    {
        $this->db->query('UPDATE `sw_asignatura_curso` SET `ac_orden` = :ac_orden WHERE `id_asignatura_curso` = :id_asignatura_curso');

        //Vincular valores
        $this->db->bind(':id_asignatura_curso', $id_asignatura_curso);
        $this->db->bind(':ac_orden', $ac_orden);

        echo $this->db->execute();
    }
}