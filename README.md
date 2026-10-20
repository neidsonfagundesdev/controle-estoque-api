# API RESTful - Controle de Estoque

API RESTful desenvolvida em PHP para gerenciamento de produtos em estoque.

O projeto permite cadastrar, listar, buscar, atualizar e excluir produtos, utilizando PHP, MySQL, PDO e arquitetura separada em Controller, Service e Repository.

A aplicação está publicada em produção no Railway, utilizando uma instância MySQL também hospedada na plataforma.

## API publicada

A API está disponível em produção em:

https://controle-estoque-api-production.up.railway.app

Exemplo de endpoint:

```http
GET https://controle-estoque-api-production.up.railway.app/produtos
```

> A URL raiz da aplicação não possui uma rota definida. Utilize os endpoints documentados abaixo.

## Tecnologias utilizadas

- PHP
- MySQL
- PDO
- API REST
- JSON
- Git
- Composer
- Railway
- Thunder Client

## Funcionalidades

A API permite:

- Listar todos os produtos
- Buscar um produto pelo ID
- Cadastrar novos produtos
- Atualizar parcialmente um produto
- Excluir produtos
- Validar os dados enviados pelo cliente
- Retornar códigos HTTP apropriados
- Tratar JSON inválido
- Tratar rotas inexistentes
- Tratar métodos HTTP não permitidos
- Tratar erros internos sem expor detalhes do banco de dados
- Registrar erros internos no log do servidor

## Estrutura do projeto

```text
controle-estoque-api/
│
├── config/
│   ├── database.php
│   └── database.example.php
│
├── public/
│   └── index.php
│
├── src/
│   ├── Controller/
│   │   └── ProdutoController.php
│   │
│   ├── Repository/
│   │   └── ProdutoRepository.php
│   │
│   └── Service/
│       └── ProdutoService.php
│
├── .gitignore
├── composer.json
├── schema.sql
└── README.md
```

## Arquitetura

O projeto foi organizado em camadas.

### Controller

Responsável por receber as requisições HTTP, interpretar os dados enviados em JSON e retornar as respostas da API.

### Service

Responsável pelas regras de negócio e validações dos produtos.

### Repository

Responsável pelo acesso ao banco de dados e execução das consultas SQL através do PDO.

O fluxo principal da aplicação é:

```text
Cliente
   ↓
index.php
   ↓
Controller
   ↓
Service
   ↓
Repository
   ↓
MySQL
```

## Banco de dados

O projeto utiliza MySQL.

A estrutura utilizada localmente está disponível no arquivo:

```text
schema.sql
```

A tabela `produtos` possui os seguintes campos:

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT AUTO_INCREMENT | Identificador do produto |
| nome | VARCHAR(150) | Nome do produto |
| preco | DECIMAL(10,2) | Preço do produto |
| quantidade | INT | Quantidade disponível |

Estrutura da tabela:

```sql
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL
);
```

## Pré-requisitos

Para executar o projeto localmente, é necessário ter:

- PHP 8.2 ou superior
- Extensão PHP `mbstring`
- Extensão PHP `PDO`
- Extensão PHP `pdo_mysql`
- MySQL ou MariaDB
- Git

As dependências necessárias ao ambiente PHP também estão declaradas no arquivo `composer.json`.

## Habilitando a extensão mbstring

A API utiliza `mb_strlen()` para contar corretamente caracteres UTF-8, incluindo letras acentuadas.

No arquivo `php.ini`, verifique se a extensão `mbstring` está habilitada:

```ini
extension=mbstring
```

Caso a linha esteja assim:

```ini
;extension=mbstring
```

remova o `;`, salve o arquivo e reinicie o PHP.

Para confirmar se a extensão está carregada:

```bash
php -m
```

## Configuração local

Clone o repositório:

```bash
git clone https://github.com/neidsonfagundesdev/controle-estoque-api
```

Entre na pasta do projeto:

```bash
cd controle-estoque-api
```

