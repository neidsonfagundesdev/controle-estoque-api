<?php

/*Esse arquivo é como o atendente da API. 
- recebe o pedido HTTP
- pega os dados
- chama o Service
- prepara a resposta
*/

require_once __DIR__ . "/../Service/ProdutoService.php";

class ProdutoController
{
    private ProdutoService $service;

    public function __construct(ProdutoService $service)
    {
        $this->service = $service;
    }

    public function criar(): void // void significa: essa função não devolve um valor com return; ela envia a resposta HTTP diretamente.
    {
    // -->file_get_contents<--função do PHP que “pega o conteúdo” de alguma coisa e te devolve como texto
    $corpo = file_get_contents("php://input");

    //Tenta decodificar o JSON.
    //JSON_THROW_ON_ERROR faz o json_decode() lançar uma exceção se o JSON estiver inválido.
    //Se isso acontecer, o catch captura o erro e retorna status 400.
    try {
        $dadosObjeto = json_decode(
            $corpo,
            false,
            512,
            JSON_THROW_ON_ERROR
        );

    } catch (JsonException $erro) {
        http_response_code(400);
        echo json_encode(["erro" => "JSON inválido."]);
        exit;
}

    //Depois verifica se relmente é um objeto JSON
    if (!is_object($dadosObjeto)) {
        http_response_code(400);
        echo json_encode(["erro" => "O corpo deve ser um objeto JSON"]);
        exit;
    }

    //Cast. Transforma o obejto em array PHP
    $dados = (array) $dadosObjeto;
    
    //retorna o valor se não houver valor retorna null
    $nome = $dados["nome"] ?? null;
    $preco = $dados["preco"] ?? null;
    $quantidade = $dados["quantidade"] ?? null;
    
    //Se for null vai para validação
    //NOME
    //Se estiver vazio, retorna o erro
    if ($nome === null) {
        http_response_code(400);
        echo json_encode(["erro" => "Nome é obrigatório."]);
        exit;
    }

    //Verifica se é string
    if (!is_string($nome)) {
        http_response_code(400);
        echo json_encode(["erro" => "Nome deve ser um texto."]);
        exit;
    }

    //só após a validação executa o trim
    $nome = trim($nome);

    //PREÇO
    //Validações com preço, não pode ser texto
    if ($preco === null) {
        http_response_code(400);
        echo json_encode(["erro" => "Preço é obrigatório."]);
        exit;
    }

    //verifica se é inteiro e se é float
    if (!is_int($preco) && !is_float($preco)) {
        http_response_code(400);
        echo json_encode(["erro" => "Preço deve ser um número."]);
        exit;
    }

    //QUANTIDADE
    //Validações com quantidade, não pode ser float
    if ($quantidade === null) {
        http_response_code(400);
        echo json_encode(["erro" => "Quantidade é obrigatória."]);
        exit;
    }

    //verifica se é inteiro
    if (!is_int($quantidade)) {
        http_response_code(400);
        echo json_encode(["erro" => "Quantidade deve ser um número inteiro."]);
        exit;
    }

    try {
        //Valida os dados pelo produto service antes de criar no banco
        $novoId = $this->service->criar(
            $nome,
            $preco,
            $quantidade
    );

    //Caso haja erro no banco de dados ou PDO, retorn status 500. 
    //Primeiro verificamos o erro específico depois o erro genérico. 
    } catch (PDOException $erro) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro interno do servidor."]);
        exit;

    //Retorna erro pelas validações inválidas
    } catch (Exception $erro) {
        http_response_code(400);
        echo json_encode(["erro"=> $erro->getMessage()]);
        exit;
    }

    //Cria o novo produto
    $novoProduto = [
       
        "id" => $novoId ,
        "nome" => $nome,
        "preco" => $preco,
        "quantidade" => $quantidade

    ];

    //retorna um status http 201 (algo foi criado)
    http_response_code(201);

