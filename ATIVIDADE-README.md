# Atividade — Padrão de Projeto Adapter (PHP)

**Dupla:** Mariana Akemi e Giovana Marsigli

## 1. Objetivo

Implementar o padrão de projeto estrutural **Adapter** para resolver a
incompatibilidade entre a interface `Book` (esperada pelo cliente) e a
interface `EBook` (subsistema externo, com nomenclatura e tipos de
retorno diferentes), utilizando **composição** (Adapter de Objeto).

## 2. Passos executados

### 2.1 Clonagem do repositório base

```bash
git clone https://github.com/DesignPatternsPHP/DesignPatternsPHP.git
cd DesignPatternsPHP
```

### 2.2 Instalação de dependências

```bash
composer install
```

### 2.3 Mapeamento do domínio

Analisamos os componentes já existentes em `Structural/Adapter/`:

| Componente | Papel no padrão | Descrição |
|---|---|---|
| `Book` | **Target** | Interface esperada pelo cliente: `open()`, `turnPage()`, `getPage(): int` |
| `PaperBook` | Implementação concreta do Target | Livro físico, já compatível com `Book` |
| `EBook` | **Adaptee (interface)** | Subsistema externo: `unlock()`, `pressNext()`, `getPage(): array` (retorna `[paginaAtual, totalPaginas]`) |
| `Kindle` | **Adaptee (implementação)** | Simula um leitor digital de terceiros, com nomes de métodos e tipo de retorno diferentes de `Book` |

### 2.4 Identificação do conflito

O código cliente que consome `Book` não consegue usar `Kindle`
diretamente, pois:

- `Book::open()` ↔ `Kindle` não tem `open()`, tem `unlock()`
- `Book::turnPage()` ↔ `Kindle` não tem `turnPage()`, tem `pressNext()`
- `Book::getPage(): int` ↔ `Kindle::getPage(): array` (retorna
  `[paginaAtual, totalPaginas]`, não um `int` simples)

### 2.5 Criação da classe adaptadora — `EBookAdapter.php`

Criado o arquivo `Structural/Adapter/EBookAdapter.php`.

A classe:

- **Implementa `Book`**, mantendo compatibilidade com o código cliente
  (Target).
- **Recebe uma instância de `EBook` via injeção de dependência no
  construtor** (composição — Adapter de Objeto, não herança), guardando-a
  em um atributo privado.
- **Traduz cada chamada** do contrato `Book` para o método real do
  `EBook`:
  - `open()` → delega para `unlock()`
  - `turnPage()` → delega para `pressNext()`
  - `getPage(): int` → chama `EBook::getPage()` (que retorna
    `[paginaAtual, totalPaginas]`) e devolve apenas o primeiro elemento
    do array, convertendo a resposta para o formato que o cliente espera.

Trecho principal (comentado no código-fonte):

```php
class EBookAdapter implements Book
{
    private EBook $eBook;

    public function __construct(EBook $eBook)
    {
        $this->eBook = $eBook;
    }

    public function open()
    {
        $this->eBook->unlock();
    }

    public function turnPage()
    {
        $this->eBook->pressNext();
    }

    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}
```

### 2.6 Validação

Rodamos os testes existentes em `Tests/AdapterTest.php`, que cobrem:

1. `PaperBook` respondendo corretamente ao contrato `Book`.
2. `Kindle` envolvido por `EBookAdapter` respondendo ao mesmo contrato
   `Book`, mesmo tendo uma implementação interna totalmente diferente.

Resultado: ambos os testes passam, confirmando que o cliente consegue
tratar `PaperBook` e `Kindle` (via `EBookAdapter`) de forma
polimórfica, sem precisar conhecer as diferenças internas de cada um.

## 3. Conclusão

O padrão Adapter permitiu integrar um subsistema externo (`Kindle`),
com interface incompatível, ao código cliente já existente, **sem
alterar nem o cliente nem a classe `Kindle`**. Toda a tradução de
chamadas e de tipos de retorno ficou isolada em uma única classe
(`EBookAdapter`), respeitando o princípio Open/Closed e mantendo o
sistema flexível para adaptar outros leitores digitais no futuro,
bastando que eles implementem `EBook`.
