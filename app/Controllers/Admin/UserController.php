<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

use Core\Encrypter;

use App\Models\Admin\Perfil;
use App\Models\Admin\Usuario;
use App\Models\Admin\Persona;
use App\Models\Admin\RolUsuario;
use App\Models\Admin\Nacionalidad;
use App\Models\Admin\TipoDocumento;

class UserController extends Controller
{
    protected Perfil $roleModel;
    protected Usuario $userModel;
    protected Persona $personaModel;
    protected RolUsuario $roleUserModel;
    protected Nacionalidad $nacionalidadModel;
    protected TipoDocumento $tipoDocumentoModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->roleModel = new Perfil;
        $this->userModel = new Usuario;
        $this->personaModel = new Persona;
        $this->roleUserModel = new RolUsuario;
        $this->nacionalidadModel = new Nacionalidad;
        $this->tipoDocumentoModel = new TipoDocumento;
    }

    public function index()
    {
        $title = "Lista de Usuarios";
        $search = isset($_GET['search']) ? trim($_GET['search']) : "";

        // Aseguramos limpiar cualquier residuo estructural previo del modelo
        $this->userModel->where = "";
        $this->userModel->values = [];

        // 1. Configuramos el select, el join relacional y el ordenamiento
        $query = $this->userModel
            ->select('sw_usuario.*', 'sw_persona.nombre_completo')
            ->join('sw_persona', 'sw_usuario.persona_id', '=', 'sw_persona.id_persona');

        // 2. Aplicamos la búsqueda usando paréntesis explícitos si el usuario escribe algo
        if ($search !== "") {
            $likeSearch = '%' . $search . '%';
            // Calificamos explícitamente las tablas para evitar errores de ambigüedad en el WHERE
            $query->where = "(sw_persona.nombre_completo LIKE ? OR sw_usuario.us_login LIKE ?)";
            $query->values = [$likeSearch, $likeSearch];
        }

        // 3. Paginar los resultados obtenidos
        $usuarios = $query->orderBy('sw_persona.nombre_completo', 'ASC')
            ->paginate(5);

        return $this->view('admin.usuarios.index', compact('title', 'usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $title = "Nuevo Usuario";
        $roles = $this->roleModel->orderBy('pe_nombre')->get();
        $tipos_documentos = $this->tipoDocumentoModel->orderBy('id_tipo_documento')->get();
        $nacionalidades = $this->nacionalidadModel->orderBy('id_def_nacionalidad')->get();

        return $this->view('admin.usuarios.create', compact('title', 'roles', 'tipos_documentos', 'nacionalidades'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store()
    {
        // 1. CAPTURA EN CRUDO: Evitamos pérdidas de datos de los selects o inputs por filtros estrictos
        $input = $_POST;

        if (!$this->userModel->validate($input)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->userModel->errors
            ]);
        }

        // 2. Encriptación para contraseñas utilizando tu clase estática Encrypter
        $passwordHash = Encrypter::encrypt($input['password'] ?? '');

        // 3. Limpieza y normalización matricial de textos
        $primer_apellido  = preg_replace('/\s+/', ' ', trim($input['primer_apellido'] ?? ''));
        $segundo_apellido = preg_replace('/\s+/', ' ', trim($input['segundo_apellido'] ?? ''));
        $primer_nombre    = preg_replace('/\s+/', ' ', trim($input['primer_nombre'] ?? ''));
        $segundo_nombre   = preg_replace('/\s+/', ' ', trim($input['segundo_nombre'] ?? ''));
        $nombre_completo  = trim($primer_apellido . " " . $segundo_apellido . " " . $primer_nombre . " " . $segundo_nombre);

        // 4. PLANIFICACIÓN DE LA IMAGEN: Calculamos el nombre del archivo, pero NO lo subimos aún
        $imageName = 'default.png';
        $tieneImagen = (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK);

        if ($tieneImagen) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $imageName = 'user_' . uniqid() . '_' . time() . '.' . $ext;
        }

        // 5. Preparación del set de datos para la tabla personas (Sincronizado con tus nuevos selects)
        $datos_persona = [
            'tipo_documento_id'  => (!empty($input['tipo_documento'])) ? (int)$input['tipo_documento'] : null,
            'nacionalidad_id'    => (!empty($input['nacionalidad'])) ? (int)$input['nacionalidad'] : null,
            'dni'                => isset($input['dni']) ? trim($input['dni']) : null, // ◄ Captura pura del DNI
            'primer_nombre'      => $primer_nombre,
            'segundo_nombre'     => $segundo_nombre,
            'primer_apellido'    => $primer_apellido,
            'segundo_apellido'   => $segundo_apellido,
            'nombre_corto'       => trim($input['nombre_corto'] ?? ''),
            'nombre_completo'    => $nombre_completo,
            'titulo'             => trim($input['titulo'] ?? ''),
            'descripcion_titulo' => trim($input['titulo_descripcion'] ?? ''),
            'genero'             => trim($input['genero'] ?? ''),
        ];

        $roles = $input['roles'] ?? [];
        $rutaArchivoSubido = ''; // Guardará la ruta física si se llega a subir para control de rollback

        // 6. PERSISTENCIA CON MANEJO DE TRANSACCIONES NATIVAS REALES
        try {
            // 1. INICIAR TRANSACCIÓN SQL
            $this->userModel->beginTransaction();

            // Ejecutamos la creación en la base de datos de la persona
            $persona = $this->personaModel->create($datos_persona);

            // Bloque de depuración temporal en tu controlador:
            // return json_encode([
            //     'debug' => true,
            //     'id_capturado_por_el_modelo' => $this->personaModel->getInsertId(),
            //     'datos_devueltos' => $persona
            // ]);

            // Captura del ID a través de tu método público
            $idPersona = $this->personaModel->getInsertId();
            if ($idPersona === 0 && is_array($persona)) {
                // Buscar ambas variantes por si acaso
                $idPersona = (int)($persona['id_persona'] ?? $persona['id'] ?? 0);
            }

            if ($idPersona === 0) {
                throw new \Exception("Error al procesar el identificador único de nueva persona (ID devolvió 0).");
            }

            // Datos del nuevo Usuario vinculados a la persona recién creada
            $datos_usuario = [
                'persona_id'  => $idPersona, // ◄ Vinculación relacional correcta
                'us_login'    => trim($input['username'] ?? ''),
                'us_email'    => trim($input['email'] ?? ''),
                'us_password' => $passwordHash,
                'us_foto'     => $imageName,
                'us_activo'   => $input['activo'] ?? '1'
            ];

            // Ejecutamos la creación en la base de datos
            $usuario = $this->userModel->create($datos_usuario);

            // Captura del ID a través de tu método público
            $idUsuario = $this->userModel->getInsertId();
            if ($idUsuario === 0 && is_array($usuario)) {
                $idUsuario = (int)($usuario['id'] ?? 0);
            }

            if ($idUsuario === 0) {
                throw new \Exception("Error al procesar el identificador único del nuevo usuario (ID devolvió 0).");
            }

            // 2. CARGA FÍSICA DE LA IMAGEN: Ahora que la BD aceptó todo, movemos el archivo
            if ($tieneImagen) {
                // __DIR__ está en App/Controllers/Admin (subimos 3 niveles a la raíz)
                $raizProyecto = dirname(__DIR__, 3);
                $directorioUploads = $raizProyecto . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';

                if (!is_dir($directorioUploads)) {
                    mkdir($directorioUploads, 0777, true);
                }

                $destino = $directorioUploads . DIRECTORY_SEPARATOR . $imageName;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destino)) {
                    $rutaArchivoSubido = $destino; // Almacenamos la ruta para control del catch
                } else {
                    throw new \Exception("No se pudo guardar físicamente la imagen en el servidor. Verifique rutas.");
                }
            }

            // 3. Sincronizar los roles (Bajo la misma transacción SQL)
            $this->roleUserModel->sync($idUsuario, $roles);

            // 4. CONFIRMAR CAMBIOS SI TODO SALIÓ BIEN
            $this->userModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Usuario procesado con éxito.'
            ]);
        } catch (\Throwable $e) {
            // 5. REVERTIR TRANSACCIÓN SQL EN CASO DE FALLAS
            $this->userModel->rollBack();

            // LIMPIEZA DE BASURA EN DISCO: Borramos el archivo físico para evitar imágenes huérfanas
            if (!empty($rutaArchivoSubido) && file_exists($rutaArchivoSubido)) {
                unlink($rutaArchivoSubido);
            }

            return json_encode([
                'ok' => false,
                'mensaje' => "Ocurrió un error inesperado: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     */
    public function edit(int $id)
    {
        $title = "Editar Usuario";
        $usuario = $this->userModel
            ->select('sw_usuario.id_usuario', 
            'persona_id', 
            'us_login', 
            'us_email', 
            'us_password', 
            'us_activo', 
            'us_foto', 
            'sw_persona.*')
            ->join('sw_persona', 'sw_usuario.persona_id', '=', 'sw_persona.id_persona')
            ->where('sw_usuario.id_usuario', $id)
            ->first();
        $password = Encrypter::decrypt($usuario['us_password'] ?? '');
        $usuario['us_password'] = $password;
        
        $roles = $this->roleModel->orderBy('pe_nombre')->get();
        $userRoles = $this->userModel->getRoleIds($id);
        $tipos_documentos = $this->tipoDocumentoModel->orderBy($this->tipoDocumentoModel->getPrimaryKey())->get();
        $nacionalidades = $this->nacionalidadModel->orderBy($this->nacionalidadModel->getPrimaryKey())->get();
        // show($nacionalidades);
        // die();

        return $this->view('admin.usuarios.edit', compact('title', 'usuario', 'userRoles', 'roles', 'tipos_documentos', 'nacionalidades'));
    }
}
