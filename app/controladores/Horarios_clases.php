<?php
class Horarios_clases extends Controlador
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
            'titulo' => 'Areas',
            'paralelos' => $paralelos,
            'nombreVista' => 'autoridad/horarios_clases/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

}