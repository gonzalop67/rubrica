<?php

class CreateUsuariosRolesTable
{
    /**
     * Propiedad para almacenar la conexión que inyectará el comando migrate
     */
    public $connection;

    /**
     * Ejecuta la migración (Crear o modificar tablas).
     */
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS usuarios_roles (
            usuario_id INT NOT NULL,
            rol_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Único campo de auditoría útil (Opcional)

            -- Llave primaria compuesta para impedir que se asigne el mismo rol al mismo usuario dos veces
            PRIMARY KEY (usuario_id, rol_id), 

            INDEX idx_rol (rol_id),

            CONSTRAINT fk_usuarios_roles_usuario
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,

            CONSTRAINT fk_usuarios_roles_rol
                FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->connection->query($sql);
    }

    /**
     * Revierte la migración (Eliminar tablas).
     */
    public function down(): void
    {
        $sql = "DROP TABLE IF EXISTS usuarios_roles;";
        $this->connection->query($sql);
    }
}
