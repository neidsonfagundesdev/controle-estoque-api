<?php

//Responsável por conversar com a tabela produtos no MySQL.

class ProdutoRepository {

    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    public function listarTodos(): array 
    {
        $sql = "SELECT * FROM produtos";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar(string $nome, float $preco, int $quantidade): int
    {   
        //Comando sql para inserção de dados
        $sql = "INSERT INTO produtos (nome, preco, quantidade)
                VALUES ( :nome, :preco, :quantidade)";
        
        /*prepare() é um método que envia o comando (molde SQL) ao banco 
        o banco já sabe o que é comando. Os valores virão depois separados. Isso evita SQL Injection.*/
        $stmt = $this->pdo->prepare($sql);

        // execute() é um método que preenche o molde com os valores e manda o banco rodar.
        // Os valores são tratados como dados, nunca como comando.
        $stmt->execute([
            "nome" => $nome,
            "preco" => $preco,
            "quantidade" => $quantidade
        ]);

        /*lastInsertId() é um método. Ele pergunta ao banco qual foi o ID 
          gerado automaticamente pelo último INSERT feito nesta conexão.*/
        $novoId = $this->pdo->lastInsertId();

        //retorna o novo id 
        return (int) $novoId;

    }
}

