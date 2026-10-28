-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/09/2026 às 01:31
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loja_roupas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `ativo`) VALUES
(1, 'Fantasias Completas', 1),
(2, 'Uniformes de Anime e Manga', 1),
(3, 'Capas e Mantos', 1),
(4, 'Acessorios', 0),
(5, 'Perucas', 1),
(6, 'Calcados Cosplay', 1),
(7, 'Props Cenograficos', 1),
(8, 'Armaduras', 0),
(9, 'Materiais para Cosplay', 1),
(10, 'Kits Cosplay', 0),
(11, 'Promocoes', 0),
(12, 'AMOS', 0),
(13, 'Bruno teste', 0),
(14, 'ia', 0),
(15, 'ieie', 0),
(16, 'Alice', 1),
(17, 'blablabla', 0),
(18, 'Amosssss', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf_cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `cpf_cnpj`, `telefone`, `email`, `endereco`, `cep`) VALUES
(1, 'Cliente Balcao', '000.000.000-00', '(21) 90000-0000', 'balcao@cliente.com', 'Nova Iguacu - RJ', NULL),
(2, 'Bruno henrique', '04235848455', '21123456677', 'brunoh@email.com', 'Rua Alice de Oliveira', NULL),
(3, 'Conta Peter', '12345679414', NULL, 'peter@email.com', '—', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente_site`
--

CREATE TABLE `cliente_site` (
  `cpf` char(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente_site`
--

INSERT INTO `cliente_site` (`cpf`, `nome`, `email`, `senha_hash`, `endereco`, `telefone`, `criado_em`) VALUES
('04235848455', 'Bruno henrique', 'brunoh@email.com', '$2y$10$I1OnevbtnyyIw9G2dbF7GOKOsk5hY39QjUVEpjV0P8Qv0Y2nx7ORu', 'Rua Alice de Oliveira, Jardim Nova Era, Nova Iguaçu/RJ, CEP 26272-020', '21123456677', '2026-09-20 12:48:16'),
('12345679414', 'Conta Peter', 'peter@email.com', '$2y$10$7rU5obebfvd96f/kZpu3u.TtBtbzgLHM5uz1lAZqM5ncMMtsCV4CS', '—', NULL, '2026-09-21 09:56:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_item`
--

CREATE TABLE `entrada_item` (
  `id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `custo_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entrada_item`
--

INSERT INTO `entrada_item` (`id`, `entrada_id`, `variacao_id`, `quantidade`, `custo_unitario`) VALUES
(1, 1, 1, 99, 0.00),
(2, 2, 1, 99, 35.00),
(3, 3, 7, 10, 20.00),
(4, 4, 8, 99, 70.90),
(5, 5, 9, 999, 0.00),
(6, 6, 9, 99, 99.50),
(7, 7, 7, 1, 1.00),
(8, 8, 12, 20, 20.00),
(9, 9, 14, 99, 50.00),
(10, 10, 13, 1, 10.00),
(11, 11, 12, 3, 10.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_mercadoria`
--

CREATE TABLE `entrada_mercadoria` (
  `id` int(11) NOT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `status` enum('rascunho','confirmada') NOT NULL DEFAULT 'rascunho',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entrada_mercadoria`
--

INSERT INTO `entrada_mercadoria` (`id`, `fornecedor_id`, `data`, `status`, `valor_total`) VALUES
(1, 1, '2026-07-19', 'confirmada', 0.00),
(2, 2, '2026-07-19', 'confirmada', 3465.00),
(3, 3, '2026-09-21', 'confirmada', 200.00),
(4, 3, '2026-09-21', 'confirmada', 7019.10),
(5, 1, '2026-09-21', 'confirmada', 0.00),
(6, 1, '2026-09-21', 'confirmada', 9850.50),
(7, 2, '2026-09-21', 'confirmada', 1.00),
(8, 4, '2026-09-21', 'confirmada', 400.00),
(9, 2, '2026-09-21', 'confirmada', 4950.00),
(10, 4, '2026-09-21', 'confirmada', 10.00),
(11, 1, '2026-09-21', 'confirmada', 30.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `minimo` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `variacao_id`, `quantidade`, `minimo`) VALUES
(1, 1, 185, 0),
(2, 7, 9, 0),
(3, 8, 98, 0),
(4, 9, 1096, 0),
(5, 12, 11, 0),
(6, 14, 79, 0),
(7, 13, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedor`
--

INSERT INTO `fornecedor` (`id`, `nome`, `cnpj`, `telefone`, `email`, `endereco`, `cep`, `ativo`) VALUES
(1, 'Cosplay Imports LTDA', '12.345.678/0001-90', '(21) 99999-1111', 'contato@cosplayimports.com', 'Rio de Janeiro - RJ', NULL, 1),
(2, 'Bruno', '123456789', '145161', 'Bruno@email.com', 'teste', NULL, 1),
(3, 'Teste', '12349898', '129834129831', 'teste@email.com', 'Rua Leocádio Melo, Jardim Nova Era - Nova Iguaçu/RJ', NULL, 1),
(4, 'Peter', '54165156154', '546165154', 'Peterforne@email.com', 'Rua Leocádio Melo, Jardim Nova Era - Nova Iguaçu/RJ', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimento_estoque`
--

CREATE TABLE `movimento_estoque` (
  `id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `origem` enum('entrada','venda','pedido') NOT NULL,
  `origem_id` int(11) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimento_estoque`
--

INSERT INTO `movimento_estoque` (`id`, `variacao_id`, `tipo`, `quantidade`, `origem`, `origem_id`, `data`) VALUES
(1, 1, 'entrada', 99, '', 1, '2026-07-19 15:51:27'),
(2, 1, 'entrada', 99, '', 2, '2026-07-19 15:51:43'),
(3, 1, 'saida', 3, 'venda', 6, '2026-07-19 15:55:50'),
(4, 1, 'saida', 3, 'venda', 1, '2026-09-20 12:48:36'),
(5, 1, 'saida', 2, 'venda', 7, '2026-09-20 13:02:09'),
(6, 7, 'entrada', 10, '', 3, '2026-09-21 08:39:04'),
(7, 7, 'saida', 2, 'venda', 8, '2026-09-21 08:39:25'),
(8, 8, 'entrada', 99, '', 4, '2026-09-21 09:35:55'),
(9, 9, 'entrada', 999, '', 5, '2026-09-21 09:57:58'),
(10, 9, 'entrada', 99, '', 6, '2026-09-21 09:58:16'),
(11, 1, 'saida', 3, 'venda', 2, '2026-09-21 09:58:36'),
(12, 9, 'saida', 2, 'venda', 2, '2026-09-21 09:58:36'),
(13, 7, 'entrada', 1, '', 7, '2026-09-21 10:27:17'),
(14, 12, 'entrada', 20, '', 8, '2026-09-21 10:38:49'),
(15, 12, 'saida', 2, 'venda', 9, '2026-09-21 10:39:45'),
(16, 1, 'saida', 1, 'venda', 3, '2026-09-21 10:41:27'),
(17, 12, 'saida', 1, 'venda', 3, '2026-09-21 10:41:27'),
(18, 14, 'entrada', 99, '', 9, '2026-09-21 10:57:39'),
(19, 14, 'saida', 5, 'venda', 10, '2026-09-21 10:58:34'),
(20, 12, 'saida', 1, 'venda', 10, '2026-09-21 10:58:34'),
(21, 14, 'saida', 5, 'venda', 4, '2026-09-21 11:02:44'),
(22, 13, 'entrada', 1, '', 10, '2026-09-21 19:26:37'),
(23, 12, 'entrada', 3, 'entrada', 11, '2026-09-21 20:02:48'),
(24, 13, 'saida', 1, 'venda', 5, '2026-09-21 20:11:08'),
(25, 12, 'saida', 3, 'venda', 11, '2026-09-21 20:16:05'),
(26, 12, 'saida', 1, 'venda', 6, '2026-09-24 14:04:12'),
(27, 8, 'saida', 1, 'venda', 7, '2026-09-24 14:05:48'),
(28, 14, 'saida', 10, 'venda', 7, '2026-09-24 14:05:48'),
(29, 1, 'saida', 1, 'venda', 8, '2026-09-24 20:08:47'),
(30, 12, 'saida', 1, 'venda', 8, '2026-09-24 20:08:47'),
(31, 12, 'saida', 1, 'venda', 9, '2026-09-24 20:22:49'),
(32, 12, 'saida', 1, 'venda', 10, '2026-09-24 20:27:03'),
(33, 12, 'saida', 1, 'venda', 11, '2026-09-24 20:31:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_entrada`
--

CREATE TABLE `nota_fiscal_entrada` (
  `id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `modelo` varchar(5) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `chave_acesso` varchar(44) NOT NULL,
  `data_emissao` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_venda`
--

CREATE TABLE `nota_fiscal_venda` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `modelo` varchar(5) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `data_emissao` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `nota_fiscal_venda`
--

INSERT INTO `nota_fiscal_venda` (`id`, `venda_id`, `modelo`, `serie`, `numero`, `data_emissao`, `valor_total`) VALUES
(1, 6, 'NFCe', '1', '1', '2026-07-19', 569.70),
(2, 7, 'NFCe', '1', '2', '2026-09-20', 379.80),
(3, 8, 'NFCe', '1', '3', '2026-09-21', 20.00),
(4, 9, 'NFCe', '1', '4', '2026-09-21', 199.80),
(5, 10, 'NFCe', '1', '5', '2026-09-21', 599.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `cliente_cpf` char(11) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  `endereco_entrega` varchar(255) NOT NULL,
  `cep_entrega` varchar(9) DEFAULT NULL,
  `valor_frete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prazo_frete_dias` int(11) DEFAULT NULL,
  `forma_pagamento` enum('pix','cartao','boleto') NOT NULL DEFAULT 'pix',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido`
--

INSERT INTO `pedido` (`id`, `cliente_cpf`, `data`, `status`, `endereco_entrega`, `cep_entrega`, `valor_frete`, `prazo_frete_dias`, `forma_pagamento`, `valor_total`) VALUES
(1, '04235848455', '2026-09-20 12:48:36', 'pendente', 'Rua Alice de Oliveira 66', NULL, 0.00, NULL, 'pix', 569.70),
(2, '12345679414', '2026-09-21 09:58:36', 'pendente', 'Rua teste, isso e aquilo', NULL, 0.00, NULL, 'pix', 768.70),
(3, '04235848455', '2026-09-21 10:41:27', 'pendente', 'Rua Alice de Oliveira', NULL, 0.00, NULL, 'pix', 289.80),
(4, '04235848455', '2026-09-21 11:02:44', 'pendente', 'Rua Alice de Oliveira 66', NULL, 0.00, NULL, 'pix', 500.00),
(5, '04235848455', '2026-09-21 20:11:08', 'pendente', 'teste 10564841', NULL, 0.00, NULL, 'pix', 99.90),
(6, '04235848455', '2026-09-24 14:04:12', 'pendente', 'Rua Leocádio Melo, 248 - Jardim Nova Era, Nova Iguaçu/RJ - CEP 26272-060', NULL, 0.00, NULL, 'pix', 99.90),
(7, '04235848455', '2026-09-24 14:05:48', 'pendente', 'Rua Leocádio Melo, 248 - Jardim Nova Era, Nova Iguaçu/RJ - CEP 26272-060', NULL, 0.00, NULL, 'pix', 1099.50),
(8, '04235848455', '2026-09-24 20:08:47', 'pendente', 'Rua Leocádio Melo, 248 - Jardim Nova Era, Nova Iguaçu/RJ - CEP 26272-060', NULL, 0.00, NULL, 'pix', 289.80),
(9, '04235848455', '2026-09-24 20:22:49', 'pendente', 'Rua Alice de Oliveira, Jardim Nova Era, Nova Iguaçu/RJ, CEP 26272-020', NULL, 0.00, NULL, 'pix', 99.90),
(10, '04235848455', '2026-09-24 20:27:03', 'pendente', 'Rua Alice de Oliveira, Jardim Nova Era, Nova Iguaçu/RJ, CEP 26272-020', NULL, 0.00, NULL, 'pix', 99.90),
(11, '04235848455', '2026-09-24 20:31:03', 'pendente', 'Rua Alice de Oliveira, Jardim Nova Era, Nova Iguaçu/RJ, CEP 26272-020', NULL, 0.00, NULL, 'pix', 99.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_item`
--

CREATE TABLE `pedido_item` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_item`
--

INSERT INTO `pedido_item` (`id`, `pedido_id`, `variacao_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 3, 189.90),
(2, 2, 1, 3, 189.90),
(3, 2, 9, 2, 99.50),
(4, 3, 1, 1, 189.90),
(5, 3, 12, 1, 99.90),
(6, 4, 14, 5, 100.00),
(7, 5, 13, 1, 99.90),
(8, 6, 12, 1, 99.90),
(9, 7, 8, 1, 99.50),
(10, 7, 14, 10, 100.00),
(11, 8, 1, 1, 189.90),
(12, 8, 12, 1, 99.90),
(13, 9, 12, 1, 99.90),
(14, 10, 12, 1, 99.90),
(15, 11, 12, 1, 99.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `categoria_id`, `nome`, `descricao`, `ativo`) VALUES
(1, 1, 'Fantasia Ninja Sombria', 'Fantasia completa estilo anime, tecido leve.', 1),
(2, 6, 'Peruca Azul Longa', 'Peruca resistente ao calor, ideal para eventos.', 1),
(3, 3, 'Manto Vermelho Akai', 'Manto com simbolos bordados, tamanho unico.', 1),
(4, 12, 'Kit Acessorios Steampunk', 'Oculos + luvas + cinto tematico.', 0),
(5, 12, 'Boneco careca', 'Bla bla bla', 0),
(6, 12, 'Teste', '122344', 1),
(7, 5, 'coco', 'wwwww', 1),
(8, 16, 'Boneca', 'afdjnajnfaj', 1),
(9, 18, 'Boneco Homem aranha', 'nahbhaf', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','vendedor') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`) VALUES
(1, 'Admin Cosplay', 'admin@cosplay.com', '$2y$10$COLE_AQUI_O_HASH_DO_123456', 'admin', 1),
(2, 'Vendedor Cosplay', 'vendedor@cosplay.com', '$2y$10$COLE_AQUI_O_HASH_DO_123456', 'vendedor', 1),
(3, 'Bruno', 'bruno@email.com', '$2y$10$5APfpiR5id4AuY0kn02XHuMPDUM2aKeC9FRdzRY1UuuZeKOeSIhS.', 'admin', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `variacao`
--

CREATE TABLE `variacao` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `tamanho` varchar(10) NOT NULL,
  `cor` varchar(30) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `variacao`
--

INSERT INTO `variacao` (`id`, `produto_id`, `tamanho`, `cor`, `sku`, `preco`) VALUES
(1, 1, 'P', 'Preto', 'COS-NINJA-P-PRETO', 189.90),
(2, 1, 'M', 'Preto', 'COS-NINJA-M-PRETO', 189.90),
(3, 1, 'G', 'Preto', 'COS-NINJA-G-PRETO', 189.90),
(4, 2, 'U', 'Azul', 'COS-PERUCA-U-AZUL', 99.90),
(5, 3, 'U', 'Vermelho', 'COS-MANTO-U-VERM', 149.90),
(6, 4, 'U', 'Marrom', 'COS-STEAM-U-MARR', 79.90),
(7, 6, 'G', 'Preto', 'TESTE-01', 10.00),
(8, 7, 'G', 'Preto', 'BIGBOSSGPRE', 99.50),
(9, 7, 'P', 'Vermelho', 'BIGBOSSP', 99.50),
(11, 7, 'G', 'Vermelho', 'BIGBOSSGV', 99.50),
(12, 8, 'P', 'Branca', 'ALICEPEQUENA', 99.90),
(13, 8, 'M', 'Branca', 'ALICEMEDIA', 99.90),
(14, 9, 'P', 'Vermelho', 'ARANHA-P', 100.00),
(15, 9, 'M', 'Vermelho', 'ARANHA-M', 100.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda`
--

CREATE TABLE `venda` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `status` enum('aberta','finalizada') NOT NULL DEFAULT 'aberta',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda`
--

INSERT INTO `venda` (`id`, `usuario_id`, `cliente_id`, `data`, `status`, `valor_total`) VALUES
(6, 3, 1, '2026-07-19', 'finalizada', 569.70),
(7, 3, 1, '2026-09-20', 'finalizada', 379.80),
(8, 3, 1, '2026-09-21', 'finalizada', 20.00),
(9, 3, 1, '2026-09-21', 'finalizada', 199.80),
(10, 3, 1, '2026-09-21', 'finalizada', 599.90),
(11, 3, 2, '2026-09-21', 'finalizada', 299.70);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda_item`
--

CREATE TABLE `venda_item` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda_item`
--

INSERT INTO `venda_item` (`id`, `venda_id`, `variacao_id`, `quantidade`, `preco_unitario`) VALUES
(1, 6, 1, 3, 189.90),
(2, 7, 1, 2, 189.90),
(3, 8, 7, 2, 10.00),
(4, 9, 12, 2, 99.90),
(5, 10, 14, 5, 100.00),
(6, 10, 12, 1, 99.90),
(7, 11, 12, 3, 99.90);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente_site`
--
ALTER TABLE `cliente_site`
  ADD PRIMARY KEY (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `entrada_item`
--
ALTER TABLE `entrada_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrada_item_entrada` (`entrada_id`),
  ADD KEY `idx_entrada_item_variacao` (`variacao_id`);

--
-- Índices de tabela `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrada_fornecedor` (`fornecedor_id`),
  ADD KEY `idx_entrada_data` (`data`),
  ADD KEY `idx_entrada_status` (`status`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variacao_id` (`variacao_id`);

--
-- Índices de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mov_variacao` (`variacao_id`),
  ADD KEY `idx_mov_data` (`data`),
  ADD KEY `idx_mov_origem` (`origem`,`origem_id`),
  ADD KEY `idx_mov_tipo` (`tipo`);

--
-- Índices de tabela `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entrada_id` (`entrada_id`),
  ADD UNIQUE KEY `chave_acesso` (`chave_acesso`),
  ADD KEY `idx_nf_entrada_data` (`data_emissao`);

--
-- Índices de tabela `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `venda_id` (`venda_id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `idx_nf_venda_data` (`data_emissao`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_cpf` (`cliente_cpf`);

--
-- Índices de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `variacao_id` (`variacao_id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_categoria` (`categoria_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `variacao`
--
ALTER TABLE `variacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_variacao_produto` (`produto_id`);

--
-- Índices de tabela `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_usuario` (`usuario_id`),
  ADD KEY `idx_venda_cliente` (`cliente_id`),
  ADD KEY `idx_venda_data` (`data`),
  ADD KEY `idx_venda_status` (`status`);

--
-- Índices de tabela `venda_item`
--
ALTER TABLE `venda_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_item_venda` (`venda_id`),
  ADD KEY `idx_venda_item_variacao` (`variacao_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `entrada_item`
--
ALTER TABLE `entrada_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `variacao`
--
ALTER TABLE `variacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `venda`
--
ALTER TABLE `venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `venda_item`
--
ALTER TABLE `venda_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `entrada_item`
--
ALTER TABLE `entrada_item`
  ADD CONSTRAINT `fk_entrada_item_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entrada_mercadoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrada_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  ADD CONSTRAINT `fk_entrada_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  ADD CONSTRAINT `fk_movimento_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  ADD CONSTRAINT `fk_nf_entrada_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entrada_mercadoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  ADD CONSTRAINT `fk_nf_venda_venda` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`cliente_cpf`) REFERENCES `cliente_site` (`cpf`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD CONSTRAINT `pedido_item_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pedido_item_ibfk_2` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `variacao`
--
ALTER TABLE `variacao`
  ADD CONSTRAINT `fk_variacao_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `fk_venda_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venda_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `venda_item`
--
ALTER TABLE `venda_item`
  ADD CONSTRAINT `fk_venda_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venda_item_venda` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
