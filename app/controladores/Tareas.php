<?php
class Tareas extends Controlador
{
    private $tareaModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tareaModelo = $this->modelo('Tarea');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Tareas',
            'nombreVista' => 'admin/tareas/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insertar()
    {
        $tarea = $_POST['tarea'];

        echo $this->tareaModelo->insertar($tarea);
    }

    public function obtener()
    {
        //Obtener información del perfil desde el modelo
        $tarea = $this->tareaModelo->obtenerTareaPorId($_POST['id']);

        echo json_encode($tarea);
    }

    public function actualizar()
    {
        $datos = [
            'id_tarea' => trim($_POST['id_tarea']),
            'tarea' => trim($_POST['tarea'])
        ];

        echo $this->tareaModelo->actualizarTarea($datos);
    }

    public function actualizar_hecho()
    {
        $id = $_POST['id'];
        $hecho = $_POST['done'] ? 1 : 0;

        // echo $hecho;
        echo $this->tareaModelo->actualizarHecho($id, $hecho);
    }

    public function paginar()
    {
        // Recibimos los parámetros vía POST
        $paginaActual = $_POST["partida"];
        $num_registros = $_POST["num_registros"];
        $filtro = $_POST["filtro"];
        echo $this->tareaModelo->paginacion($paginaActual, $num_registros, $filtro);
    }
}
