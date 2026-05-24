<?php
class PDF extends FPDF
{
    var $nombreInstitucion = "";
    var $direccionInstitucion = "";
    var $telefonoInstitucion = "";
    var $AMIEInstitucion = "";
    var $ciudadInstitucion = "";
    var $nombreRector = "";
    var $nombreSecretario = "";
    var $logoInstitucion = "";
    var $nombrePeriodoLectivo = "";
    var $nombreCurso = "";
    var $jornada = "";
    var $paralelo = "";

    //Cabecera de pagina
    function Header()
    {
        //Logo Izquierda
        $this->Image(RUTA_URL . '/public/img/ministerio.png', 10, 18, 33);
        //Logo Derecha
        $this->Image($this->logoInstitucion, 210 - 40, 5, 23);
        //Nombre de la IE
        $this->SetFont('Times', 'B', 14);
        $title = $this->nombreInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Direccion de la IE
        $this->SetFont('Arial', 'I', 12);
        $title = $this->direccionInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Telefono de la IE
        $this->SetFont('Arial', 'I', 11);
        $title = utf8_decode("Teléfono: ") . $this->telefonoInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //AMIE de la IE
        $this->SetFont('Arial', 'I', 11);
        $title = "AMIE: " . $this->AMIEInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(10);
        //Linea de division
        $this->Line(10, 35, 210 - 10, 35); // 20mm from each edge
        //Titulo del Reporte
        $this->SetFont('Times', 'B', 14);
        $title = "NOMINA DE MATRICULADOS";
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Año Lectivo
        $this->SetFont('Times', '', 12);
        $title = utf8_decode("AÑO LECTIVO: " . $this->nombrePeriodoLectivo);
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);
        //Nombre del Curso
        $this->SetFont('Times', '', 12);
        $title = utf8_decode($this->nombreCurso);
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(7);
        //Jornada
        $this->SetFont('Arial', 'B', 10);
        $this->SetX(15);
        $this->Cell(60, 10, "JORNADA " . $this->jornada, 0, 0, 'L');
        //Paralelo
        $this->SetX(155);
        $this->Cell(60, 10, "PARALELO \"" . $this->paralelo . "\"", 0, 0, 'L');
        $this->Ln(7);
        //Cabeceras de las columnas
        $this->SetFont('Arial', 'B', 9);
        $y = $this->GetY();
        $this->Cell(14, 8, utf8_decode("N°"), 1, 0, 'C');
        $this->Multicell(80, 4, utf8_decode("NÓMINA DE ESTUDIANTES") . "\n(apellidos y nombres)", 1, 'C');
        $this->SetXY(104, $y);
        $this->Multicell(24, 4, utf8_decode("N° DE\nMATRÍCULA"), 1, 'C');
        $this->SetXY(128, $y);
        $this->Cell(62, 8, utf8_decode("CÉDULA/PASAPORTE"), 1, 0, 'C');
        $this->Ln();
    }

    //Pie de pagina
    function Footer()
    {
        //Posicion: a 1,5 cm del final
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Numero de pagina
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

class Reporte_nomina_matriculados extends Controlador
{
    private $db;
    private $cursoModelo;
    private $jornadaModelo;
    private $paraleloModelo;
    private $institucionModelo;
    private $periodoLectivoModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->db = new Base;
        $this->cursoModelo = $this->modelo('Curso');
        $this->jornadaModelo = $this->modelo('Jornada');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->institucionModelo = $this->modelo('Institucion');
        $this->periodoLectivoModelo = $this->modelo('PeriodoLectivo');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Nómina de matriculados',
            'paralelos' => $paralelos,
            'nombreVista' => 'secretaria/nomina_matriculados/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function reporte()
    {
        // Variables enviadas mediante POST
        $id_paralelo = $_POST["id_paralelo"];

        $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

        $datosInstitucion = $this->institucionModelo->obtenerDatosInstitucion();

        $nombreInstitucion = utf8_decode($datosInstitucion->in_nombre);
        $direccionInstitucion = utf8_decode($datosInstitucion->in_direccion);
        $telefonoInstitucion = utf8_decode($datosInstitucion->in_telefono);
        $AMIEInstitucion = $datosInstitucion->in_amie;
        $ciudadInstitucion = $datosInstitucion->in_ciudad;
        $nombreRector = utf8_decode($datosInstitucion->in_nom_rector);
        $nombreSecretario = utf8_decode($datosInstitucion->in_nom_secretario);

        $in_logo = $datosInstitucion->in_logo == '' ? 'no-disponible.png' : $datosInstitucion->in_logo;
        $img_logo = RUTA_URL . '/public/uploads/' . $in_logo;

        //Creacion del objeto de la clase heredada
        $pdf = new PDF('P');

        $pdf->nombreInstitucion = $nombreInstitucion;
        $pdf->direccionInstitucion = $direccionInstitucion;
        $pdf->telefonoInstitucion = $telefonoInstitucion;
        $pdf->AMIEInstitucion = $AMIEInstitucion;
        $pdf->ciudadInstitucion = $ciudadInstitucion;
        $pdf->nombreRector = $nombreRector;
        $pdf->nombreSecretario = $nombreSecretario;
        $pdf->logoInstitucion = $img_logo;

        $id_curso = $this->paraleloModelo->getCursoId($id_paralelo);
        $nombreCurso = $this->cursoModelo->getNombreCurso($id_curso, 1);

        $pdf->nombreCurso = $nombreCurso;

        $periodo_lectivo = $this->periodoLectivoModelo->obtenerPeriodoLectivo($id_periodo_lectivo);
        $nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
        $pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;

        $jornada = $this->jornadaModelo->obtenerNombreJornada($id_paralelo);
        $paralelo = $this->paraleloModelo->obtenerParalelo($id_paralelo)->pa_nombre;

        $pdf->jornada = $jornada;
        $pdf->paralelo = $paralelo;

        $pdf->AliasNbPages();
        $pdf->AddPage();

        $this->db->query("SELECT es_apellidos, 
                                 es_nombres, 
                                 nro_matricula,
                                 es_cedula
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep 
                           WHERE e.id_estudiante = ep.id_estudiante
                             AND ep.id_paralelo = $id_paralelo   
                             AND ep.id_periodo_lectivo = $id_periodo_lectivo 
                             AND activo = 1
                           ORDER BY es_apellidos, es_nombres");
        $registros = $this->db->registros();
        $contador = 0;
        $pdf->SetFont('Times', '', 9);
        foreach ($registros as $row) {
            $contador++;
            if ($contador % 32 == 0) $pdf->AddPage();
            $nombreEstudiante = $row->es_apellidos . " " . $row->es_nombres;
            $matricula = str_pad($row->nro_matricula, 4, "0", STR_PAD_LEFT);
            $cedula = $row->es_cedula;
            $pdf->Cell(14, 6, $contador, 1, 0, 'C');
            if (strlen($nombreEstudiante) > 40) $pdf->SetFont('Times', '', 8);
            $pdf->Cell(80, 6, utf8_decode($nombreEstudiante), 1, 0, 'L');
            $pdf->SetFont('Times', '', 9);
            $pdf->Cell(24, 6, $matricula, 1, 0, 'C');
            $pdf->Cell(62, 6, $cedula, 1, 0, 'L');
            $pdf->Ln();
        }

        //Firmas de la Autoridad y/o del Secretario (a)
        $y = $pdf->GetY() + 10;
        $pdf->Line(24, $y, 94, $y);
        $pdf->Line(114, $y, 184, $y);
        $pdf->SetY($y + 1);
        $pdf->Cell(14, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, $nombreRector, 0, 0, 'C');
        $pdf->Cell(20, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, $nombreSecretario, 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(14, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, "RECTOR (A)", 0, 0, 'C');
        $pdf->Cell(20, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, "SECRETARIO (A)", 0, 0, 'C');

        $pdf->Output();
    }
}
