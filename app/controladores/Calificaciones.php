<?php
class Calificaciones extends Controlador
{
    var $paraleloModelo;
    var $asignaturaModelo;
    var $aporteEvaluacionModelo;
    var $insumoEvaluacionModelo;
    var $periodoEvaluacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->aporteEvaluacionModelo = $this->modelo('Aporte_evaluacion');
        $this->insumoEvaluacionModelo = $this->modelo('Insumo_evaluacion');
        $this->periodoEvaluacionModelo = $this->modelo('Periodo_evaluacion');
    }

    public function parciales()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $periodos_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodosPrincipales($id_periodo_lectivo);
        $numero_asignaturas = $this->asignaturaModelo->contarAsignaturasDocente($id_usuario, $id_periodo_lectivo);
        $datos = [
            'titulo' => 'Parciales',
            'periodos_evaluacion' => $periodos_evaluacion,
            'numero_asignaturas' => $numero_asignaturas,
            'nombreVista' => 'docente/calificaciones/parciales.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function supletorios()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $periodos_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodosSupletorios($id_periodo_lectivo);
        $numero_asignaturas = $this->asignaturaModelo->contarAsignaturasDocente($id_usuario, $id_periodo_lectivo);
        $datos = [
            'titulo' => 'Supletorios',
            'periodos_evaluacion' => $periodos_evaluacion,
            'numero_asignaturas' => $numero_asignaturas,
            'nombreVista' => 'docente/calificaciones/supletorios.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function obtener_id_aporte_evaluacion()
    {
        echo $this->periodoEvaluacionModelo->obtenerIdAporteEvaluacionSupRemGracia($_POST['id_periodo_evaluacion']);
    }

    public function paginar_asignaturas()
    {
        $paginaActual = $_POST['partida'];
        $numero_asignaturas = $_POST['numero_asignaturas'];
        echo $this->asignaturaModelo->paginarAsignaturasPorDocente(3, $numero_asignaturas, $paginaActual);
    }

    public function mostrarEstadoRubrica()
    {
        $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
        $id_paralelo = $_POST["id_paralelo"];
        echo $this->aporteEvaluacionModelo->mostrarEstadoRubrica($id_aporte_evaluacion, $id_paralelo);
    }

    public function mostrarFechaCierre()
    {
        $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
        $id_paralelo = $_POST["id_paralelo"];
        echo $this->aporteEvaluacionModelo->mostrarFechaCierre($id_aporte_evaluacion, $id_paralelo);
    }

    public function mostrarLeyendasRubricas()
    {
        $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
        $id_asignatura = $_POST['id_asignatura'];
        $id_curso = $_POST['id_curso'];
        echo $this->aporteEvaluacionModelo->mostrarLeyendasRubricas($id_aporte_evaluacion, $id_asignatura, $id_curso);
    }

    public function mostrar_titulos_periodos()
    {
        $datos = [
            'alineacion' => $_POST["alineacion"],
            'id_tipo_periodo' => $_POST["pe_principal"],
            'id_periodo_lectivo' => $_SESSION["id_periodo_lectivo"]
        ];

        echo $this->periodoEvaluacionModelo->mostrarTitulosPeriodos($datos);
    }

    public function contarEstudiantesParalelo()
    {
        $id_paralelo = $_POST['id_paralelo'];
        echo $this->paraleloModelo->contarEstudiantesParalelo($id_paralelo);
    }

    public function listarEstudiantesParalelo()
    {
        $id_curso = $_POST['id_curso'];
        $id_paralelo = $_POST['id_paralelo'];
        $id_asignatura = $_POST['id_asignatura'];
        $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
        $id_periodo_evaluacion = $_POST['id_periodo_evaluacion'];

        $tipo_aporte_evaluacion = $this->aporteEvaluacionModelo->obtenerTipoAporte($id_aporte_evaluacion);

        if ($tipo_aporte_evaluacion == 1)
            echo $this->paraleloModelo->listarEstudiantesParalelo($id_paralelo, $id_asignatura, $id_aporte_evaluacion, $id_curso);
        else if ($tipo_aporte_evaluacion == 2)
            echo $this->paraleloModelo->listarCalificacionesParalelo($id_periodo_evaluacion, $id_paralelo, $id_asignatura, 2);
    }

    public function listarEstudiantesSupletorio()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_asignatura = $_POST['id_asignatura'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $id_periodo_evaluacion = $_POST['id_periodo_evaluacion'];

        $periodo_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodoEvaluacion($id_periodo_evaluacion);
        $id_tipo_periodo = $periodo_evaluacion->id_tipo_periodo;

        echo json_encode($this->paraleloModelo->listarCalificacionesSupletoriosParalelo($id_paralelo, $id_asignatura, $id_periodo_lectivo, $id_tipo_periodo, $id_periodo_evaluacion));
    }

    public function editarCalificacion()
    {
        $datos = [
            'id_estudiante' => $_POST['id_estudiante'],
            'id_paralelo'   => $_POST['id_paralelo'],
            'id_asignatura' => $_POST['id_asignatura'],
            'id_rubrica_personalizada' => $_POST['id_rubrica_personalizada'],
            're_calificacion' => $_POST['re_calificacion']
        ];

        if (!$this->insumoEvaluacionModelo->existeRubricaEstudiante($datos) && $_POST['re_calificacion'] != 0) {
            echo $this->insumoEvaluacionModelo->insertarRubricaEstudiante($datos);
        } else if ($_POST['re_calificacion'] == 0) {
            echo $this->insumoEvaluacionModelo->eliminarRubricaEstudiante($datos);
        } else {
            echo $this->insumoEvaluacionModelo->actualizarRubricaEstudiante($datos);
        }
    }

    public function editarCalificacionComportamiento()
    {
        $datos = [
            'id_estudiante' => $_POST['id_estudiante'],
            'id_paralelo' => $_POST['id_paralelo'],
            'id_asignatura' => $_POST['id_asignatura'],
            'id_aporte_evaluacion' => $_POST['id_aporte_evaluacion'],
            'co_cualitativa' => $_POST['co_calificacion']
        ];

        if (!$this->insumoEvaluacionModelo->existeRubricaEstudianteComportamiento($datos))
            echo $this->insumoEvaluacionModelo->insertarRubricaEstudianteComportamiento($datos);
        else
            echo $this->insumoEvaluacionModelo->actualizarRubricaEstudianteComportamiento($datos);
    }

    public function eliminarCalificacionComportamiento()
    {
        $datos = [
            'id_estudiante' => $_POST['id_estudiante'],
            'id_paralelo' => $_POST['id_paralelo'],
            'id_asignatura' => $_POST['id_asignatura'],
            'id_aporte_evaluacion' => $_POST['id_aporte_evaluacion']
        ];

        echo $this->insumoEvaluacionModelo->eliminarCalificacionComportamiento($datos);
    }
}
