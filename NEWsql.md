#### Tabela nova a criar no banco (obrigatório)
Criar uma tabela nova (não reaproveitar `cliente`), por exemplo `cliente_site`, com o **CPF como chave primária**:
```sql
CREATE TABLE cliente_site (
  cpf CHAR(11) NOT NULL PRIMARY KEY,   -- armazenar somente dígitos, sem pontuação
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  endereco VARCHAR(255) NOT NULL,
  telefone VARCHAR(20) NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```
Gere o `ALTER`/`CREATE TABLE` em um arquivo `.sql` separado (ex: `cliente_site.sql`) para ser rodado junto ao banco já existente — não altere o dump original `loja_roupas.sql`.

### 5. Checkout / Pedido (`controller=pedido&action=...`)
- Ao finalizar a compra, o cliente logado confirma endereço (pré-preenchido do cadastro, editável) e finaliza.
- **Decisão de arquitetura (obrigatória): NÃO reaproveitar `venda`/`venda_item`.** Criar tabelas novas e próprias do e-commerce:
  ```sql
  CREATE TABLE pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_cpf CHAR(11) NOT NULL,
    data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
    endereco_entrega VARCHAR(255) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (cliente_cpf) REFERENCES cliente_site(cpf) ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  CREATE TABLE pedido_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    variacao_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES variacao(id) ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 

 ALTER TABLE movimento_estoque MODIFY origem ENUM('entrada','venda','pedido') NOT NULL;
 
 
 
 
 
 Explicação
  ``` Motivo: `venda`/`venda_item` são do módulo de PDV/balcão do vendedor (exigem `usuario_id`, `cliente_id` aponta para a tabela `cliente` antiga com PK inteira, e o ENUM de status é pensado para venda presencial). O e-commerce tem regras próprias (endereço de entrega registrado no momento da compra, status de pedido diferentes, cliente identificado por CPF). Manter tabelas separadas evita alterar um módulo já em uso pelo vendedor e deixa o domínio de cada canal de venda mais claro. Adicione o `CREATE TABLE` de `pedido` e `pedido_item` no mesmo arquivo `cliente_site.sql` (ou em um novo `pedido.sql`).
- Ao confirmar o pedido: dar baixa no estoque (`estoque.quantidade`) das variações compradas e gerar registro em `movimento_estoque` (tipo `saida`, origem `venda`, `origem_id` = id do pedido) — esta parte é compartilhada com o restante do sistema, pois o estoque é único para toda a loja. Depois, limpar o carrinho da sessão.
- Página de confirmação simples ("Pedido realizado com sucesso") e uma página "Meus pedidos" listando o histórico do cliente logado (status, itens, total, data).




ALTER TABLE pedido
  ADD COLUMN cep_entrega VARCHAR(9) NULL AFTER endereco_entrega,
  ADD COLUMN valor_frete DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER cep_entrega,
  ADD COLUMN prazo_frete_dias INT NULL AFTER valor_frete,
  ADD COLUMN forma_pagamento ENUM('pix','cartao','boleto') NOT NULL DEFAULT 'pix' AFTER prazo_frete_dias;