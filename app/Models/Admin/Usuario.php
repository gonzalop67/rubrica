<?php

namespace App\Models\Admin;

use App\Models\Model;

class Usuario extends Model
{
    protected string $table = 'sw_usuario';
    protected string $primaryKey = 'id_usuario';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'persona_id',
        'us_login',
        'us_email',
        'us_password',
        'request_password',
        'token_password',
        'expired_session',
        'us_foto',
        'us_activo',
        'deleted_at'
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = true; 

    // Validación estricta y sincronizada con los campos del Formulario (JS)
    public function validate(array $data, ?int $id = null): bool
    {
        $this->errors = [];
        $is_updating = ($id !== null);

        // 1. Recuperar y limpiar datos del FormData
        $tipo_documento     = trim($data['tipo_documento'] ?? '');
        $nacionalidad       = trim($data['nacionalidad'] ?? '');
        $dni                = trim($data['dni'] ?? '');
        $titulo             = trim($data['titulo'] ?? '');
        $descripcion_titulo = trim($data['descripcion_titulo'] ?? '');
        $primer_apellido    = preg_replace('/\s+/', ' ', trim($data['primer_apellido'] ?? ''));
        $segundo_apellido   = preg_replace('/\s+/', ' ', trim($data['segundo_apellido'] ?? ''));
        $primer_nombre      = preg_replace('/\s+/', ' ', trim($data['primer_nombre'] ?? ''));
        $segundo_nombre     = preg_replace('/\s+/', ' ', trim($data['segundo_nombre'] ?? ''));
        $nombre_corto       = preg_replace('/\s+/', ' ', trim($data['nombre_corto'] ?? ''));
        $username           = preg_replace('/\s+/', ' ', trim($data['username'] ?? ''));
        $email              = trim($data['email'] ?? '');
        $password           = trim($data['password'] ?? '');
        $roles              = $data['roles'] ?? [];

        // 2. Bloque de Validaciones Estrictas

        // Validación de Tipo de Documento
        if ($tipo_documento === '') {
            $this->errors['tipo_documento'] = "Por favor, seleccione un tipo de documento de la lista.";
        }

        // Validación de Nacionalidad
        if ($nacionalidad === '') {
            $this->errors['nacionalidad'] = "Por favor, seleccione una nacionalidad de la lista.";
        }

        // dni
        if (empty($dni)) {
            $this->errors['dni'] = "El campo DNI es obligatorio";
        }

        // Título (Abreviatura)
        if (empty($titulo)) {
            $this->errors['titulo'] = "El campo Título es obligatorio.";
        } elseif (!preg_match('/^[a-zA-Z\.]{3,7}$/', $titulo)) {
            $this->errors['titulo'] = "La abreviatura del título tiene que ser de 3 a 7 caracteres (alfabéticos y punto).";
        }

        // Descripción del Título
        if (!empty($descripcion_titulo) && !preg_match('/^[a-zA-ZÀ-ÿ\s\.\,\-\_\:\;\(\)\n]{4,500}$/u', $descripcion_titulo)) {
            $this->errors['descripcion_titulo'] = "La descripción del título tiene que ser de 4 a 500 caracteres.";
        }

        // Apellidos
        if (empty($primer_apellido)) {
            $this->errors['primer_apellido'] = "El campo Primer Apellido es obligatorio.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]{3,32}$/u', $primer_apellido)) {
            $this->errors['primer_apellido'] = "El primer apellido del usuario debe contener de 3 a 32 caracteres alfabéticos.";
        }

        // Nombres
        if (empty($primer_nombre)) {
            $this->errors['primer_nombre'] = "El campo Primer Nombre es obligatorio.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]{3,32}$/u', $primer_nombre)) {
            $this->errors['primer_nombre'] = "El primer nombre del usuario debe contener de 3 a 32 caracteres alfabéticos.";
        }

        // Procesamiento matricial de nombres combinados y verificación de duplicados
        if (empty($this->errors['primer_apellido']) && empty($this->errors['primer_nombre'])) {
            $primer_apellido  = trim($primer_apellido);
            $segundo_apellido = trim($segundo_apellido);
            $primer_nombre    = trim($primer_nombre);
            $segundo_nombre   = trim($segundo_nombre);

            $nombre_completo  = $primer_apellido . " " . $segundo_apellido . " " . $primer_nombre . " " . $segundo_nombre;
            $nombre_completo  = preg_replace('/\s+/', ' ', $nombre_completo);

            // =================================================================
            // EXTRACTOR DEL ID DE LA PERSONA REAL (SIN DETENIMIENTO)
            // =================================================================
            $id_persona_excluir = null;

            if ($is_updating) {
                $sqlP = "SELECT `persona_id` FROM `usuarios` WHERE `id` = ?";
                $stmtP = $this->connection->prepare($sqlP);
                if ($stmtP) {
                    $stmtP->bind_param('i', $id); // Sincronizado con la variable de cabecera
                    $stmtP->execute();
                    $resP = $stmtP->get_result();
                    $filaUsuario = $resP ? $resP->fetch_assoc() : null;

                    // Extraemos el ID real de la persona para la exclusión en la tabla personas
                    $id_persona_excluir = isset($filaUsuario['persona_id']) ? (int)$filaUsuario['persona_id'] : null;

                    if ($resP) $resP->free();
                    $stmtP->close();
                }
            }
            // =================================================================

            // 2. Invocamos la función estática pasándole la PK real de la tabla personas
            // CAMBIA EL ÚLTIMO PARÁMETRO 'id' por el nombre real de la PK de tu tabla personas si no se llama 'id' (ej: 'id_persona')
            if (!empty($nombre_completo) && self::checkExists($this->connection, 'sw_persona', 'nombre_completo', $nombre_completo, $id_persona_excluir, 'id')) {
                $this->errors['nombre_completo'] = "Ya existe una persona registrada con este Nombre Completo en el sistema.";
            }

            // =================================================================

            // Manejo seguro del nombre_corto automático si viene vacío del cliente
            if ($nombre_corto !== "") {
                $nombre_corto = preg_replace('/\s+/', ' ', $nombre_corto);
            } else {
                $apellidos     = explode(" ", $primer_apellido);
                $nombres       = explode(" ", $primer_nombre);
                $p_nombre      = $nombres[0] ?? '';
                $p_apellido    = $apellidos[0] ?? '';
                $nombre_corto  = trim($titulo . " " . $p_nombre . " " . $p_apellido);
            }
        }

        // =====================================================================
        // Ejemplo 2: Validar el Username (Tabla usuarios, usando el ID del usuario)
        // =====================================================================
        if (empty($username)) {
            $this->errors['username'] = "El campo Nombre de Usuario es obligatorio.";
        } elseif (!preg_match('/^[a-zA-Z0-9\_\-]{4,16}$/', $username)) {
            $this->errors['username'] = "El Nombre de Usuario debe tener entre 4 y 16 caracteres sin espacios.";
        } elseif (self::checkExists($this->connection, 'usuarios', 'username', $username, $id)) {
            // ◄ Aquí evalúa la tabla usuarios con el ID del usuario directamente
            $this->errors['username'] = "Ya existe el Nombre de Usuario en la base de datos.";
        }

        // =====================================================================
        // Ejemplo 3: Validar el Email (Tabla usuarios, usando el ID del usuario)
        // =====================================================================
        if (empty($email)) {
            $this->errors['email'] = "El campo Email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "El correo electrónico ingresado no es válido.";
        } elseif (self::checkExists($this->connection, 'usuarios', 'email', $email, $id)) {
            $this->errors['email'] = "Ya existe el correo electrónico.";
        }

        // Password condicional
        if (!$is_updating && empty($password)) {
            $this->errors['password'] = 'El campo Password es obligatorio.';
        } elseif (!empty($password)) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
                $this->errors['password'] = 'Contraseña débil [Mínimo 8 caracteres, mayúscula, minúscula, número y cualquier símbolo].';
            }
        }

        // Checkboxes (Perfiles)
        if (!is_array($roles) || count($roles) === 0) {
            $this->errors['roles'] = "Debe asignar al menos un rol al usuario.";
        }

        // Validación Imbatible de Imágenes (MIME Type real mediante finfo cerrado)
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileSize    = $_FILES['avatar']['size'];

            if ($fileSize > 2 * 1024 * 1024) {
                $this->errors['avatar'] = "La imagen es muy pesada. Máximo permitido: 2MB.";
            } else {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($fileTmpPath);

                $mimesPermitidos = [
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/gif',
                    'image/webp'
                ];

                if (!in_array($mimeType, $mimesPermitidos)) {
                    $this->errors['avatar'] = "El archivo no es una imagen válida (Solo JPG, PNG, GIF, WEBP).";
                }
            }
        }

        return empty($this->errors);
    }

    public function getRoleIds(string $userId)
    {
        $sql = "SELECT id_perfil FROM sw_usuario_perfil WHERE id_usuario = ?";
        $data = $this->query($sql, [$userId])->get();

        // Aquí es donde simulamos el pluck('id')->toArray()
        return array_column($data, 'id_perfil');
    }
}
