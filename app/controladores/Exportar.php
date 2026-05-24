<?php
class Exportar extends Controlador
{
    private $paraleloModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->paraleloModelo = $this->modelo('Paralelo');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Exportar Paralelos a CSV',
            'paralelos' => $paralelos,
            'nombreVista' => 'secretaria/exportar/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function exportar()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $this->paraleloModelo->exportar($id_paralelo);
    }
}