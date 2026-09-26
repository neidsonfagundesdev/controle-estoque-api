CREATE DATABASE IF NOT EXISTS controle_estoque
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE controle_estoque;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL
);