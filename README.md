# API REST - Situação Acadêmica

## Integrantes
- Rafael De Oliveira De Almeida

## Descrição
API REST desenvolvida em PHP utilizando Slim Framework 4.

O sistema permite cadastrar, consultar, atualizar e remover situações acadêmicas de estudantes em unidades curriculares.

Os dados são armazenados em um arquivo JSON, sem utilização de banco de dados.

## Tecnologias utilizadas
- PHP
- Slim Framework 4
- Composer
- JSON
- cURL

## Instalação

1. Clone o repositório:

```bash
git clone LINK_DO_REPOSITORIO
```

2. Entre na pasta:

```bash
cd api-situacao-academica
```

3. Instale as dependências:

```bash
composer install
```

4. Inicie o servidor:

```bash
php -S localhost:8000 -t public
```

## Endpoints

### POST /situacao

Cadastra uma nova situação acadêmica.

Exemplo de entrada:

```json
{
    "nome": "Maria da Silva",
    "unidadeCurricular": "Desenvolvimento Web",
    "aprovado": true
}
```

Resposta: `201 Created`.

### GET /situacao/{id}

Consulta a situação acadêmica de um estudante pelo ID.

Resposta: `200 OK`.

### PUT /situacao/{id}

Atualiza exclusivamente o campo `aprovado`.

Exemplo de entrada:

```json
{
    "aprovado": false
}
```

Resposta: `200 OK`.

### DELETE /situacao/{id}

Remove a situação acadêmica pelo ID.

Resposta:

```json
{
    "mensagem": "Registro removido com sucesso."
}
```

Resposta HTTP: `200 OK`.

## Códigos HTTP

- 200: Requisição realizada com sucesso.
- 201: Registro cadastrado com sucesso.
- 400: Dados inválidos.
- 404: Estudante não encontrado.

## Regras de negócio

- Cada estudante recebe um ID automaticamente.
- O ID é utilizado para localizar os registros.
- O PUT permite alterar somente o campo aprovado.
- Os dados de entrada e saída utilizam JSON.
- Após a exclusão, o registro não pode mais ser consultado.

## Armazenamento

Os dados são armazenados no arquivo `storage/situacoes.json`, em formato JSON.

Não é utilizado banco de dados. Os registros permanecem salvos mesmo após reiniciar o servidor.