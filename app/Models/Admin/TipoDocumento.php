<?php

namespace App\Models\Admin;

use App\Models\Model;

class TipoDocumento extends Model
{
    protected string $table = 'sw_tipo_documento';
    protected string $primaryKey = 'id_tipo_documento';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'td_nombre'
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = false; 
}
