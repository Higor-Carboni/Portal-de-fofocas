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


-- Tabela de Anúncios
CREATE TABLE IF NOT EXISTS anuncios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome da empresa ou anunciante',
    imagem VARCHAR(500) NOT NULL COMMENT 'Caminho da imagem/banner',
    link VARCHAR(500) NOT NULL COMMENT 'URL de destino (ex: site, promoção)',
    texto VARCHAR(255) NOT NULL COMMENT 'Mensagem ou slogan',
    ativo BOOLEAN DEFAULT TRUE COMMENT 'Controla se o anúncio deve ou não aparecer',
    destaque BOOLEAN DEFAULT FALSE COMMENT 'Se verdadeiro, o anúncio aparece com destaque',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de inclusão no sistema',
    valorAnuncio DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Valor cobrado pelo anuncio'
);

-- Inserir categorias padrão
INSERT INTO categorias (nome) VALUES 
('Famosos'),
('TV'),
('Política'),
('Curiosidades');
