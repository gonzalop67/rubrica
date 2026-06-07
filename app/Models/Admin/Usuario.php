<?php

namespace App\Models\Admin;

use App\Models\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    // protected string $primaryKey = 'id';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'username',
        'email',
        'password',
        'request_password',
        'token_password',
        'expired_session',
        'avatar',
        'activo',
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = true; 
}
