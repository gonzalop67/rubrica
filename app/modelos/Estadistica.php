<?php
class Estadistica
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerAprobadosPorParalelo($id_paralelo, $id_periodo_lectivo)
    {
        $escala = array();
        $escala[] = array(
            'etiqueta' => 'Aprobados: ',
            'contador' => 0
        );
        $escala[] = array(
            'etiqueta' => 'Reprobados: ',
            'contador' => 0
        );
        $datos = array();
        $query = "SELECT id_estudiante FROM sw_estudiante_periodo_lectivo WHERE activo = 1 AND id_paralelo = $id_paralelo";
        $this->db->query($query);
        $estudiantes = $this->db->registros();
        $num_total_estudiantes = $this->db->rowCount();

        if ($num_total_estudiantes > 0) {
            foreach ($estudiantes as $estudiante) {
                $id_estudiante = $estudiante->id_estudiante;
                $query = "SELECT es_promocionado($id_estudiante, $id_periodo_lectivo, $id_paralelo) AS aprueba";
                $this->db->query($query);
                $registro = $this->db->registro();
                if ($registro->aprueba == 1)
                    $escala[0]['contador'] = $escala[0]['contador'] + 1;
                else
                    $escala[1]['contador'] = $escala[1]['contador'] + 1;
            }
        }

        // Calculo de porcentajes de acuerdo a la escala de calificaciones				
        for ($i = 0; $i < count($escala); $i++) {
            $datos[] = array(
                'etiqueta' => $escala[$i]['etiqueta'] . $escala[$i]['contador'],
                'porcentaje' => $escala[$i]['contador'] / $num_total_estudiantes * 100
            );
        }

        return json_encode($datos);
    }
}