Crie o banco de dados local utilizando o arquivo:

```text
schema.sql
```

Por padrão, a configuração local utiliza:

```text
Host: localhost
Porta: 3306
Banco: controle_estoque
Usuário: root
Senha: vazia
```

O arquivo `config/database.php` também permite utilizar variáveis de ambiente.

As variáveis utilizadas são:

```text
MYSQLHOST
MYSQLPORT
MYSQLDATABASE
MYSQLUSER
MYSQLPASSWORD
```

Quando essas variáveis não existem no ambiente local, a aplicação utiliza os valores locais padrão.

## Variáveis de ambiente

Em produção, as credenciais do banco não ficam escritas diretamente no código.

A aplicação utiliza:

```php
getenv("MYSQLHOST");
getenv("MYSQLPORT");
getenv("MYSQLDATABASE");
getenv("MYSQLUSER");
getenv("MYSQLPASSWORD");
```

Isso permite utilizar credenciais fornecidas pelo ambiente de execução sem armazenar senhas no repositório.

## Executando a aplicação localmente

Na raiz do projeto, execute:

```bash
php -S localhost:8000 -t public public/index.php
```

A API ficará disponível em:

```text
http://localhost:8000
```

Exemplo:

```http
GET http://localhost:8000/produtos
```

## Endpoints

### Listar produtos

```http
GET /produtos
```

Exemplo de resposta:

```json
[
    {
        "id": 1,
        "nome": "Teclado",
        "preco": "150.00",
        "quantidade": 10
    }
]
```

### Buscar produto por ID

```http
GET /produtos/1
```

Exemplo:

```json
{
    "id": 1,
    "nome": "Teclado",
    "preco": "150.00",
    "quantidade": 10
}
```

Caso o produto não exista:

```json
{
    "erro": "Produto não encontrado."
}
```

### Cadastrar produto

```http
POST /produtos
```

Corpo da requisição:

```json
{
    "nome": "Monitor",
    "preco": 899.90,
    "quantidade": 5
}
```

Exemplo de resposta:

```json
{
    "id": 2,
    "nome": "Monitor",
    "preco": "899.90",
    "quantidade": 5
}
```

Status retornado:

```text
201 Created
```

### Atualizar produto

```http
PATCH /produtos/1
```

É possível enviar somente os campos que devem ser alterados.

Exemplo:

```json
{
    "preco": 799.90
}
```

Os demais dados do produto permanecem inalterados.

Exemplo de resposta:

```json
{
    "id": 1,
    "nome": "Teclado",
    "preco": "799.90",
    "quantidade": 10
}
```

### Excluir produto

```http
DELETE /produtos/1
```

Quando a exclusão é realizada com sucesso:

```text
204 No Content
```

Caso o produto não exista:

```text
404 Not Found
```

## Códigos HTTP utilizados

| Código | Significado |
|---|---|
| 200 | Requisição realizada com sucesso |
| 201 | Produto criado com sucesso |
| 204 | Produto excluído com sucesso |
| 400 | Dados enviados são inválidos |
| 404 | Produto ou rota não encontrada |
| 405 | Método HTTP não permitido para a rota |
| 500 | Erro interno do servidor |

## Validações

A API possui validações para impedir que dados inválidos sejam enviados ao banco.

Entre elas:

- Nome não pode ser vazio
- Nome deve ser texto
- Nome deve possuir no máximo 150 caracteres
- Caracteres UTF-8 são contabilizados corretamente
- Preço deve ser numérico
- Preço deve ser no mínimo `0.01`
- Preço deve possuir no máximo duas casas decimais
- Preço deve respeitar o limite do campo `DECIMAL(10,2)`
- Valores infinitos de preço são rejeitados
- Quantidade deve ser um número inteiro
- Quantidade não pode ser negativa
- Quantidade deve respeitar o limite do campo `INT`
- JSON inválido retorna erro `400`
- Corpo de POST e PATCH deve ser um objeto JSON
- PATCH deve possuir pelo menos um campo válido para atualização
- Produto inexistente retorna erro `404`
- Rotas inexistentes retornam erro `404`
- Métodos HTTP não permitidos retornam erro `405`
- Erros internos retornam erro `500`

