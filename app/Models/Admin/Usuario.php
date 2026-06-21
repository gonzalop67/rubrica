<?php

namespace App\Models\Admin;

use App\Models\Model;

class Usuario extends Model
{
    protected string $table = 'sw_usuario';
    protected string $primaryKey = 'id_usuario';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'institucion_id',
        'us_titulo',
        'us_titulo_descripcion',
        'us_apellidos',
        'us_nombres',
        'us_shortname',
        'us_fullname',
        'us_login',
        'us_email',
        'us_password',
        'request_password',
        'token_password',
        'expired_session',
        'us_foto',
        'us_genero',
        'us_activo',
        'deleted_at'
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = true; 
}
