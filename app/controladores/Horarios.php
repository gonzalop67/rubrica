<?php
class Horarios extends Controlador
{
    private $horarioModelo;
    private $paraleloModelo;
    private $diaSemanaModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->horarioModelo = $this->modelo('Horario');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->diaSemanaModelo = $this->modelo('Dia_semana');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $dias_semana = $this->diaSemanaModelo->obtenerDiasSemana($id_periodo_lectivo);

        $datos = [
            'titulo' => 'Horarios',
            'paralelos' => $paralelos,
            'dias_semana' => $dias_semana,
            'nombreVista' => 'autoridad/horarios/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_dia_semana = $_POST['id_dia_semana'];

        echo $this->horarioModelo->listarHorario($id_paralelo, $id_dia_semana);
    }

    public function insertar()
    {
        $id_paralelo = trim($_POST['id_paralelo']);
        $id_asignatura = trim($_POST['id_asignatura']);
        $id_dia_semana = trim($_POST['id_dia_semana']);
        $id_hora_clase = trim($_POST['id_hora_clase']);

        //Primero obtengo el id_usuario para luego verificar si existe otra hora con el mismo docente
        $id_usuario = $this->horarioModelo->getIdUsuario($id_paralelo, $id_asignatura);

        $datos = [
            'id_paralelo'   => $id_paralelo,
            'id_asignatura' => $id_asignatura,
            'id_dia_semana' => $id_dia_semana,
            'id_hora_clase' => $id_hora_clase,
            'id_usuario'    => $id_usuario
        ];

        try {
            $this->horarioModelo->insertarAsociacion($datos);

            $data = array(
                "titulo"       => "Inserción exitosa.",
                "mensaje"      => 'El Item del Horario se insertó correctamente',
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => 'El Item del Horario NO se insertó correctamente. Error: ' . $ex->getMessage(),
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        }
    }

    public function insert()
    {
        $id_paralelo = trim($_POST['id_paralelo']);
        $id_asignatura = trim($_POST['id_asignatura']);
        $id_dia_semana = trim($_POST['id_dia_semana']);
        $id_hora_clase = trim($_POST['id_hora_clase']);

        if ($this->horarioModelo->existeAsociacion($id_paralelo, $id_dia_semana, $id_hora_clase)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe una Asignatura asociada en esta Hora Clase...",
                "tipo_mensaje" => "error",
                "cruce"        => 0
            );

            echo json_encode($data);
        } elseif (!$this->horarioModelo->existeDocente($id_paralelo, $id_asignatura)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "No se ha asignado un Docente para esta Asignatura en el Distributivo...",
                "tipo_mensaje" => "error",
                "cruce"        => 0
            );

            echo json_encode($data);
        } elseif ($this->horarioModelo->comprobarCruce($id_paralelo, $id_asignatura, $id_dia_semana, $id_hora_clase)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Existe cruce de horario asociado con esta Hora Clase. ¿Desea insertar de todas formas?",
                "tipo_mensaje" => "error",
                "cruce"        => 1
            );

            echo json_encode($data);
        } else {
            //Primero obtengo el id_usuario para luego verificar si existe otra hora con el mismo docente
            $id_usuario = $this->horarioModelo->getIdUsuario($id_paralelo, $id_asignatura);

            $datos = [
                'id_paralelo'   => $id_paralelo,
                'id_asignatura' => $id_asignatura,
                'id_dia_semana' => $id_dia_semana,
                'id_hora_clase' => $id_hora_clase,
                'id_usuario'    => $id_usuario
            ];

            try {
                $this->horarioModelo->insertarAsociacion($datos);

                $data = array(
                    "titulo"       => "Inserción exitosa.",
                    "mensaje"      => 'El Item del Horario se insertó correctamente',
                    "tipo_mensaje" => "success",
                    "cruce"        => 0
                );
    
                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Ocurrió un error inesperado.",
                    "mensaje"      => 'El Item del Horario NO se insertó correctamente. Error: ' . $ex->getMessage(),
                    "tipo_mensaje" => "success",
                    "cruce"        => 0
                );
    
                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        try {
            $this->horarioModelo->eliminarAsociacion($_POST['id']);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item del Horario fue eliminado exitosamente",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item del Horario NO fue eliminado correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }
}
