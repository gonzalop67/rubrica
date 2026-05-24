<?php
class Estadisticas extends Controlador
{
    private $paraleloModelo;
    private $estadisticaModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->estadisticaModelo = $this->modelo('Estadistica');
    }

    public function aprobados_paralelo()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Aprobados Por Paralelo',
            'paralelos' => $paralelos,
            'nombreVista' => 'autoridad/estadisticas/aprobados_paralelo.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function obtener_aprobados_por_paralelo()
    {
        $id_paralelo = $_POST["id_paralelo"];
        $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

        echo $this->estadisticaModelo->obtenerAprobadosPorParalelo($id_paralelo, $id_periodo_lectivo);
    }
}