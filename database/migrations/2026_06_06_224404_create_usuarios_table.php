<?php

class CreateUsuariosTable
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
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            -- Agrega tus columnas aquí
            username VARCHAR(64) UNIQUE,
            email VARCHAR(64) NULL,
            password VARCHAR(535),
            request_password ENUM('0','1') DEFAULT '0',
            token_password VARCHAR(200) NULL,
            expired_session VARCHAR(40) NULL,
            avatar VARCHAR(100) NULL,
            activo INT(1) UNSIGNED DEFAULT 1,
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
        $sql = "DROP TABLE IF EXISTS usuarios;";
        $this->connection->query($sql);
    }
}
