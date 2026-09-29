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
    //API rejeita o dado antes do banco decidir o que fazer com ele
    public function criar(string $nome, float $preco, int $quantidade): int
    {
        if ($nome === "") {
            throw new Exception("Nome não pode estar vazio.");
        }
        //Verifica quantidade de caracteres
        if (strlen($nome) > 150) {
            throw new Exception("Nome deve ter no máximo 150 caracteres.");
        }

        if ($preco <= 0) {
            throw new Exception("Preço deve ser maior que zero.");
        }

        //Evita números infinitos. is_finite verifica se o núemro é finito com !, o sisteam verifica se ele não é finito e retorna erro
        if (!is_finite($preco)) {
            throw new Exception("Preço inválido.");
        }

        //Impõe limite de preço e casas decimais
        if ($preco > 99999999.99) {
            throw new Exception("Preço excede o limite permitido.");
        }

        /* 
        Multiplicamos o preço por 100 para verificar as casas decimais.
        
        --> round() arredonda o valor.
        --> abs() pega apenas o tamanho da diferença entre o valor original
        e o arredondado.

        Como float pode ter pequenas imprecisões, aceitamos uma tolerância.
        Se a diferença for maior que 0.000001,
        o preço tem mais de 2 casas decimais.
        */
        $centavos = $preco * 100;

        if (abs($centavos - round($centavos)) > 0.000001) {
            throw new Exception("Preço deve ter no máximo 2 casas decimais.");
        }

        if ($quantidade < 0) {
            throw new Exception("Quantidade não pode ser negativa.");
        }
        //Impõe limite máximo ao item quantidade
        if ($quantidade > 2147483647) {
            throw new Exception("Quantidade excede o limite permitido.");
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

    //Atualiza o dado no banco
    public function atualizar(int $id, ?string $nome, int|float|null $preco, ?int $quantidade): ?array
    {
        $produto = $this->repository->buscarPorId($id);

        if ($produto === null) {
            return null;
        }
        //Coração do PATCH. Se chegou novo nome, use. Se não chegou, use o do banco mesmo.
        $nomeAtualizado = $nome ?? $produto["nome"];
        $precoAtualizado = $preco ?? (float) $produto["preco"];
        $quantidadeAtualizada = $quantidade ?? (int) $produto["quantidade"];

        if ($nomeAtualizado === "") {
            throw new Exception ("Nome não pode estar vazio.");
        }

        if ($precoAtualizado <= 0) {
            throw new Exception ("Preço deve ser maior do que zero.");
        }

        if ($quantidadeAtualizada < 0) {
            throw new Exception ("Quantidade não pode ser negativa.");
        }

        $this->repository->atualizar(
            $id,
            $nomeAtualizado,
            $precoAtualizado,
            $quantidadeAtualizada
        );

        return [
            "id" => $id,
            "nome" => $nomeAtualizado,
            "preco" => $precoAtualizado,
            "quantidade" => $quantidadeAtualizada
        ];
    }

    //Exclui um item pelo id
    public function excluir(int $id): bool
    {
        $produto = $this->repository->buscarPorId($id);

        if ($produto === null) {
            return false;
        }

        return $this->repository->excluir($id);
    }

}