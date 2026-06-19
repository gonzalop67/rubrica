<?php

class RolSeeder
{
    /**
     * Ejecuta el seeder para poblar la base de datos.
     *
     * @param mysqli $mysqli Conexión nativa a la base de datos.
     * @return void
     */
    public function run(mysqli $mysqli)
    {
        // 1. Nombre de la tabla física asociada a los roles
        $tabla = 'roles';

        // 2. Definición de los roles iniciales de SIAE 2026
        $roles = [
            ['nombre' => 'Administrador', 'slug' => 'administrador', 'descripcion' => 'Acceso total a todos los módulos del sistema administrativo.'],
            ['nombre' => 'Autoridad', 'slug' => 'Autoridad', 'descripcion' => 'Acceso a reportes de gestión educativa.'],
            ['nombre' => 'Coordinador',  'slug' => 'coordinador', 'descripcion' => 'Gestión y supervisión de ofertas educativas y asignaciones.'],
            ['nombre' => 'Docente',      'slug' => 'docente', 'descripcion' => 'Registro de calificaciones, asistencias y rúbricas.'],
            ['nombre' => 'Estudiante',   'slug' => 'estudiante', 'descripcion' => 'Consulta de historial académico y perfiles.'],
            ['nombre' => 'Secretaría',   'slug' => 'secretaria', 'descripcion' => 'Acceso a Matriculación y Reportes.'],
            ['nombre' => 'Tutor',   'slug' => 'tutor', 'descripcion' => 'Acceso a reportes de calificaciones y comportamiento.'],
        ];

        echo "    -> Insertando roles base en la tabla [{$tabla}]...\n";

        // 🔥 MEJORA: Evita colisiones de ejecución masiva
        $stmt = $mysqli->prepare("INSERT IGNORE INTO `{$tabla}` (nombre, slug, descripcion) VALUES (?, ?, ?)");

        foreach ($roles as $rol) {
            $stmt->bind_param('sss', $rol['nombre'], $rol['slug'], $rol['descripcion']);
            $stmt->execute();
        }
        $stmt->close();
        echo "\e[32m    ✅ Roles insertados correctamente.\e[0m\n";
    }
}
