erDiagram
  USUARIO  PEDIDO : "1:N realiza"
  USUARIO  ENDERECO : "1:N possui"
  ENDERECO  PEDIDO : "N:1 usado_em (opcional)"
  CATEGORIA  ITEM : "1:N organiza"
  PEDIDO  ITEM_PEDIDO : "1:N inclui"
  ITEM  ITEM_PEDIDO : "1:N aparece_em"
  PEDIDO  PAGAMENTO : "1:N registra"

-- TABELA: USUÁRIO
CREATE TABLE usuario (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  email CITEXT UNIQUE,
  telefone TEXT,
  criado_em TIMESTAMPTZ DEFAULT now()
);

-- TABELA: ENDEREÇO
CREATE TABLE endereco (
  id SERIAL PRIMARY KEY,
  usuario_id INT REFERENCES usuario(id) ON DELETE CASCADE,
  rua TEXT NOT NULL,
  numero TEXT,
  bairro TEXT,
  cidade TEXT NOT NULL,
  estado TEXT NOT NULL,
  cep TEXT
);

-- TABELA: CATEGORIA (CARDÁPIO)

CREATE TABLE categoria (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  ordem INT DEFAULT 0,
  ativo BOOLEAN DEFAULT true
);

-- TABELA: ITEM (PRODUTO)
CREATE TABLE item (
  id SERIAL PRIMARY KEY,
  categoria_id INT REFERENCES categoria(id) ON DELETE SET NULL,
  nome TEXT NOT NULL,
  descricao TEXT,
  preco_centavos INT NOT NULL,    -- exemplo: R$ 12,50 = 1250
  ativo BOOLEAN DEFAULT true
);

-- TABELA: PEDIDO

CREATE TABLE pedido (
  id SERIAL PRIMARY KEY,
  usuario_id INT REFERENCES usuario(id) ON DELETE SET NULL,
  endereco_id INT REFERENCES endereco(id) ON DELETE SET NULL,
  tipo TEXT CHECK (tipo IN ('entrega','retirada','local')),
  status TEXT CHECK (status IN ('criado','confirmado','pronto','concluido','cancelado')) DEFAULT 'criado',
  subtotal_centavos INT DEFAULT 0,
  taxa_entrega_centavos INT DEFAULT 0,
  total_centavos INT DEFAULT 0,
  criado_em TIMESTAMPTZ DEFAULT now()
);

-- TABELA: ITEM_PEDIDO

CREATE TABLE item_pedido (
  id SERIAL PRIMARY KEY,
  pedido_id INT REFERENCES pedido(id) ON DELETE CASCADE,
  item_id INT REFERENCES item(id),
  quantidade INT NOT NULL CHECK (quantidade > 0),
  preco_unit_centavos INT NOT NULL  
);

-- TABELA: PAGAMENTO

CREATE TABLE pagamento (
  id SERIAL PRIMARY KEY,
  pedido_id INT REFERENCES pedido(id) ON DELETE CASCADE,
  metodo TEXT CHECK (metodo IN ('pix','cartao','dinheiro')),
  status TEXT CHECK (status IN ('pendente','pago','recusado')) DEFAULT 'pendente',
  valor_centavos INT NOT NULL,
  pago_em TIMESTAMPTZ
);
