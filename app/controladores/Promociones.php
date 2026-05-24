<?php
class Promociones extends Controlador
{
    private $paraleloModelo;
    private $promocionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->promocionModelo = $this->modelo('Promocion');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'paralelos' => $paralelos,
            'titulo' => 'Promociones',
            'nombreVista' => 'secretaria/promociones/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listarEstudiantesPromocion()
    {
        $id_paralelo = $_POST["id_paralelo"];
        $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

        echo $this->promocionModelo->listarEstudiantes($id_paralelo, $id_periodo_lectivo);
    }

    public function certificado()
    {
        //Aquí va el certificado de la promoción en PDF
        $id_paralelo = $_POST["id_paralelo"];
        $id_estudiante = $_POST["id_estudiante"];

        //
    }
}