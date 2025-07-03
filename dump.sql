-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS portal_noticias;
USE portal_noticias;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil varchar(20) DEFAULT 'normal'
);

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

-- Tabela de notícias com categoria integrada
CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    noticia TEXT NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    autor INT NOT NULL,
    imagem VARCHAR(255),
    categoria_id INT NOT NULL,
    FOREIGN KEY (autor) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Inserir categorias padrão
INSERT INTO categorias (nome) VALUES 
('Famosos'),
('TV'),
('Política'),
('Curiosidades');

CREATE TABLE IF NOT EXISTS anuncio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    imagem VARCHAR(255),
    link VARCHAR(255),
    texto VARCHAR(255),
    ativo BOOLEAN DEFAULT 1,
    destaque BOOLEAN DEFAULT 0,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    valorAnuncio DECIMAL(10,2) DEFAULT 0.00
);
T7