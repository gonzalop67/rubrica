<?php

class DatabaseSeeder
{
    /**
     * Método central de ejecución (Estilo Laravel).
     * Recibe la conexión nativa mysqli por parámetro.
     */
    public function run(mysqli $mysqli): void
    {
        global $flags;
        $refresh = is_array($flags) && in_array('--refresh', $flags);

        // 🔥 CENTRALIZACIÓN DEL REFRESH: Limpieza absoluta del mapa de datos
        if ($refresh) {
            echo "\e[34m  -> [Refresh] Vaciando tablas en orden inverso de integridad...\e[0m\n";
            
            // 1. Apagar temporalmente la revisión de llaves foráneas
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
            
            // 2. TRUNCATE en orden inverso (Hijo -> Padre) para respetar las reglas RESTRICT
            $mysqli->query("TRUNCATE TABLE `usuarios_roles`;");
            $mysqli->query("TRUNCATE TABLE `usuarios`;");
            $mysqli->query("TRUNCATE TABLE `roles`;");
            
            // 3. Forzar confirmación en el disco físico y encender de nuevo la protección
            $mysqli->query("COMMIT;");
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
            
            echo "  \e[32m  ✅ Ecosistema de tablas limpio y reseteado a ID 1.\e[0m\n\n";
        }

        // Ejecución secuencial de los seeders en orden de jerarquía (Padre -> Hijo)
        $this->call($mysqli, [
            RolSeeder::class,
            AdminUserSeeder::class,
        ]);
    }

    /**
     * Método auxiliar encargado de instanciar y ejecutar seeders secundarios.
     */
    protected function call(mysqli $mysqli, array $seeders): void
    {
        foreach ($seeders as $seederClass) {
            if (!class_exists($seederClass)) {
                $file = __DIR__ . '/' . $seederClass . '.php';
                if (file_exists($file)) {
                    require_once $file;
                } else {
                    echo "\e[31mError:\e[0m No se encontró el archivo para la clase [{$seederClass}]\n";
                    continue;
                }
            }

            echo "  -> Ejecutando Seeder: {$seederClass}...\n";
            $instance = new $seederClass();
            $instance->run($mysqli);
        }
    }
}
