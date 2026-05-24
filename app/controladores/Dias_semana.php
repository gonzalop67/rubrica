<?php
class Dias_semana extends Controlador
{
    private $diaSemanaModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->diaSemanaModelo = $this->modelo('Dia_semana');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Dias de la Semana',
            'nombreVista' => 'autoridad/dias_semana/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo $this->diaSemanaModelo->listarDiasSemana($id_periodo_lectivo);
    }

    public function obtenerDiasSemanaPorParalelo()
    {
        $id_paralelo = $_POST['id_paralelo'];
        echo $this->diaSemanaModelo->obtenerDiasSemanaPorParalelo($id_paralelo);
    }

    public function insert()
    {
        $ds_nombre = $_POST['ds_nombre'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->diaSemanaModelo->existeNombreDiaSemana($ds_nombre, $id_periodo_lectivo)) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => "Ya se ha definido el Día de la Semana $ds_nombre para este periodo lectivo",
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_periodo_lectivo' => $id_periodo_lectivo,
                'ds_nombre' => $ds_nombre
            ];

            try {
                $this->diaSemanaModelo->insertarDiaSemana($datos);

                $data = array(
                    "titulo" => "Inserción exitosa.",
                    "mensaje"  => 'El Día de la Semana se insertó correctamente.',
                    "tipo_mensaje"  => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo" => "Ocurrió un error inesperado.",
                    "mensaje"  => 'El Día de la Semana NO se insertó correctamente. Error: ' . $ex->getMessage(),
                    "tipo_mensaje"  => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function edit()
    {
        $id_dia_semana = $_POST['id_dia_semana'];
        echo json_encode($this->diaSemanaModelo->obtenerDiaSemana($id_dia_semana));
    }

    public function update()
    {
        $id_dia_semana = $_POST['id_dia_semana'];
        $ds_nombre = strtoupper($_POST['ds_nombre']);

        $diaSemanaActual = $this->diaSemanaModelo->obtenerDiaSemana($id_dia_semana);

        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($diaSemanaActual->ds_nombre != $_POST['ds_nombre'] && $this->diaSemanaModelo->existeNombreDiaSemana($ds_nombre, $id_periodo_lectivo)) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => "Ya se ha definido el Día de la Semana $ds_nombre para este periodo lectivo",
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_dia_semana' => $id_dia_semana,
                'ds_nombre' => $ds_nombre
            ];

            try {
                $this->diaSemanaModelo->actualizarDiaSemana($datos);

                $data = array(
                    "titulo" => "Actualización exitosa.",
                    "mensaje"  => 'El Día de la Semana se actualizó correctamente.',
                    "tipo_mensaje"  => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo" => "Ocurrió un error inesperado.",
                    "mensaje"  => 'El Día de la Semana NO se actualizó correctamente. Error: ' . $ex->getMessage(),
                    "tipo_mensaje"  => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        $id_dia_semana = $_POST['id'];

        try {
            $this->diaSemanaModelo->eliminarDiaSemana($id_dia_semana);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Día de la Semana fue eliminado exitosamente",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Día de la Semana NO fue eliminado correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->diaSemanaModelo->actualizarOrden($index, $newPosition);
        }
    }
}
