<?php 
class Subperiodos_evaluacion extends Controlador {

    private $tipoPeriodoModelo;
    private $subPeriodoEvaluacionModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tipoPeriodoModelo = $this->modelo('Tipo_periodo');
        $this->subPeriodoEvaluacionModelo = $this->modelo('Subperiodo_evaluacion');
    }

    public function index()
    {
        $subperiodos_evaluacion = $this->subPeriodoEvaluacionModelo->obtenerTodos();
        $datos = [
            'titulo' => 'CRUD Sub Periodo de Evaluación',
            'dashboard' => 'Admin',
            'subperiodos_evaluacion' => $subperiodos_evaluacion,
            'nombreVista' => 'admin/subperiodo_evaluacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $tipos_periodo = $this->tipoPeriodoModelo->obtenerTodos();
        $datos = [
            'titulo' => 'Crear Subnivel de Educación',
            'tipos_periodo' => $tipos_periodo,
            'nombreVista' => 'admin/subperiodo_evaluacion/create.php'
        ];
        $this->vista('admin/index', $datos);
    }
}
?>