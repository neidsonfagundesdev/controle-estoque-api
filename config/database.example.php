<?php

$host = "localhost";
$banco = "controle_estoque";
$usuario = "SEU_USUARIO";
$senha = "SUA_SENHA";

$dsn = "mysql:host=$host;dbname=$banco;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $usuario, $senha);

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $erro) {//$erro variável que vai guardar os detalhes desse erro
    http_response_code(500);
    /*
    garante que a resposta continue sendo JSON
    mesmo que a conexão falhe antes de o restante da API ser carregado.*/
    header("Content-type: application/json");
    echo json_encode(["erro" => "Erro interno do servidor."]);

    exit;
}