## Atualização parcial com PATCH

O endpoint `PATCH` atualiza somente os campos enviados pelo cliente.

Por exemplo:

```json
{
    "quantidade": 20
}
```

Nesse caso, somente a quantidade é alterada.

Os campos `nome` e `preco` permanecem com seus valores atuais.

## Segurança

As consultas que recebem dados do usuário utilizam prepared statements com PDO.

Exemplo:

```php
$stmt = $this->pdo->prepare($sql);

$stmt->execute([
    "id" => $id
]);
```

Isso evita concatenar diretamente os dados recebidos nas consultas SQL e ajuda na prevenção de SQL Injection.

No `PATCH`, as colunas permitidas são definidas pela própria aplicação e os valores são enviados separadamente através dos parâmetros da consulta.

As credenciais do banco de produção são fornecidas através de variáveis de ambiente e não são armazenadas diretamente no código.

Erros internos retornam mensagens genéricas ao cliente, evitando expor:

- SQL
- credenciais
- caminhos internos
- detalhes da conexão com o banco

Os detalhes técnicos das exceções do PDO são registrados internamente utilizando:

```php
error_log($erro->getMessage());
```

## Tratamento de erros

Exemplo de dado inválido:

```json
{
    "erro": "Preço deve ter no máximo 2 casas decimais."
}
```

Exemplo de produto inexistente:

```json
{
    "erro": "Produto não encontrado."
}
```

Exemplo de erro interno:

```json
{
    "erro": "Erro interno do servidor."
}
```

## Deploy

A aplicação está hospedada no Railway.

A infraestrutura de produção utiliza:

```text
Internet
   ↓
Railway
   ↓
API PHP
   ↓
PDO
   ↓
MySQL
```

O serviço PHP utiliza o diretório:

```text
public/
```

como raiz pública da aplicação.

No Railway, foi configurada a variável:

```text
RAILPACK_PHP_ROOT_DIR=/app/public
```

O banco MySQL é executado em um serviço separado dentro do mesmo projeto e a aplicação acessa suas credenciais por variáveis de ambiente.

## Testes realizados

Os principais fluxos foram testados tanto no ambiente local quanto em produção.

Foram validados:

```text
GET    /produtos
POST   /produtos
GET    /produtos/{id}
PATCH  /produtos/{id}
DELETE /produtos/{id}
```

Também foram testados:

- JSON malformado
- Dados com tipos inválidos
- Limites de preço
- Limites de quantidade
- Nomes UTF-8
- Produtos inexistentes
- Rotas inexistentes
- Métodos HTTP não permitidos
- Falhas de conexão com o banco
- Tentativas de SQL Injection
- Atualizações parciais
- Exclusão repetida do mesmo produto

## Objetivo do projeto

Projeto desenvolvido para praticar conceitos de desenvolvimento back-end, incluindo:

- APIs RESTful
- Métodos HTTP
- CRUD
- JSON
- Validação de dados
- Códigos HTTP
- PDO
- MySQL
- Prepared Statements
- SQL Injection
- Tratamento de exceções
- Logs de erros
- Validação de JSON
- Codificação UTF-8
- Variáveis de ambiente
- Separação de responsabilidades
- Arquitetura Controller / Service / Repository
- Git
- Versionamento de código
- Versionamento semântico
- Deploy
- Banco de dados em nuvem

## Versões

### v1.0.0

Primeira versão estável da API, contendo o CRUD completo, validações, tratamento de erros e arquitetura em camadas.

### v1.0.1

Versão preparada para execução em produção, com configuração para variáveis de ambiente e deploy no Railway.

## Autor

Desenvolvido por Neidson Fagundes como projeto de estudo e portfólio em desenvolvimento back-end.