<?php
class Paralelo_inspector
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarParalelosInspectores($id_periodo_lectivo)
    {
        $this->db->query("SELECT id_paralelo_inspector,
                                 cu_nombre, 
                                 es_figura, 
                                 pa_nombre, 
                                 us_titulo, 
                                 us_fullname 
                            FROM sw_paralelo_inspector pi,
                                 sw_paralelo p, 
                                 sw_curso c, 
                                 sw_especialidad e, 
                                 sw_usuario u 
                           WHERE p.id_paralelo = pi.id_paralelo 
                             AND p.id_curso = c.id_curso 
                             AND e.id_especialidad = c.id_especialidad 
                             AND pi.id_usuario = u.id_usuario 
                             AND pi.id_periodo_lectivo = $id_periodo_lectivo 
                           ORDER BY c.id_especialidad, cu_orden, pa_nombre");

        return $this->db->registros();
    }

    public function existeAsociacion($id_paralelo, $id_usuario)
    {
        $this->db->query("SELECT * FROM sw_paralelo_inspector WHERE id_paralelo = $id_paralelo AND id_usuario = $id_usuario");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeAsociacionParaleloInspector($id_paralelo)
    {
        $this->db->query("SELECT * FROM sw_paralelo_inspector WHERE id_paralelo = $id_paralelo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_paralelo_inspector SET id_paralelo = :id_paralelo, id_usuario = :id_usuario, id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular los valores
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('id_usuario', $datos['id_usuario']);
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);

        $this->db->execute();
    }

    public function eliminarAsociacion($id_paralelo_inspector)
    {
        $this->db->query("DELETE FROM sw_paralelo_inspector WHERE id_paralelo_inspector = $id_paralelo_inspector");
        return $this->db->execute();
    }
}
