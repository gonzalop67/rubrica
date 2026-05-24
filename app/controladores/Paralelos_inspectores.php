<?php
class Paralelos_inspectores extends Controlador
{
    private $usuarioModelo;
    private $paraleloModelo;
    private $paraleloInspectorModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->paraleloInspectorModelo = $this->modelo('Paralelo_inspector');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $inspectores = $this->usuarioModelo->obtenerInspectores();
        $datos = [
            'paralelos' => $paralelos,
            'inspectores' => $inspectores,
            'titulo' => 'Paralelos Inspectores',
            'nombreVista' => 'admin/paralelos_inspectores/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo json_encode($this->paraleloInspectorModelo->listarParalelosInspectores($id_periodo_lectivo));
    }

    public function insert()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_usuario = $_POST['id_usuario'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->paraleloInspectorModelo->existeAsociacion($id_paralelo, $id_usuario)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe la asociacion entre el paralelo y el inspector seleccionados.",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else if ($this->paraleloInspectorModelo->existeAsociacionParaleloInspector($id_paralelo)) {
            $data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "Ya se ha asociado un inspector al paralelo seleccionado.",
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
                $this->paraleloInspectorModelo->insertarAsociacion($datos);
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Paralelo Inspector fue guardada correctamente.",
                    "tipo_mensaje" => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Paralelo Inspector NO fue guardada correctamente. Error: " . $ex->getMessage(),
                    "tipo_mensaje" => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        $id_paralelo_inspector = $_POST['id'];
        try {
            $this->paraleloInspectorModelo->eliminarAsociacion($id_paralelo_inspector);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Paralelo Inspector fue eliminada correctamente.",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Paralelo Inspector NO fue eliminada correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }
}