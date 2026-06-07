<?php

class CreateRolesTable
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
        $sql = "CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            -- Agrega tus columnas aquí
            nombre VARCHAR(32) NOT NULL,
            slug VARCHAR(32) NOT NULL,
            descripcion VARCHAR(64) NOT NULL,
            -- Fin tus columnas
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->connection->query($sql);
    }

    /**
     * Revierte la migración (Eliminar tablas).
     */
    public function down(): void
    {
        $sql = "DROP TABLE IF EXISTS roles;";
        $this->connection->query($sql);
    }
}
