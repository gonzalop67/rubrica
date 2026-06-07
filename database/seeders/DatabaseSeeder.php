<?php

class DatabaseSeeder
{
    /**
     * Método central de ejecución (Estilo Laravel).
     */
    public function run(mysqli $mysqli, bool $refresh = false): void
    {
        if ($refresh) {
            echo "\e[34m  -> [Refresh] Vaciando tablas en orden inverso de integridad...\e[0m\n";
            
            // 1. Apagar temporalmente la revisión de llaves foráneas en MySQL
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
            
            // 2. Limpiar las tablas de raíz mediante DELETE (Más seguro ante restricciones de metadatos)
            $mysqli->query("DELETE FROM `usuarios_roles`;");
            $mysqli->query("DELETE FROM `usuarios`;");
            $mysqli->query("DELETE FROM `roles`;");
            
            // 3. Resetear los contadores autoincrementales a 1
            $mysqli->query("ALTER TABLE `usuarios` AUTO_INCREMENT = 1;");
            $mysqli->query("ALTER TABLE `roles` AUTO_INCREMENT = 1;");
            
            // 4. Reactivar la seguridad de llaves foráneas de forma obligatoria
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
            
            echo "  \e[32m  ✅ Ecosistema de tablas limpio y reseteado a ID 1.\e[0m\n\n";
        }

        // Ejecución secuencial de los seeders
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
