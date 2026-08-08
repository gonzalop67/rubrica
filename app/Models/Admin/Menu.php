<?php

namespace App\Models\Admin;

use App\Models\Model;

class Menu extends Model
{
    protected string $table = 'sw_menu';
    protected string $primaryKey = 'id_menu';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'mnu_texto',
        'mnu_enlace',
        'mnu_link',
        'mnu_nivel',
        'mnu_padre',
        'mnu_publicado',
        'mnu_icono'
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = true; 
}
