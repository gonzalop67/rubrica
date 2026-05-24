<?php
class Funciones
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
    {
        $this->db->query("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
        $registro = $this->db->registro();
        $id_rubrica_personalizada = $registro->id_rubrica_evaluacion;

        $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
        $registro = $this->db->registro();

        return !empty($registro);
    }

    function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
    {
        $this->db->query("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
        $registro = $this->db->registro();
        $id_rubrica_personalizada = $registro->id_rubrica_evaluacion;

        $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
        $registro = $this->db->registro();

        $calificacion = $registro->re_calificacion;

        return $calificacion;
    }
}
