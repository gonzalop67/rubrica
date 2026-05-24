<?php
class Distributivos extends Controlador
{
    private $usuarioModelo;
    private $paraleloModelo;
    private $distributivoModelo;
    private $mallaCurricularModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->distributivoModelo = $this->modelo('Distributivo');
        $this->mallaCurricularModelo = $this->modelo('Malla_curricular');
    }

    public function index()
    {
        $docentes = $this->usuarioModelo->obtenerDocentes();
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Distributivos',
            'docentes' => $docentes,
            'paralelos' => $paralelos,
            'nombreVista' => 'autoridad/distributivos/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function getByUsuarioId()
    {
        $id_usuario = $_POST['id_usuario'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo json_encode($this->distributivoModelo->listarAsignaturasAsociadas($id_usuario, $id_periodo_lectivo));
    }

    public function insert()
    {
        $id_usuario = $_POST['id_usuario'];
        $id_paralelo = $_POST['id_paralelo'];
        $id_asignatura = $_POST['id_asignatura'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->distributivoModelo->existeAsociacion($id_paralelo, $id_asignatura)) {
            $data = array(
                "titulo"        => "Ocurrió un error inesperado.",
                "mensaje"       => 'Ya existe la asociación entre la asignatura y el paralelo en el distributivo.',
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        } else {
            $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

            //Recupero el id_curso asociado con el id_paralelo
            $id_curso = $this->paraleloModelo->getCursoId($id_paralelo);

            if ($this->mallaCurricularModelo->existeAsociacion($id_curso, $id_asignatura)) {
                //Ahora recupero el id_malla_curricular asociado con el id_curso y el id_asignatura
                $id_malla_curricular = $this->mallaCurricularModelo->getMallaIdCursoAsignatura($id_curso, $id_asignatura);

                $datos = [
                    'id_periodo_lectivo'  => $id_periodo_lectivo,
                    'id_malla_curricular' => $id_malla_curricular,
                    'id_paralelo'         => $id_paralelo,
                    'id_asignatura'       => $id_asignatura,
                    'id_usuario'          => $id_usuario
                ];

                try {
                    $this->distributivoModelo->insertarAsociacion($datos);

                    $data = array(
                        "titulo" => "Inserción exitosa.",
                        "mensaje"  => 'El Item del Distributivo se insertó correctamente.',
                        "tipo_mensaje"  => "success"
                    );
        
                    echo json_encode($data);
                } catch (PDOException $ex) {
                    $data = array(
                        "titulo" => "Ocurrió un error inesperado.",
                        "mensaje"  => "El Item del Distributivo NO se insertó correctamente. Error: " . $ex->getMessage(),
                        "tipo_mensaje"  => "error"
                    );

                    echo json_encode($data);
                }
            } else {
                $data = array(
                    "titulo" => "Ocurrió un error inesperado.",
                    "mensaje"  => "No se han asociado items a la malla con el paralelo y la asignatura seleccionados...",
                    "tipo_mensaje"  => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        try {
            $this->distributivoModelo->eliminarAsociacion($_POST['id']);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item del Distributivo fue eliminado exitosamente",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item del Distributivo NO fue eliminado correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }
}
