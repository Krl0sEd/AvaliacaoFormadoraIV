-- phpMyAdmin SQL Dump
-- versão 5.2.1
-- Banco de dados: loja

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Tabela pessoa
-- --------------------------------------------------------
CREATE TABLE pessoa (
  id_pessoa int(11) NOT NULL AUTO_INCREMENT,
  nome varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  cpf varchar(15) DEFAULT NULL,
  telefone varchar(15) DEFAULT NULL,
  senha varchar(30) DEFAULT NULL,
  tipo_usuario ENUM('cliente','entregador','admin') NOT NULL DEFAULT 'cliente',
  PRIMARY KEY (id_pessoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usuário admin pré-criado
INSERT INTO pessoa (nome, email, cpf, telefone, senha, tipo_usuario)
VALUES ('Administrador', 'admin@loja.com', '00000000000', '00000000', 'admin123', 'admin');

-- --------------------------------------------------------
-- Tabela cliente
-- --------------------------------------------------------
CREATE TABLE cliente (
  id_pessoa int(11) NOT NULL,
  metodo_pagamento varchar(30) DEFAULT NULL,
  numero_cartao varchar(14) DEFAULT NULL,
  PRIMARY KEY (id_pessoa),
  CONSTRAINT cliente_ibfk_1 FOREIGN KEY (id_pessoa) REFERENCES pessoa (id_pessoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela entregador
-- --------------------------------------------------------
CREATE TABLE entregador (
  id_pessoa int(11) NOT NULL,
  tipo_veiculo varchar(30) DEFAULT NULL,
  cnh varchar(9) DEFAULT NULL,
  status varchar(15) DEFAULT NULL,
  PRIMARY KEY (id_pessoa),
  CONSTRAINT entregador_ibfk_1 FOREIGN KEY (id_pessoa) REFERENCES pessoa (id_pessoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela produto
-- --------------------------------------------------------
CREATE TABLE produto (
  id_produto int(11) NOT NULL AUTO_INCREMENT,
  nome_produto varchar(120) DEFAULT NULL,
  descricao varchar(300) DEFAULT NULL,
  quant_estoque int(11) DEFAULT NULL,
  preco decimal(10,2) DEFAULT NULL,
  imagem varchar(255) DEFAULT NULL,
  PRIMARY KEY (id_produto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Produtos criados
INSERT INTO produto (id_produto, nome_produto, descricao, quant_estoque, preco, imagem) VALUES
(1, 'Caderno universitário', '200 Folhas, capa dura', 50, 20.90, 'caderno_universitario.jpg'),
(2, 'Caneta Azul', 'Ponta fina 0.7mm', 50, 3.50, 'caneta_azul.jpg'),
(3, 'Lápis Preto', 'Grafite resistente', 100, 1.50, 'lapis_preto.jpg'),
(4, 'Borracha Hello Kitty', 'Macia e eficiente', 20, 3.50, 'borracha_hellokity.jpg'),
(5, 'Marcador de Texto', 'Várias cores neon', 30, 7.50, 'marcadores.jpg'),
(6, 'Estojo Escolar', 'Material resistente, da My melody', 15, 15.90, 'estojo.jpg'),
(7, 'Apontador Metal', 'Muito resistente', 25, 3.00, 'apontador.jpg'),
(8, 'Caderno de Desenho', '50 folhas, papel branco (Hello Kitty)', 12, 10.50, 'caderno_de_desenho.jpg'),
(10, 'Caneta Preta', 'Ponta fina 0.7mm (Para o Enem)', 9, 300.00, 'caneta_preta.jpg');

-- --------------------------------------------------------
-- Tabela endereco
-- --------------------------------------------------------
CREATE TABLE endereco (
  id_endereco int(11) NOT NULL AUTO_INCREMENT,
  id_cliente int(11) NOT NULL,
  cep varchar(8) DEFAULT NULL,
  rua varchar(50) DEFAULT NULL,
  bairro varchar(50) DEFAULT NULL,
  estado varchar(60) DEFAULT NULL,
  cidade varchar(60) DEFAULT NULL,
  complemento varchar(70) DEFAULT NULL,
  PRIMARY KEY (id_endereco),
  KEY id_cliente (id_cliente),
  CONSTRAINT endereco_ibfk_1 FOREIGN KEY (id_cliente) REFERENCES pessoa (id_pessoa) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela pedido
-- --------------------------------------------------------
CREATE TABLE pedido (
  id_pedido int(11) NOT NULL AUTO_INCREMENT,
  id_cliente int(11) NOT NULL,
  id_endereco int(11) NOT NULL,
  id_entregador int(11) DEFAULT NULL, -- AQUI FOI A MUDANÇA (Aceita NULL)
  nome_produto varchar(120) DEFAULT NULL,
  data_pedido date DEFAULT NULL,
  data_entrega date DEFAULT NULL,
  preco_final decimal(10,2) DEFAULT NULL,
  status varchar(25) DEFAULT NULL,
  PRIMARY KEY (id_pedido),
  KEY id_cliente (id_cliente),
  KEY id_endereco (id_endereco),
  KEY id_entregador (id_entregador),
  CONSTRAINT pedido_ibfk_1 FOREIGN KEY (id_cliente) REFERENCES pessoa (id_pessoa) ON DELETE CASCADE,
  CONSTRAINT pedido_ibfk_2 FOREIGN KEY (id_endereco) REFERENCES endereco (id_endereco),
  CONSTRAINT pedido_ibfk_3 FOREIGN KEY (id_entregador) REFERENCES pessoa (id_pessoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela item_pedido
-- --------------------------------------------------------
CREATE TABLE item_pedido (
  id_produto INT(11) NOT NULL,
  id_pedido INT(11) NOT NULL,
  quantidade INT(100) NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_item_produto FOREIGN KEY (id_produto) REFERENCES produto(id_produto)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_item_pedido FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;