<?php
class Estudiantes extends Controlador
{
    private $estudianteModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->estudianteModelo = $this->modelo('Estudiante');
    }

    public function numero_estudiantes_por_jornada()
    {
        $id_jornada = $_POST['id_jornada'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo $this->estudianteModelo->getNumeroEstudiantesPorParalelo($id_periodo_lectivo, $id_jornada);
    }

}