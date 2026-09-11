<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

/**
 * EBookAdapter (Classe Adaptadora / Adapter)
 *
 * Objetivo do padrão Adapter:
 * Permitir que classes com interfaces incompatíveis trabalhem juntas,
 * "traduzindo" a chamada de um método esperado pelo cliente (Target = Book)
 * para os métodos realmente existentes no objeto adaptado (Adaptee = EBook).
 *
 * Aqui usamos ADAPTER DE OBJETO (composição), e não herança:
 * a classe não estende Kindle, ela apenas GUARDA uma referência para um
 * objeto que implementa EBook e delega/traduz as chamadas para ele.
 * Isso é mais flexível, pois o adapter funciona com QUALQUER implementação
 * de EBook (Kindle, Kobo, um leitor fictício de testes, etc.), não só com Kindle.
 */
class EBookAdapter implements Book
{
    /**
     * Composição: o Adapter guarda uma instância do Adaptee (EBook).
     * É por meio desse atributo que as chamadas serão delegadas.
     */
    private EBook $eBook;

    /**
     * Injeção de dependência via construtor.
     * Quem cria o EBookAdapter decide qual leitor digital concreto
     * (ex.: Kindle) será adaptado, sem o Adapter precisar conhecer
     * detalhes de implementação além do contrato EBook.
     */
    public function __construct(EBook $eBook)
    {
        $this->eBook = $eBook;
    }

    /**
     * Tradução do método open() (esperado pelo cliente que usa Book)
     * para unlock() (método real do EBook/Kindle).
     * Do ponto de vista do subsistema externo, "abrir o livro" digital
     * corresponde a "desbloquear" o dispositivo/arquivo.
     */
    public function open()
    {
        $this->eBook->unlock();
    }

    /**
     * Tradução do método turnPage() (Book) para pressNext() (EBook).
     * "Virar a página" no livro físico equivale a "pressionar próximo"
     * no leitor digital.
     */
    public function turnPage()
    {
        $this->eBook->pressNext();
    }

    /**
     * Tradução de tipo de retorno: o cliente (Book) espera um único
     * inteiro representando a página atual (int).
     * Já o EBook retorna um array no formato [paginaAtual, totalPaginas]
     * (int[]).
     *
     * O Adapter é responsável por resolver essa incompatibilidade,
     * extraindo apenas o primeiro elemento do array (a página atual)
     * e devolvendo no formato que o cliente sabe consumir.
     */
    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}
