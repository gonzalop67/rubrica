<?php
class Importar extends Controlador
{
    private $generoModelo;
    private $paraleloModelo;
    private $estudianteModelo;
    private $nacionalidadModelo;
    private $tipoDocumentoModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->generoModelo = $this->modelo('Genero');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->estudianteModelo = $this->modelo('Estudiante');
        $this->nacionalidadModelo = $this->modelo('Nacionalidad');
        $this->tipoDocumentoModelo = $this->modelo('Tipo_documento');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Importar Paralelos desde CSV',
            'paralelos' => $paralelos,
            'nombreVista' => 'secretaria/importar/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function importar()
    {
        $file = $_FILES['student_file']['tmp_name'];
        
        $first = false;
        $file = fopen($file, 'r');

        $contador = 0;
        while ($row = fgetcsv($file)) {
            if (!$first) {
                $first = true;
            } else {
                $td_nombre = $row[0];     // Tipo de documento
                $es_cedula = $row[1];     // Nro. de doc. de identificación
                $dn_nombre = $row[2];     // Nacionalidad
                $es_apellidos = $row[3];  // Apellidos
                $es_nombres = $row[4];    // Nombres
                $dg_nombre = $row[5];     // Género
                $es_email = $row[6];      // Email
                $es_sector = $row[7];     // Sector
                $es_direccion = $row[8];  // Dirección
                $es_telefono = $row[9];   // Teléfono(s)
                $es_fec_nacim = $row[10]; // Fecha de nacimiento

                /* if (!$this->estudianteModelo->existeNroCedula($es_cedula) && !$this->estudianteModelo->existeNombreEstudiante($es_apellidos, $es_nombres)) {
                    // Inserta el nuevo estudiante...
                    // Obtener el id_tipo_documento...
                    $id_tipo_documento = $this->tipoDocumentoModelo->obtenerIdTipoDocumento($td_nombre);
                    // Obtener el id_def_genero
                    $id_def_genero = $this->generoModelo->obtenerIdDefGenero($dg_nombre);
                    // Obtener el id_def_nacionalidad
                    $id_def_nacionalidad = $this->nacionalidadModelo->obtenerIdDefNacionalidad($dn_nombre);

                    $datos = [
                        'id_tipo_documento' => $id_tipo_documento,
                        'id_def_genero' => $id_def_genero,
                        'id_def_nacionalidad' => $id_def_nacionalidad,
                        'es_apellidos' => $es_apellidos,
                        'es_nombres' => $es_nombres,
                        'es_nombre_completo' => $es_apellidos . " " . $es_nombres,
                        'es_cedula' => $es_cedula,
                        'es_email' => $es_email,
                        'es_sector' => $es_sector,
                        'es_direccion' => $es_direccion,
                        'es_telefono' => $es_telefono,
                        'es_fec_nacim' => $es_fec_nacim
                    ];

                    try {
                        $this->estudianteModelo->insertarEstudiante($datos);
                        $contador++;
                    } catch (PDOException $ex) {
                        $data = [
                            "titulo"       => "Ocurrió un error inesperado!",
                            "mensaje"      => "El estudiante no fue insertado exitosamente. Error: " . $ex->getMessage(),
                            "tipo_mensaje" => "error"
                        ];
        
                        echo json_encode($data);
                    }
                    
                } else {
                    // Ya existe el estudiante en la BDD 
                    // Preguntar si ya está matriculado en el presente periodo lectivo

                    $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
                    $id_estudiante = $this->estudianteModelo->obtenerIdEstudiante($es_cedula);

                    if (!$this->estudianteModelo->existeEstudiantePeriodoLectivo($id_estudiante, $id_periodo_lectivo)) {
                        // Asociar Estudiante con Periodo Lectivo en la tabla sw_estudiante_periodo_lectivo

                        try {
                            $this->estudianteModelo->asociarEstudiantePeriodoLectivo($id_estudiante, $id_periodo_lectivo);
                            $contador++;
                        } catch (PDOException $ex) {
                            $data = [
                                "titulo"       => "Ocurrió un error inesperado!",
                                "mensaje"      => "El estudiante no fue insertado exitosamente. Error: " . $ex->getMessage(),
                                "tipo_mensaje" => "error"
                            ];
            
                            echo json_encode($data);
                        }

                    }
                } */
            }
        }

        fclose($file);

        /* $data = [
            "titulo"       => "Importación exitosa.",
            "mensaje"      => "Nro. de estudiantes importados: " . $contador,
            "tipo_mensaje" => "success"
        ];

        echo json_encode($data); */
    }
}
