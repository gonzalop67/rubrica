<?php
class Tipos_periodo extends Controlador
{
    private $tipoPeriodoModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tipoPeriodoModelo = $this->modelo('Tipo_periodo');
    }

    public function index()
    {
        $tipos_periodo = $this->tipoPeriodoModelo->obtenerTodos();
        $datos = [
            'titulo' => 'Tipos de Periodo de Evaluación',
            'tipos_periodo' => $tipos_periodo,
            'nombreVista' => 'admin/tipos_periodo/index.php'
        ];
        $this->vista('admin/index', $datos);
    }
}