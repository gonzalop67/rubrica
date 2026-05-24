<?php
class Mallas_curriculares extends Controlador
{
    private $cursoModelo;
    private $asignaturaModelo;
    private $mallaCurricularModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cursoModelo = $this->modelo('Curso');
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->mallaCurricularModelo = $this->modelo('Malla_curricular');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $cursos = $this->cursoModelo->obtenerCursos($id_periodo_lectivo);
        $asignaturas = $this->asignaturaModelo->obtenerAsignaturas();
        $datos = [
            'titulo' => 'Mallas Curriculares',
            'cursos' => $cursos,
            'asignaturas' => $asignaturas,
            'nombreVista' => 'autoridad/mallas_curriculares/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function getById()
    {
        $id_malla_curricular = $_POST['id_malla_curricular'];
        echo json_encode($this->mallaCurricularModelo->obtenerItemMalla($id_malla_curricular));
    }

    public function getByCursoId()
    {
        $id_curso = $_POST['id_curso'];
        echo json_encode($this->mallaCurricularModelo->listarAsignaturasAsociadas($id_curso));
    }

    public function insert()
    {
        $id_curso = $_POST['id_curso'];
        $id_asignatura = $_POST['id_asignatura'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->mallaCurricularModelo->existeAsociacion($id_curso, $id_asignatura)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe la asociacion entre el curso y la asignatura seleccionados.",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else {
            $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
            $ma_subtotal = $_POST['ma_horas_presenciales'] + $_POST['ma_horas_autonomas'] + $_POST['ma_horas_tutorias'];
            $datos = [
                'id_periodo_lectivo'    => $id_periodo_lectivo,
                'id_curso'              => $id_curso,
                'id_asignatura'         => $id_asignatura,
                'ma_horas_presenciales' => $_POST['ma_horas_presenciales'],
                'ma_horas_autonomas'    => $_POST['ma_horas_autonomas'],
                'ma_horas_tutorias'     => $_POST['ma_horas_tutorias'],
                'ma_subtotal'           => $ma_subtotal
            ];
            try {
                $this->mallaCurricularModelo->insertarAsociacion($datos);
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "El Item de la Malla Curricular fue insertado exitosamente",
                    "tipo_mensaje" => "success"
                );
    
                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "El Item de la Malla Curricular NO fue insertado correctamente. Error: " . $ex->getMessage(),
                    "tipo_mensaje" => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function update()
    {
        $ma_subtotal = $_POST['ma_horas_presenciales'] + $_POST['ma_horas_autonomas'] + $_POST['ma_horas_tutorias'];
        $datos = [
            'id_malla_curricular'   => $_POST['id_malla_curricular'],
            'ma_horas_presenciales' => $_POST['ma_horas_presenciales'],
            'ma_horas_autonomas'    => $_POST['ma_horas_autonomas'],
            'ma_horas_tutorias'     => $_POST['ma_horas_tutorias'],
            'ma_subtotal'           => $ma_subtotal
        ];
        try {
            $this->mallaCurricularModelo->actualizarAsociacion($datos);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item de la Malla Curricular fue actualizado correctamente.",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado!",
                "mensaje"      => "El Item de la Malla Curricular NO fue actualizado correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }

    public function delete()
    {
        try {
            $this->mallaCurricularModelo->eliminarAsociacion($_POST['id']);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item de la Malla Curricular fue eliminado exitosamente",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Item de la Malla Curricular NO fue eliminado correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }
}