    echo json_encode($novoProduto);

    }

    public function listarTodos(): void
    {
        try {
            $produtos = $this->service->listarTodos();
            http_response_code(200);
            echo json_encode($produtos);

        //Caso o PDO falhe, retorna erro
        } catch (PDOException $erro) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno do servidor."]);
            exit;
        }
        
    }


    //Busca pelo id
    public function buscarPorId(int $id): void
    {
        try {
            $produto = $this->service->buscarPorId($id);

            if ($produto === null) {
                http_response_code(404);
                echo json_encode(["erro" => "Produto não encontrado."]);
                exit;
            }

            http_response_code(200);
            echo json_encode($produto);
        
        //Caso haja falha no PDO, retorna erro
        } catch (PDOException $erro) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno do servidor."]);
            exit;
        }
        
    }

    //Lê o corpo da requisição, decodifica json e passa pela validação
    public function atualizar(int $id): void
    {
        //Lê o corpo da requisição
        $corpo = file_get_contents("php://input");

        //Tenta decodificar o JSON.
        //JSON_THROW_ON_ERROR faz o json_decode() lançar uma exceção se o JSON estiver inválido.
        //Se isso acontecer, o catch captura o erro e retorna status 400.
        try {
            $dadosObjeto = json_decode(
                $corpo,
                false,
                512,
                JSON_THROW_ON_ERROR
            );
        
        } catch (JsonException $erro) {
            http_response_code(400);
            echo json_encode(["erro" => "JSON inválido."]);
            exit;
        }

        //Depois verifica se realmente é um objeto JSON
        if (!is_object($dadosObjeto)) {
            http_response_code(400);
            echo json_encode(["erro" => "O corpo deve ser um objeto JSON."]);
            exit;
        }

        //Cast. Transforma objeto para array PHP
        $dados = (array) $dadosObjeto;

        //verifica se veio pelo menos um dos campos permitidos
        //array_key_exists() --> verifica se uma chave existe em um array, retornando true ou false
        if (
            !array_key_exists("nome", $dados) &&
            !array_key_exists("preco", $dados) &&
            !array_key_exists("quantidade", $dados)
            ) {
                http_response_code(400);
                echo json_encode(["erro" => "Nenhum campo válido foi informado para atualização."]);
                exit;
            }

        //Ternário. Se "nome" existir em $dados, $nome recebe $dados["nome"]. Senão, $nome recebe null. 
        $nome = array_key_exists("nome", $dados) ? $dados["nome"] : null;
        $preco = array_key_exists("preco", $dados) ? $dados["preco"] : null;
        $quantidade = array_key_exists("quantidade", $dados) ? $dados["quantidade"] : null;

        //Valida os campos nulos
        if (array_key_exists("nome", $dados) && $nome === null) {
            http_response_code(400);
            echo json_encode(["erro" => "Nome não pode ser nulo."]);
            exit;
        }
        
        if (array_key_exists("preco", $dados) && $preco === null) {
            http_response_code(400);
            echo json_encode(["erro" => "Preço não pode ser nulo."]);
            exit;
        }

        if (array_key_exists("quantidade", $dados) && $quantidade === null) {
            http_response_code(400);
            echo json_encode(["erro" => "Quantidade não pode ser nula."]);
            exit;
        }

        //Validação dos campos que vieram no PATCH
        //NOME
        if ($nome !== null && !is_string($nome)) {
            http_response_code(400);
            echo json_encode(["erro" => "Nome deve ser um texto."]);
            exit;
        }

        if ($nome !== null) {
            $nome = trim($nome);
        }

        //PREÇO
        if ($preco !== null && !is_int($preco) && !is_float($preco)) {
            http_response_code(400);
            echo json_encode(["erro" => "Preço deve ser um número."]);
            exit;
        }

        //QUANTIDADE
        if ($quantidade !== null && !is_int($quantidade)) {
            http_response_code(400);
            echo json_encode(["erro" => "Quantidade deve ser um número inteiro."]);
            exit;
        }

        try {
            $produtoAtualizado = $this->service->atualizar(
                $id,
                $nome,
                $preco,
                $quantidade
            );

            if ($produtoAtualizado === null) {
                http_response_code(404);
                echo json_encode(["erro" => "Produto não encontrado."]);
                exit;
            }

            http_response_code(200);

            echo json_encode($produtoAtualizado);

        } catch (PDOException $erro) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno do servidor."]);
            exit;

        } catch (Exception $erro) {
            http_response_code(400);
            echo json_encode(["erro" => $erro->getMessage()]);
            exit;
        }

    }

    public function excluir(int $id): void
    {
        try{
            $excluido = $this->service->excluir($id);

            if (!$excluido) {
                http_response_code(404);
                echo json_encode(["erro" => "Produto não encontrado."]);
                exit;
            }
            //204 - Significa que a operação deu certo, mas não tem o que devolver.
            http_response_code(204);

        //Caso falhe o PDO, retorna erro
        } catch (PDOException $erro) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno do servidor."]);
            exit;
        }
    }

}