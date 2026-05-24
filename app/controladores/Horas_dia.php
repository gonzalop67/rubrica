<?php
class Horas_dia extends Controlador
{
    private $horaDiaModelo;
    private $diaSemanaModelo;
    private $horaClaseModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->horaDiaModelo = $this->modelo('Hora_dia');
        $this->diaSemanaModelo = $this->modelo('Dia_semana');
        $this->horaClaseModelo = $this->modelo('Hora_clase');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        
        $dias_semana = $this->diaSemanaModelo->obtenerDiasSemana($id_periodo_lectivo);
        $horas_clase = $this->horaClaseModelo->obtenerHorasClase($id_periodo_lectivo);

        $datos = [
            'titulo' => 'Asociar Horas con Dias',
            'dias_semana' => $dias_semana,
            'horas_clase' => $horas_clase,
            'nombreVista' => 'autoridad/horas_dia/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_dia_semana = $_POST['id_dia_semana'];
        echo json_encode($this->horaDiaModelo->obtenerHorasClase($id_dia_semana));
    }

    public function insert()
    {
        $id_dia_semana = $_POST['id_dia_semana'];
        $id_hora_clase = $_POST['id_hora_clase'];

        if ($this->horaDiaModelo->existeAsociacion($id_dia_semana, $id_hora_clase)) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => "Ya existe la asociación entre el día y la hora clase para este periodo lectivo",
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_dia_semana' => $id_dia_semana,
                'id_hora_clase' => $id_hora_clase
            ];

            try {
                $this->horaDiaModelo->insertarAsociacion($datos);

                $data = array(
                    "titulo" => "Inserción exitosa.",
                    "mensaje"  => "La Asociación entre Hora Clase y Día de la Semana se insertó correctamente",
                    "tipo_mensaje"  => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo" => "Ocurrió un error inesperado.",
                    "mensaje"  => "La Asociación entre Hora Clase y Día de la Semana no se pudo insertar correctamente...Error: " . $ex->getMessage(),
                    "tipo_mensaje"  => "error"
                );
    
                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        try {
            $this->horaDiaModelo->eliminarAsociacion($_POST['id']);
            $data = array(
                "titulo" => "Eliminación exitosa.",
                "mensaje"  => "La Asociación entre Hora Clase y Día de la Semana se eliminó correctamente",
                "tipo_mensaje"  => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => "La Asociación entre Hora Clase y Día de la Semana no se pudo eliminar correctamente...Error: " . $ex->getMessage(),
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        }
    }
}