<?php

/* 
   - onde está o banco?
   - qual banco queremos?
   - qual usuário vai acessar?
   - qual senha? 
*/
$host = "localhost";
$banco = "controle_estoque";
$usuario = "root";
$senha = "";

//Monta o endereço da conexão
//$dsn (Data Source Name) = Nome da Fonte de Dados
$dsn = "mysql:host=$host;dbname=$banco;charset=utf8mb4";

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

    //se a conexão falhar, o script para e mostra o erro. Evita erros estranhos na tela.
    die("Erro ao conectar ao banco.");
}