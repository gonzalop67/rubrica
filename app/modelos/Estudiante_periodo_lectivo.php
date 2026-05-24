<?php
class Estudiante_periodo_lectivo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerEstudiantePeriodoLectivo($id_estudiante_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_estudiante_periodo_lectivo WHERE id_estudiante_periodo_lectivo = $id_estudiante_periodo_lectivo");
        return $this->db->registro();
    }

    public function quitarEstudianteParalelo($id_estudiante, $id_paralelo)
    {
        $this->db->query("UPDATE sw_estudiante_periodo_lectivo SET activo = 0 WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo");
        $this->db->execute();
    }

    public function undeleteEstudianteParalelo($id_estudiante_periodo_lectivo)
    {
        $this->db->query("UPDATE sw_estudiante_periodo_lectivo SET activo = 1 WHERE id_estudiante_periodo_lectivo = $id_estudiante_periodo_lectivo");
        $this->db->execute();
    }

    public function actualizarEstadoRetirado($datos)
    {
        $this->db->query("UPDATE sw_estudiante_periodo_lectivo SET es_retirado = '" . $datos['estado_retirado'] . "' WHERE id_estudiante = " . $datos['id_estudiante'] . " AND id_periodo_lectivo = " . $datos['id_periodo_lectivo']);
        $this->db->execute();
    }
}