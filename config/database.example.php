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
} catch (PDOException $erro) {
    die("Erro ao conectar ao banco.");
}