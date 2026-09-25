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
    //decodifica os dados json para PHP
    $dados = json_decode($corpo, true);

    //verifica se o json é válido
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["erro"=>"JSON inválido."]);

        exit;
    }

    //verifica se o JSON virou um array em PHP, caso contrario, dará erro 
    if (!is_array($dados)) {
        //retorna um status http 400 (o cliente enviou algo inválido)
        http_response_code(400);
        echo json_encode(["erro"=>"O corpo da requisição deve ser um objeto JSON."]);

        exit;
    }
    
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

    } catch (Exception $erro) {

        http_response_code(400);

        echo json_encode([
            "erro"=> $erro->getMessage()
        ]);

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
        $produtos = $this->service->listarTodos();

        echo json_encode($produtos);
    }

}