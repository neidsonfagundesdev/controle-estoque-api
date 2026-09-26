<?php

// Responsável pelas regras do negócio

require_once __DIR__ . "/../Repository/ProdutoRepository.php";

class ProdutoService
{
    private ProdutoRepository $repository;

    public function __construct(ProdutoRepository $repository)
    {
        $this->repository = $repository;
    }

    //Valida os dados do array recebido
    public function criar(string $nome, float $preco, int $quantidade): int
    {
        if ($nome === "") {
            throw new Exception("Nome não pode estar vazio.");
        }

        if ($preco <= 0) {
            throw new Exception("Preço deve ser maior que zero.");
        }

        if ($quantidade < 0) {
            throw new Exception("Quantidade não pode ser negativa.");
        }

        return $this->repository->criar(
            $nome,
            $preco,
            $quantidade
        );
    }

    //lista todos os produtos
    public function listarTodos(): array
    {
        return $this->repository->listarTodos();
    }

    //busca o produto por id
    public function buscarPorId(int $id): ?array
    {
        return $this->repository->buscarPorId($id);
    }
}