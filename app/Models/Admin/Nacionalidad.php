<?php

namespace App\Models\Admin;

use App\Models\Model;

class Nacionalidad extends Model
{
    protected string $table = 'sw_def_nacionalidad';
    protected string $primaryKey = 'id_def_nacionalidad';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'dn_nombre'
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = false; 
}
