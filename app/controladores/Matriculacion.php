<?php
class Matriculacion extends Controlador
{

    private $cursoModelo;
    private $jornadaModelo;
    private $paraleloModelo;
    private $defGeneroModelo;
    private $estudianteModelo;
    private $institucionModelo;
    private $tipoDocumentoModelo;
    private $tipoEducacionModelo;
    private $periodoLectivoModelo;
    private $defNacionalidadModelo;
    private $estudiantePeriodoLectivoModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cursoModelo = $this->modelo('Curso');
        $this->jornadaModelo = $this->modelo('Jornada');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->defGeneroModelo = $this->modelo('Def_genero');
        $this->estudianteModelo = $this->modelo('Estudiante');
        $this->institucionModelo = $this->modelo('Institucion');
        $this->tipoDocumentoModelo = $this->modelo('Tipo_documento');
        $this->tipoEducacionModelo = $this->modelo('Tipo_educacion');
        $this->periodoLectivoModelo = $this->modelo('PeriodoLectivo');
        $this->defNacionalidadModelo = $this->modelo('Def_nacionalidad');
        $this->estudiantePeriodoLectivoModelo = $this->modelo('Estudiante_periodo_lectivo');
    }

    public function fecha_actual($ciudad)
    {
        $meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $dias = array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");
        $mes = $meses[date("n")];

        $fecha_string = "$ciudad,  " . date("j") . " de $mes, " . date("Y");
        return $fecha_string;
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $def_generos = $this->defGeneroModelo->obtenerDefGeneros();
        $tipos_documento = $this->tipoDocumentoModelo->obtenerTiposDocumento();
        $def_nacionalidades = $this->defNacionalidadModelo->obtenerDefNacionalidades();
        $datos = [
            'titulo' => 'Matriculación de Estudiantes',
            'paralelos' => $paralelos,
            'def_generos' => $def_generos,
            'tipos_documento' => $tipos_documento,
            'def_nacionalidades' => $def_nacionalidades,
            'nombreVista' => 'secretaria/matriculacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_paralelo = $_POST['id_paralelo'];

        echo $this->estudianteModelo->listarEstudiantesParalelo($id_paralelo);
    }

    public function contar_estudiantes_por_genero()
    {
        $id_paralelo = $_POST['id_paralelo'];

        echo $this->paraleloModelo->contarEstudiantesPorGenero($id_paralelo);
    }

    public function insert()
    {
        $id_tipo_documento = trim($_POST['id_tipo_documento']);
        $id_def_genero = trim($_POST['id_def_genero']);
        $id_def_nacionalidad = trim($_POST['id_def_nacionalidad']);
        $es_apellidos = strtoupper(trim($_POST['es_apellidos']));
        $es_nombres = strtoupper(trim($_POST['es_nombres']));
        $es_nombre_completo = $es_apellidos . " " . $es_nombres;
        $es_cedula = strtoupper(trim($_POST['es_cedula']));
        $es_email = trim($_POST['es_email']);
        $es_sector = trim($_POST['es_sector']);
        $es_direccion = trim($_POST['es_direccion']);
        $es_telefono = trim($_POST['es_telefono']);
        $es_fec_nacim = trim($_POST['es_fec_nacim']);

        // Primero comprobar si ya existen los nombres y apellidos del estudiante
        if ($this->estudianteModelo->existeNombreEstudiante($es_apellidos, $es_nombres)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe el estudiante en la base de datos...",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } elseif ($this->estudianteModelo->existeNroCedula($es_cedula)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe el número de cédula en la base de datos...",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_tipo_documento' => $id_tipo_documento,
                'id_def_genero' => $id_def_genero,
                'id_def_nacionalidad' => $id_def_nacionalidad,
                'es_apellidos' => $es_apellidos,
                'es_nombres' => $es_nombres,
                'es_nombre_completo' => $es_nombre_completo,
                'es_cedula' => $es_cedula,
                'es_email' => $es_email,
                'es_sector' => $es_sector,
                'es_direccion' => $es_direccion,
                'es_telefono' => $es_telefono,
                'es_fec_nacim' => $es_fec_nacim
            ];

            try {
                $this->estudianteModelo->insertarEstudiante($datos);

                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "El estudiante fue insertado exitosamente.",
                    "tipo_mensaje" => "success"
                );

                echo json_encode($data);
            } catch (\Throwable $th) {
                $data = array(
                    "titulo"       => "Ocurrió un error al tratar de insertar al estudiante.",
                    "mensaje"      => "Error...: " . $th->getMessage(),
                    "tipo_mensaje" => "error"
                );
            }
        }
    }

    public function edit()
    {
        $id_estudiante = $_POST['id_estudiante'];
        echo json_encode($this->estudianteModelo->obtenerEstudiante($id_estudiante));
    }

    public function update()
    {
        $id_estudiante = trim($_POST['id_estudiante']);
        $id_tipo_documento = trim($_POST['id_tipo_documento']);
        $id_def_genero = trim($_POST['id_def_genero']);
        $id_def_nacionalidad = trim($_POST['id_def_nacionalidad']);
        $es_apellidos = strtoupper(trim($_POST['es_apellidos']));
        $es_nombres = strtoupper(trim($_POST['es_nombres']));
        $es_nombre_completo = $es_apellidos . " " . $es_nombres;
        $es_cedula = strtoupper(trim($_POST['es_cedula']));
        $es_email = trim($_POST['es_email']);
        $es_sector = trim($_POST['es_sector']);
        $es_direccion = trim($_POST['es_direccion']);
        $es_telefono = trim($_POST['es_telefono']);
        $es_fec_nacim = trim($_POST['es_fec_nacim']);

        $estudiante_actual = $this->estudianteModelo->obtenerEstudiante($id_estudiante);

        // Primero comprobar si ya existen los nombres y apellidos del estudiante
        if (
            $estudiante_actual->es_apellidos != $es_apellidos &&
            $estudiante_actual->es_nombres != $es_nombres &&
            $this->estudianteModelo->existeNombreEstudiante($es_apellidos, $es_nombres)
        ) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe el estudiante en la base de datos...",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } elseif ($estudiante_actual->es_cedula != $es_cedula && $this->estudianteModelo->existeNroCedula($es_cedula)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe el número de cédula en la base de datos...",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else {
            try {
                $datos = [
                    'id_estudiante' => $id_estudiante,
                    'id_tipo_documento' => $id_tipo_documento,
                    'id_def_genero' => $id_def_genero,
                    'id_def_nacionalidad' => $id_def_nacionalidad,
                    'es_apellidos' => $es_apellidos,
                    'es_nombres' => $es_nombres,
                    'es_nombre_completo' => $es_nombre_completo,
                    'es_cedula' => $es_cedula,
                    'es_email' => $es_email,
                    'es_sector' => $es_sector,
                    'es_direccion' => $es_direccion,
                    'es_telefono' => $es_telefono,
                    'es_fec_nacim' => $es_fec_nacim
                ];

                $this->estudianteModelo->actualizarEstudiante($datos);

                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "El estudiante fue actualizado exitosamente.",
                    "tipo_mensaje" => "success"
                );
                echo json_encode($data);
            } catch (\Exception $e) {
                $data = array(
                    "titulo"       => "Ocurrió un error al tratar de actualizar el estudiante.",
                    "mensaje"      => "Error...: " . $e->getMessage(),
                    "tipo_mensaje" => "error"
                );
                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        $id_estudiante = $_POST['id_estudiante'];
        $id_paralelo = $_POST['id_paralelo'];

        try {
            $this->estudiantePeriodoLectivoModelo->quitarEstudianteParalelo($id_estudiante, $id_paralelo);

            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El estudiante fue des-matriculado exitosamente.",
                "tipo_mensaje" => "success"
            );
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = array(
                "titulo"       => "Ocurrió un error al tratar de des-matricular el estudiante.",
                "mensaje"      => "Error...: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );
            echo json_encode($data);
        }
    }

    public function undelete()
    {
        $id_estudiante_periodo_lectivo = $_POST['id_estudiante_periodo_lectivo'];

        try {
            $this->estudiantePeriodoLectivoModelo->undeleteEstudianteParalelo($id_estudiante_periodo_lectivo);

            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El estudiante fue matriculado nuevamente de manera exitosa.",
                "tipo_mensaje" => "success"
            );
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = array(
                "titulo"       => "Ocurrió un error al tratar de volver a matricular al estudiante.",
                "mensaje"      => "Error...: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );
            echo json_encode($data);
        }
    }

    public function listar_inactivados()
    {
        $id_paralelo = $_POST['id_paralelo'];

        echo $this->estudianteModelo->listarEstudiantesInactivados($id_paralelo);
    }

    public function actualizar_retirado()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $id_estudiante = $_POST["id_estudiante"];
        $estado_retirado = $_POST["es_retirado"];

        $datos = [
            'id_estudiante' => $id_estudiante,
            'estado_retirado' => $estado_retirado,
            'id_periodo_lectivo' => $id_periodo_lectivo
        ];

        try {
            $this->estudiantePeriodoLectivoModelo->actualizarEstadoRetirado($datos);

            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "Se actualizó el estado de retirado de manera exitosa.",
                "tipo_mensaje" => "success"
            );
            echo json_encode($data);
        } catch (\Exception $e) {
            $data = array(
                "titulo"       => "Ocurrió un error al tratar de actualizar el estado de retirado.",
                "mensaje"      => "Error...: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );
            echo json_encode($data);
        }
    }

    public function certificado($id_estudiante_periodo_lectivo)
    {
        $datos = $this->estudiantePeriodoLectivoModelo->obtenerEstudiantePeriodoLectivo($id_estudiante_periodo_lectivo);

        $id_estudiante = $datos->id_estudiante;
        $id_paralelo = $datos->id_paralelo;

        $pdf = new FPDF();
        $pdf->AddPage();

        $datosInstitucion = $this->institucionModelo->obtenerDatosInstitucion();
        $nombreInstitucion = utf8_decode($datosInstitucion->in_nombre);
        $direccionInstitucion = utf8_decode($datosInstitucion->in_direccion);
        $telefonoInstitucion = utf8_decode($datosInstitucion->in_telefono);
        $AMIEInstitucion = $datosInstitucion->in_amie;
        $ciudadInstitucion = $datosInstitucion->in_ciudad;
        $nombreRector = utf8_decode($datosInstitucion->in_nom_rector);

        //Logo Izquierda
        $pdf->Image(RUTA_URL . '/public/img/ministerio.png', 10, 18, 33);

        //Logo Derecha
        $in_logo = $datosInstitucion->in_logo == '' ? 'no-disponible.png' : $datosInstitucion->in_logo;
        $img_logo = RUTA_URL . '/public/uploads/' . $in_logo;

        $pdf->Image($img_logo, 210 - 40, 5, 23);

        $pdf->SetFont('Times', 'B', 14);
        $title = $nombreInstitucion;
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'I', 12);
        $title = $direccionInstitucion;
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'I', 11);
        $title = utf8_decode("Teléfono: ") . $telefonoInstitucion;
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'I', 11);
        $title = "AMIE: " . $AMIEInstitucion;
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln(15);

        $pdf->Line(10, 35, 210 - 10, 35); // 20mm from each edge

        $pdf->SetFont('Times', 'B', 14);
        $title = "CERTIFICADO DE MATRICULA";
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $text = utf8_decode("El Rector(a) de la Institución, previo cumplimiento de las respectivas disposiciones de la Ley Orgánica");
        $pdf->Cell(200, 10, $text, 0, 0, 'L');
        $pdf->Ln(5);

        $text = utf8_decode("de Educación Intercultural y su Reglamento vigentes.");
        $pdf->Cell(200, 10, $text, 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(200, 10, "CERTIFICA:", 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(200, 10, utf8_decode("Que en los archivos de Secretaría, está inscrita la siguiente MATRICULA:"), 0, 0, 'L');
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, "ESTUDIANTE:", 0, 0, 'L');

        //Aqui obtengo el nombre completo del estudiante
        $nombreEstudiante = $this->estudianteModelo->obtenerNombreEstudiante($id_estudiante);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(150, 10, $nombreEstudiante, 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, "NIVEL DE EDUCACION:", 0, 0, 'L');

        //Aqui obtengo el nivel de educación del estudiante
        $nivelEducacion = $this->tipoEducacionModelo->obtenerNombreNivelEducacion($id_paralelo);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(150, 10, utf8_decode($nivelEducacion), 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, "JORNADA:", 0, 0, 'L');

        //Aqui obtengo la jornada
        $jornada = $this->jornadaModelo->obtenerNombreJornada($id_paralelo);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(30, 10, $jornada, 0, 0, 'L');

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(30, 10, utf8_decode("AÑO LECTIVO: "), 0, 0, 'L');

        //Aqui obtengo el año lectivo
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        $periodo_lectivo = $this->periodoLectivoModelo->obtenerPeriodoLectivo($id_periodo_lectivo);
        $anio_lectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(30, 10, "  " . $anio_lectivo, 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, utf8_decode("MATRÍCULA:"), 0, 0, 'L');

        $matricula = $this->estudianteModelo->obtenerNumeroMatricula($id_estudiante, $id_periodo_lectivo);
        $matricula = str_pad($matricula, 4, "0", STR_PAD_LEFT);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(30, 10, $matricula, 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, utf8_decode("GRADO/CURSO:"), 0, 0, 'L');

        $curso = $this->cursoModelo->obtenerNombreCurso($id_paralelo);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(30, 10, $curso, 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(50, 10, "PARALELO:", 0, 0, 'L');

        $paralelo = $this->paraleloModelo->obtenerParalelo($id_paralelo)->pa_nombre;

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(30, 10, "\"" . $paralelo . "\"", 0, 0, 'L');
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFont('Times', '', 12);
        $title = $this->fecha_actual($ciudadInstitucion);
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFont('Times', '', 14);
        $title = $nombreRector;
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Times', 'B', 14);
        $title = "RECTOR(A) DEL PLANTEL";
        $w = $pdf->GetStringWidth($title);
        $pdf->SetX((210 - $w) / 2);
        $pdf->Cell($w, 10, $title, 0, 0, 'C');
        $pdf->Ln();

        header('Content-Type: application/pdf');
        $pdf->Output(utf8_decode($nombreEstudiante) . ".pdf", "I");
    }

    public function buscar_estudiante()
    {
        $patron = $_POST["valor"];

        echo $this->estudianteModelo->buscarEstudiantes($patron);
    }
}
