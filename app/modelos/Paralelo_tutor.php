<?php
class Paralelo_tutor
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarParalelosTutores($id_periodo_lectivo)
    {
        $this->db->query("SELECT id_paralelo_tutor,
                                 cu_nombre, 
                                 es_figura, 
                                 pa_nombre, 
                                 us_titulo, 
                                 us_fullname 
                            FROM sw_paralelo_tutor pt,
                                 sw_paralelo p, 
                                 sw_curso c, 
                                 sw_especialidad e, 
                                 sw_usuario u 
                           WHERE p.id_paralelo = pt.id_paralelo 
                             AND p.id_curso = c.id_curso 
                             AND e.id_especialidad = c.id_especialidad 
                             AND pt.id_usuario = u.id_usuario 
                             AND pt.id_periodo_lectivo = $id_periodo_lectivo 
                           ORDER BY c.id_especialidad, cu_orden, pa_nombre");

        return $this->db->registros();
    }

    public function existeAsociacion($id_paralelo, $id_usuario)
    {
        $this->db->query("SELECT * FROM sw_paralelo_tutor WHERE id_paralelo = $id_paralelo AND id_usuario = $id_usuario");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function existeAsociacionParaleloTutor($id_paralelo)
    {
        $this->db->query("SELECT * FROM sw_paralelo_tutor WHERE id_paralelo = $id_paralelo");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_paralelo_tutor SET id_paralelo = :id_paralelo, id_usuario = :id_usuario, id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular los valores
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('id_usuario', $datos['id_usuario']);
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);

        $this->db->execute();
    }

    public function eliminarAsociacion($id_paralelo_tutor)
    {
        $this->db->query("DELETE FROM sw_paralelo_tutor WHERE id_paralelo_tutor = $id_paralelo_tutor");
        return $this->db->execute();
    }
}
