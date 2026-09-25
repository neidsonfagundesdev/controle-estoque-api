<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../src/Repository/ProdutoRepository.php";
require_once __DIR__ . "/../src/Service/ProdutoService.php";
require_once __DIR__ . "/../src/Controller/ProdutoController.php";

$produtoRepository = new ProdutoRepository($pdo);
$produtoService = new ProdutoService($produtoRepository);
$produtoController = new ProdutoController($produtoService);

/*Serve para avisar o navegador, aplicativo ou sistema que está recebendo a resposta 
que o conteúdo enviado é um JSON.*/
header("Content-Type: application/json");

//Pega o método http utilizado na requisição (get, post, patch, delete)
$metodo = $_SERVER['REQUEST_METHOD'];

//Condição aplicada ao que foi solicitado pelo cliente
if ($metodo === "GET") {
    $produtoController->listarTodos();

} elseif ($metodo === "POST") {
    $produtoController->criar();

} elseif ($metodo === "PATCH") {
    echo json_encode(["mensagem" => "PATCH ainda não implementado"]);

} elseif ($metodo === "DELETE") {
    echo json_encode(["mensagem" => "DELETE ainda não implementado"]);
}