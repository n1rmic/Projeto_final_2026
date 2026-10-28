-- =====================================================================
-- Loja de Cosplay — lado CLIENTE (e-commerce)
-- Rodar DEPOIS de importar o loja_roupas.sql (não altera nenhuma tabela existente).
-- =====================================================================
USE loja_roupas;

-- Cadastro/login do cliente do site (CPF = chave primária, somente dígitos)
CREATE TABLE IF NOT EXISTS cliente_site (
  cpf        CHAR(11)     NOT NULL PRIMARY KEY,
  nome       VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  endereco   VARCHAR(255) NOT NULL,
  telefone   VARCHAR(20)  NULL,
  criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pedido do e-commerce (separado de venda/venda_item, que são do PDV do vendedor)
CREATE TABLE IF NOT EXISTS pedido (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  cliente_cpf      CHAR(11)      NOT NULL,
  data             DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status           ENUM('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  endereco_entrega VARCHAR(255)  NOT NULL,
  valor_total      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (cliente_cpf) REFERENCES cliente_site(cpf) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS pedido_item (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id      INT           NOT NULL,
  variacao_id    INT           NOT NULL,
  quantidade     INT           NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id)   REFERENCES pedido(id)   ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (variacao_id) REFERENCES variacao(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
