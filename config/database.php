<?php

/* 
   - onde está o banco?
   - qual banco queremos?
   - qual usuário vai acessar?
   - qual senha? 
*/

/*Lê as credenciais de forma segura das variáveis de ambiente (ex: getenv('DB_PASS')), 
evitando deixar senhas escritas diretamente no código.*/
$host = getenv("MYSQLHOST") ?: "localhost";
$porta = getenv("MYSQLPORT") ?: "3306";
$banco = getenv("MYSQLDATABASE") ?: "controle_estoque";
$usuario = getenv("MYSQLLUSER") ?: "root";
$senha = getenv("MYSQLPASSWORD") ?: "";

//Monta o endereço da conexão
//$dsn (Data Source Name) = Nome da Fonte de Dados
$dsn = "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4";

/*
Criação do PDO (PHP Data Objects) 
--> camada de acesso a banco de dados no PHP
--> é como um tradutor universal entre o PHP e vários bancos diferentes
*/
try {
    $pdo = new PDO($dsn, $usuario, $senha);// PDO é uma classe nativa do PHP

    $pdo->setAttribute(/*setAttribute é um método nativo da classe PDO. Ele é como um botão de configuração. 
    Ele pede: (Qual config você quer mudar, Qual valor você quer colocar nela)
    */
        PDO::ATTR_ERRMODE,//ATTRibuto de ERRor MODE -> config de como PDO deve se comportar quando der erro.
        PDO::ERRMODE_EXCEPTION//ERRor MODE EXCEPTION -> modo de erro: lançar exceção
    );

// PDOException é uma classe de erro específica do PDO.
} catch (PDOException $erro) {//$erro variável que vai guardar os detalhes desse erro

    /*Registra o erro internamente no log do servidor, 
    evitando expor detalhes técnicos para o cliente da API.*/
    error_log($erro->getMessage());

    http_response_code(500);
     /*
    garante que a resposta continue sendo JSON
    mesmo que a conexão falhe antes de o restante da API ser carregado.*/
    header("Content-type: application/json");
    echo json_encode(["erro" => "Erro interno do servidor."]);

    exit;
}