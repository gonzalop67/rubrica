<?php
class Reporte_docente_quimestral extends Controlador
{
    private $asignaturaModelo;
    private $periodoEvaluacionModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->periodoEvaluacionModelo = $this->modelo('Periodo_evaluacion');
    }

    public function index()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $periodos_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodosPrincipales($id_periodo_lectivo);
        $numero_asignaturas = $this->asignaturaModelo->contarAsignaturasDocente($id_usuario, $id_periodo_lectivo);
        $datos = [
            'titulo' => 'Quimestrales',
            'periodos_evaluacion' => $periodos_evaluacion,
            'numero_asignaturas' => $numero_asignaturas,
            'nombreVista' => 'docente/reportes/quimestrales.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function obtenerDetallePeriodoEvaluacion()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_asignatura = $_POST['id_asignatura'];
        $id_periodo_evaluacion = $_POST['id_periodo_evaluacion'];

        echo $this->periodoEvaluacionModelo->obtenerDetallePeriodoEvaluacion($id_paralelo, $id_asignatura, $id_periodo_evaluacion);
    }

    public function reporte()
    {
        //
    }
}
