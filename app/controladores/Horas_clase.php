<?php
class Horas_clase extends Controlador
{
    private $horaClaseModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->horaClaseModelo = $this->modelo('Hora_clase');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Horas Clase',
            'nombreVista' => 'autoridad/horas_clase/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo $this->horaClaseModelo->listarHorasClase($id_periodo_lectivo);
    }

    public function obtenerHorasClasePorParalelo()
    {
        $id_paralelo = $_POST['id_paralelo'];
        echo $this->horaClaseModelo->obtenerHorasClasePorParalelo($id_paralelo);
    }

    public function insert()
    {
        $hc_nombre = $_POST['hc_nombre'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->horaClaseModelo->existeNombreHoraClase($hc_nombre, $id_periodo_lectivo)) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => "Ya se ha definido la hora de clase $hc_nombre para este periodo lectivo",
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_periodo_lectivo' => $id_periodo_lectivo,
                'hc_nombre' => $hc_nombre,
                'hc_hora_inicio' => $_POST['hc_hora_inicio'],
                'hc_hora_fin' => $_POST['hc_hora_fin']
            ];

            try {
                $this->horaClaseModelo->insertarHoraClase($datos);
                $data = array(
                    "titulo" => "Inserción exitosa.",
                    "mensaje"  => 'La Hora de Clase se insertó correctamente',
                    "tipo_mensaje"  => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo" => "Ocurrió un error inesperado.",
                    "mensaje"  => 'La Hora de Clase no se pudo insertar correctamente...Error: ' . $ex->getMessage(),
                    "tipo_mensaje"  => "error"
                );
    
                echo json_encode($data);
            }
        }
    }

    public function edit()
    {
        $id_hora_clase = $_POST['id_hora_clase'];
        echo json_encode($this->horaClaseModelo->obtenerHoraClase($id_hora_clase));
    }

    public function update()
    {
        $id_hora_clase = $_POST['id_hora_clase'];
        $horaClaseActual = $this->horaClaseModelo->obtenerHoraClase($id_hora_clase);

        $hc_nombre = $_POST['hc_nombre'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($horaClaseActual->hc_nombre != $hc_nombre && $this->horaClaseModelo->existeNombreHoraClase($hc_nombre, $id_periodo_lectivo)) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => "Ya se ha definido la hora de clase $hc_nombre para este periodo lectivo",
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_hora_clase' => $id_hora_clase,
                'hc_nombre' => $hc_nombre,
                'hc_hora_inicio' => $_POST['hc_hora_inicio'],
                'hc_hora_fin' => $_POST['hc_hora_fin']
            ];

            try {
                $this->horaClaseModelo->actualizarHoraClase($datos);
                $data = array(
                    "titulo" => "Actualización exitosa.",
                    "mensaje"  => 'La Hora de Clase se actualizó correctamente',
                    "tipo_mensaje"  => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo" => "Ocurrió un error inesperado.",
                    "mensaje"  => 'La Hora de Clase no se pudo actualizar correctamente...Error: ' . $ex->getMessage(),
                    "tipo_mensaje"  => "error"
                );
    
                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        $id_hora_clase = $_POST['id'];

        try {
            $this->horaClaseModelo->eliminarHoraClase($id_hora_clase);
            $data = array(
                "titulo" => "Eliminación exitosa.",
                "mensaje"  => 'La Hora de Clase se eliminó correctamente',
                "tipo_mensaje"  => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo" => "Ocurrió un error inesperado.",
                "mensaje"  => 'La Hora de Clase no se pudo eliminar correctamente...Error: ' . $ex->getMessage(),
                "tipo_mensaje"  => "error"
            );

            echo json_encode($data);
        }
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->horaClaseModelo->actualizarOrden($index, $newPosition);
        }
    }

}