<?php

namespace App\Models\Admin;

use App\Models\Model;

class UsuariosRoles extends Model
{
    protected string $table = 'sw_usuario_perfil';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = ['id_usuario', 'id_perfil'];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = false; 
}
