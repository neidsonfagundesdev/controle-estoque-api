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

//
$rota = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

//
$partes = explode("/", trim( $rota, "/"));

//GET --> BUSCAR/LER
//Condição aplicada ao que foi solicitado pelo cliente
if ($metodo === "GET" && $partes[0] === "produtos" && count($partes) === 1) {
    $produtoController->listarTodos();

} elseif ($metodo === "GET" && $partes[0] === "produtos" && count($partes) === 2 && ctype_digit($partes[1])) {
    $id = (int) $partes[1]; 
    $produtoController->buscarPorId($id);

//POST --> CRIAR/ENVIAR
} elseif ($metodo === "POST" && $partes[0] === "produtos" && count($partes) === 1) {
    $produtoController->criar();

//PATCH --> ATUALIZAR UMA PARTE. MUDAR SÓ UM ITEM.
} elseif ($metodo === "PATCH" && $partes[0] === "produtos" && count($partes) === 2 && ctype_digit($partes[1])) {
    $id = (int) $partes[1];
    $produtoController->atualizar($id);

//DELE --> APAGAR
} elseif ($metodo === "DELETE" && $partes[0] === "produtos" && count($partes) === 2 && ctype_digit($partes[1])) {
    $id = (int) $partes[1];
    $produtoController->excluir($id);
}