<?php

use Core\Encrypter;

class AdminUserSeeder
{
    /**
     * Ejecuta el seeder para poblar la base de datos.
     *
     * @param mysqli $mysqli Conexión nativa a la base de datos.
     * @return void
     */
    public function run(mysqli $mysqli)
    {
        $tablaPivot    = 'usuarios_roles';

        // 1. 🔥 CORRECCIÓN: Buscamos el ID del rol usando $mysqli en lugar de $this->connection
        echo "    -> Buscando el ID del rol 'Administrador'...\n";
        $resultadoRol = $mysqli->query("SELECT id FROM roles WHERE nombre = 'Administrador' LIMIT 1");
        $rol = $resultadoRol->fetch_assoc();

        if (!$rol) {
            throw new \Exception("Error crítico: No se encontró el rol 'Administrador'. Asegúrate de que RolSeeder se ejecute antes en tu DatabaseSeeder.");
        }
        
        $rolId = (int)$rol['id'];

        // 2. Datos de tu usuario administrador real
        $username = "Ing. Gonzalo Peñaherrera E.";
        $email = "gonzalop67@gmail.com";
        $password = Encrypter::encrypt('gP67M24e$+');

        echo "    -> Insertando usuario administrador base...\n";

        // 🔥 MEJORA: Usamos INSERT IGNORE para hacerlo tolerante a fallos de caché
        $stmt = $mysqli->prepare("INSERT IGNORE INTO usuarios (username, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new \Exception("Error al preparar consulta de usuario: " . $mysqli->error);
        }
        $stmt->bind_param('sss', $username, $email, $password);
        $stmt->execute();
        
        // Obtener el ID numérico asignado por MySQL
        $usuarioId = $mysqli->insert_id;
        $stmt->close();

        // Si el insert_id devuelve 0 significa que se ignoró el insert porque ya existía en caché.
        // Lo rescatamos mediante un SELECT rápido para obtener su ID real.
        if ($usuarioId === 0) {
            $checkUser = $mysqli->query("SELECT id FROM usuarios WHERE username = 'Ing. Gonzalo Peñaherrera E.' LIMIT 1");
            $userRow = $checkUser->fetch_assoc();
            $usuarioId = (int)$userRow['id'];
        }

        // 3. Vincular el usuario con su respectivo rol en la tabla pivotante
        echo "    -> Vinculando usuario ID [{$usuarioId}] con el rol Administrador ID [{$rolId}]...\n";
        
        $stmtPivot = $mysqli->prepare("INSERT INTO `{$tablaPivot}` (usuario_id, rol_id) VALUES (?, ?)");
        if (!$stmtPivot) {
            throw new \Exception("Error al preparar consulta en tabla pivot: " . $mysqli->error);
        }
        $stmtPivot->bind_param('ii', $usuarioId, $rolId);
        $stmtPivot->execute();
        $stmtPivot->close();

        echo "\e[32m    ✅ Administrador y rol acoplados con éxito en el sistema.\e[0m\n";
    }
}
