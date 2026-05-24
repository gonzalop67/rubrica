<?php
class Cuestionario
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function existeAsociacion($id_categoria, $id_paralelo, $id_asignatura)
    {
        $this->db->query("SELECT * FROM sw_cuestionario_paralelo WHERE id_category = $id_categoria AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_cuestionario_paralelo SET id_usuario = :id_usuario, id_category = :id_category, id_paralelo = :id_paralelo, id_asignatura = :id_asignatura");

        //Vincular los valores
        $this->db->bind('id_usuario', $datos['id_usuario']);
        $this->db->bind('id_category', $datos['id_category']);
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('id_asignatura', $datos['id_asignatura']);

        $this->db->execute();
    }

    public function eliminarAsociacion($id_cuestionario_paralelo)
    {
        $this->db->query("DELETE FROM sw_cuestionario_paralelo WHERE id = $id_cuestionario_paralelo");
        return $this->db->execute();
    }

    public function listarCuestionariosAsociados($id_categoria)
    {
        $this->db->query("SELECT cp.id, 
                                 category, 
                                 es_figura, 
                                 cu_nombre,
                                 pa_nombre,
                                 as_nombre 
                            FROM sw_cuestionario_paralelo cp,
                                 sw_exam_category ec,
                                 sw_paralelo p,
                                 sw_curso c,
                                 sw_especialidad e,   
                                 sw_asignatura a 
                           WHERE ec.id = cp.id_category 
                             AND p.id_paralelo = cp.id_paralelo 
                             AND c.id_curso = p.id_curso
                             AND e.id_especialidad = c.id_especialidad 
                             AND a.id_asignatura = cp.id_asignatura
                             AND cp.id_category = $id_categoria 
                           ORDER BY category, as_nombre, pa_orden");

        return $this->db->registros();
    }
}