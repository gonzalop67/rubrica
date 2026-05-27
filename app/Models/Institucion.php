<?php

namespace App\Models;

class Institucion extends Model
{
    protected string $table = 'sw_institucion';
    protected string $primaryKey = 'id_institucion';
    protected array $fillable = [
        'admin_id', 
        'in_nombre',
        'in_direccion',
        'in_telefono',
        'in_regimen',
        'in_nom_rector',
        'in_genero_rector',
        'in_nom_vicerrector',
        'in_genero_vicerrector',
        'in_nom_secretario',
        'in_genero_secretario',
        'in_email',
        'in_url',
        'in_logo',
        'in_amie',
        'in_ciudad',
        'in_copiar_y_pegar'
    ];

}