CREATE TABLE
    sw_comentarios (
        id_comentario INT AUTO_INCREMENT PRIMARY KEY,
        parent_id INT DEFAULT NULL, -- ID del comentario al que responde
        autor_id INT NOT NULL, -- Autor del comentario
        recipient_id INT DEFAULT NULL, -- Usuario al que se dirige la respuesta o el comentario original
        is_read TINYINT(1) DEFAULT 0,  -- 0: No leído, 1: Leído
        contenido TEXT NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES sw_comentarios (id_comentario) ON DELETE CASCADE
    );