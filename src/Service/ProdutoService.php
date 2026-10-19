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
        $nome = $this->validarNome($nome);
        $this->validarPreco($preco);
        $this->validarQuantidade($quantidade);

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
    public function atualizar(int $id, array $campos): ?array
    {
        $produto = $this->repository->buscarPorId($id);

        if ($produto === null) {
            return null;
        }

        //Valida o nome
        if (array_key_exists("nome", $campos)) {

            $campos["nome"] = $this->validarNome($campos["nome"]);
        }

        //Valida o preço
        if (array_key_exists("preco", $campos)) {
            $this->validarPreco($campos["preco"]);
        }

        //Valida a quantidade
        if (array_key_exists("quantidade", $campos)) {
            $this->validarQuantidade($campos["quantidade"]);
        }

        $this->repository->atualizar($id, $campos);

        return $this->repository->buscarPorId($id);
        
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

    //Métodos auxiliares privados.
    //Eles vão evitar duplicações e possíveis erros futuros com atualizações

    //valida o nome
    private function validarNome(string $nome): string
    {
        $nome = trim($nome);

        if ($nome === "") {
            throw new Exception("Nome não pode estar vazio.");
        }

        /*usamos mb_strlen por que o strlen mede o tamanho em bytes 
        um letra acentuda seria contada como 2 em UTF-8
        mb_strlen() existe justamente para lidar com caracteres que
        podem ocupar mais de um byte*/
        if (mb_strlen($nome, "UTF-8") > 150) {
            throw new Exception("Nome deve ter no máximo 150 caracteres.");
        }

        return $nome;
    }

    //valida o preço
    private function validarPreco(int|float $preco): void
    {
        if ($preco < 0.01) {
            throw new Exception("Preço deve ser no mínimo 0.01.");
        }

        //Evita números infinitos. is_finite verifica se o núemro é finito com !, o sisteam verifica se ele não é finito e retorna erro
        if (!is_finite($preco)) {
            throw new Exception("Preço inválido");
        }

        //Imões limites de preço e casas decimais
        if ($preco > 99999999.99) {
            throw new Exception("Preço excede o limite permitido.");
        }

        /*
        Exemplo: Se $preco for 10.555, o round() transforma em 10.56.
        Como 10.555 é diferente de 10.56, o código entende que há mais de 
        2 casas decimais e dispara o erro.
        */
        if ($preco != round($preco, 2)) {
            throw new Exception("Preço deve ter no máximo 2 casas decimais.");
        }
    }

    //Valida a quantidade
    private function validarQuantidade(int $quantidade): void
    {
        if ($quantidade < 0) {
            throw new Exception("Quantidade não pode ser negativa.");
        }

        //Impõe limite máximo ao item quantidade
        if ($quantidade > 2147483647) {
            throw new Exception("Quantidade excede o limite permitido.");
        }
    }

}