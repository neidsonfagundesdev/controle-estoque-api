````markdown
# API RESTful - Controle de Estoque

API RESTful desenvolvida em PHP para gerenciamento de produtos em estoque.

O projeto permite cadastrar, listar, buscar, atualizar e excluir produtos, utilizando PHP, MySQL, PDO e arquitetura separada em Controller, Service e Repository.

## Tecnologias utilizadas

- PHP
- MySQL
- PDO
- API REST
- JSON
- Git
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
- Tratar rotas inexistentes
- Tratar métodos HTTP não permitidos
- Tratar erros internos sem expor detalhes do banco de dados

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

A estrutura necessária está disponível no arquivo:

```text
schema.sql
```

O banco possui a tabela `produtos` com os seguintes campos:

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT | Identificador do produto |
| nome | VARCHAR(150) | Nome do produto |
| preco | DECIMAL(10,2) | Preço do produto |
| quantidade | INT | Quantidade disponível |

## Pré-requisitos

Para executar o projeto localmente, é necessário ter:

- PHP com suporte ao PDO MySQL
- MySQL ou MariaDB
- Git, caso queira clonar o repositório

## Configuração

Clone o repositório:

```bash
git clone https://github.com/neidsonfagundesdev/controle-estoque-api
```

Entre na pasta do projeto:

```bash
cd controle-estoque-api
```

Crie o banco de dados executando o arquivo:

```text
schema.sql
```

Depois copie:

```text
config/database.example.php
```

para:

```text
config/database.php
```

Informe os dados da sua conexão com o MySQL:

```php
$host = "localhost";
$banco = "controle_estoque";
$usuario = "SEU_USUARIO";
$senha = "SUA_SENHA";
```

O arquivo `database.php` não é enviado ao GitHub porque está incluído no `.gitignore`.

## Executando a aplicação

Na raiz do projeto, execute:

```bash
php -S localhost:8000 -t public public/index.php
```

A API ficará disponível em:

```text
http://localhost:8000
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

---

### Buscar produto por ID

```http
GET /produtos/1
```

Exemplo de resposta:

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

---

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

---

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

Os outros dados do produto permanecem inalterados.

Exemplo de resposta:

```json
{
    "id": 1,
    "nome": "Teclado",
    "preco": "799.90",
    "quantidade": 10
}
```

---

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
- Preço deve ser numérico
- Preço deve ser maior que zero
- Preço deve possuir no máximo duas casas decimais
- Preço deve respeitar o limite do campo `DECIMAL(10,2)`
- Quantidade deve ser um número inteiro
- Quantidade não pode ser negativa
- Quantidade deve respeitar o limite do campo `INT`
- JSON inválido retorna erro `400`
- Corpo da requisição deve ser um objeto JSON
- Produto inexistente retorna erro `404`
- Rotas inexistentes retornam erro `404`
- Métodos HTTP não permitidos retornam erro `405`
- Erros internos retornam erro `500`

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

As credenciais locais do banco também não são versionadas no Git.

Erros internos da aplicação retornam mensagens genéricas ao cliente, evitando expor detalhes da conexão ou do banco de dados.

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
- Separação de responsabilidades
- Arquitetura Controller / Service / Repository
- Git e versionamento de código

## Autor

Desenvolvido por Neidson Fagundes como projeto de estudo e portfólio em desenvolvimento back-end.
````