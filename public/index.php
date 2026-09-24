<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../src/Repository/ProdutoRepository.php";

$produtoRepository = new ProdutoRepository($pdo);

/*Serve para avisar o navegador, aplicativo ou sistema que está recebendo a resposta 
que o conteúdo enviado é um JSON.*/
header("Content-Type: application/json");

//Pega o método http utilizado na requisição (get, post, patch, delete)
$metodo = $_SERVER['REQUEST_METHOD'];

//Condição aplicada ao que foi solicitado pelo cliente
if ($metodo == "GET") {

    $produtos = $produtoRepository->listarTodos();
    
    echo json_encode($produtos);

} elseif ($metodo == "POST") {
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

    $nome = trim($dados["nome"] ?? "");
    $preco = $dados["preco"] ?? null;
    $quantidade = $dados["quantidade"] ?? null;

    //Verifica se o campo nome está vazio. O === compara valor e tipo
    if ($nome === "") {
        //retorna um status http 400 (o cliente enviou algo inválido)
        http_response_code(400);
        echo json_encode(["erro"=>"Nome não pode estar vazio."]);

        exit;
    } 

    /*Validação do preço:
      - obrigatório
      - deve ser numérico
      - deve ser maior que zero
    */
    if ($preco === null) {
        http_response_code(400);
        echo json_encode(["erro"=>"Preço é obrigatório."]);

        exit;
    }

    if (!is_numeric($preco)) {
        http_response_code(400);
        echo json_encode(["erro"=>"Preço deve ser numérico."]);

        exit;
    }

    if ($preco <= 0) {
        http_response_code(400);
        echo json_encode(["erro"=>"Preço não pode ser 0 ou negativo."]);

        exit;
    }

    /*Validação da quantidade:
      - obrigatório
      - deve ser numérico
      - não deve ser menor que zero
    */
    if ($quantidade === null) {
        http_response_code(400);
        echo json_encode(["erro"=>"Quantidade é obrigatória."]);

        exit;
    }

    if (!is_numeric($quantidade)) {
        http_response_code(400);
        echo json_encode(["erro"=>"Quantidade deve ser númerica."]);

        exit;
    }

    if ($quantidade < 0) {
        http_response_code(400);
        echo json_encode(["erro"=>"quantidade não deve ser negativa."]);

        exit;
    }

    //chama a função criar do produto repository
    $novoId = $produtoRepository->criar(
        $nome,
        $preco,
        $quantidade
    );

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

} elseif ($metodo == "PATCH") {
    echo json_encode(["mensagem" => "PATCH ainda não implementado"]);

} elseif ($metodo == "DELETE") {
    echo json_encode(["mensagem" => "DELETE ainda não implementado"]);
}