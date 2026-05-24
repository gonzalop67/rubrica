<?php
class Paralelos_tutores extends Controlador
{
    private $paraleloModelo;
    private $usuarioModelo;
    private $paraleloTutorModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->paraleloTutorModelo = $this->modelo('Paralelo_tutor');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $tutores = $this->usuarioModelo->obtenerTutores();
        $datos = [
            'paralelos' => $paralelos,
            'tutores' => $tutores,
            'titulo' => 'Paralelos Tutores',
            'nombreVista' => 'admin/paralelos_tutores/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo json_encode($this->paraleloTutorModelo->listarParalelosTutores($id_periodo_lectivo));
    }

    public function insert()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_usuario = $_POST['id_usuario'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->paraleloTutorModelo->existeAsociacion($id_paralelo, $id_usuario)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe la asociacion entre el paralelo y el tutor seleccionados.",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else if ($this->paraleloTutorModelo->existeAsociacionParaleloTutor($id_paralelo)) {
            $data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "Ya se ha asociado un tutor al paralelo seleccionado.",
				"tipo_mensaje" => "error"
			);

			echo json_encode($data);
        } else {
            $datos = [
                'id_paralelo' => $id_paralelo,
                'id_usuario' => $id_usuario,
                'id_periodo_lectivo' => $_SESSION['id_periodo_lectivo']
            ];
            try {
                $this->paraleloTutorModelo->insertarAsociacion($datos);
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Paralelo Tutor fue guardada correctamente.",
                    "tipo_mensaje" => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Paralelo Tutor NO fue guardada correctamente. Error: " . $ex->getMessage(),
                    "tipo_mensaje" => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        $id_paralelo_tutor = $_POST['id'];
        try {
            $this->paraleloTutorModelo->eliminarAsociacion($id_paralelo_tutor);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Paralelo Tutor fue eliminada correctamente.",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Paralelo Tutor NO fue eliminada correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }
}