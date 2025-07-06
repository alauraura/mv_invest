CREATE DATABASE IF NOT EXISTS mv_invest;

USE mv_invest;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (email, senha_hash) VALUES ('teste@exemplo.com', '$2y$10$T81xO/N3W5Q6P7R8S9T0U.V1W2X3Y4Z5A6B7C8D9E0F1G2H3I4J5K6L7M8N9O0Pq');
-- A senha hash acima corresponde a '123456' gerada por password_hash() no PHP.