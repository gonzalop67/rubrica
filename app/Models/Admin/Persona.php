<?php

namespace App\Models\Admin;

use App\Models\Model;

class Persona extends Model
{
    protected string $table = 'sw_persona';
    protected string $primaryKey = 'id_persona';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'tipo_documento_id',
        'nacionalidad_id',
        'dni',
        'titulo',
        'descripcion_titulo',
        'primer_apellido',
        'segundo_apellido',
        'primer_nombre',
        'segundo_nombre',
        'nombre_corto',
        'nombre_completo',
        'genero',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'sector',
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = false; 
}
