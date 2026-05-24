<?php
class Lista_docentes extends Controlador
{
    private $usuarioModelo;
    private $paraleloModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->paraleloModelo = $this->modelo('Paralelo');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);

        $datos = [
            'titulo' => 'Lista de Docentes',
            'paralelos' => $paralelos,
            'nombreVista' => 'autoridad/lista_docentes/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_paralelo = $_POST['id_paralelo'];
        //Recupero el id_curso asociado con el id_paralelo
        $id_curso = $this->paraleloModelo->getCursoId($id_paralelo);

        echo $this->usuarioModelo->obtenerDocentesPorParalelo($id_curso, $id_paralelo);
    }

}