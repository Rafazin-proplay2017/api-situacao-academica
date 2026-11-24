
<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// Arquivo para armazenar os registros
$arquivo = __DIR__ . '/../storage/situacoes.json';

// Criar arquivo caso não exista
if (!is_dir(dirname($arquivo))) {
    mkdir(dirname($arquivo), 0777, true);
}

if (!file_exists($arquivo)) {
    file_put_contents($arquivo, '{}');
}

// Ler os registros salvos
function lerSituacoes($arquivo) {
    $dados = json_decode(file_get_contents($arquivo), true);

    return is_array($dados) ? $dados : [];
}

// Salvar os registros
function salvarSituacoes($arquivo, $situacoes) {
    file_put_contents(
        $arquivo,
        json_encode($situacoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

// POST - Cadastrar situação acadêmica
$app->post('/situacao', function (Request $request, Response $response) use ($arquivo) {

    $dados = $request->getParsedBody();

    if (
        !is_array($dados) ||
        !isset($dados['nome'], $dados['unidadeCurricular']) ||
        !is_string($dados['nome']) ||
        trim($dados['nome']) === '' ||
        !is_string($dados['unidadeCurricular']) ||
        trim($dados['unidadeCurricular']) === '' ||
        !array_key_exists('aprovado', $dados) ||
        !is_bool($dados['aprovado'])
    ) {
        $response->getBody()->write(json_encode([
            'erro' => 'Dados inválidos.'
        ]));

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
    }

    $situacoes = lerSituacoes($arquivo);

    $id = 1;

    if (!empty($situacoes)) {
        $ids = array_map('intval', array_keys($situacoes));
        $id = max($ids) + 1;
    }

    $situacoes[$id] = [
        'id' => $id,
        'nome' => $dados['nome'],
        'unidadeCurricular' => $dados['unidadeCurricular'],
        'aprovado' => $dados['aprovado']
    ];

    salvarSituacoes($arquivo, $situacoes);

    $response->getBody()->write(json_encode($situacoes[$id]));

    return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
});

// GET - Consultar situação acadêmica
$app->get('/situacao/{id}', function (Request $request, Response $response, array $args) use ($arquivo) {

    $situacoes = lerSituacoes($arquivo);
    $id = (int) $args['id'];

    if (!isset($situacoes[$id])) {
        $response->getBody()->write(json_encode([
            'erro' => 'Estudante não encontrado.'
        ]));

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
    }

    $response->getBody()->write(json_encode($situacoes[$id]));

    return $response->withHeader('Content-Type', 'application/json');
});

// PUT - Atualizar somente o campo aprovado
$app->put('/situacao/{id}', function (Request $request, Response $response, array $args) use ($arquivo) {

    $situacoes = lerSituacoes($arquivo);
    $id = (int) $args['id'];
    $dados = $request->getParsedBody();

    if (!isset($situacoes[$id])) {
        $response->getBody()->write(json_encode([
            'erro' => 'Estudante não encontrado.'
        ]));

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
    }

    if (
        !is_array($dados) ||
        count($dados) !== 1 ||
        !array_key_exists('aprovado', $dados) ||
        !is_bool($dados['aprovado'])
    ) {
        $response->getBody()->write(json_encode([
            'erro' => 'Informe somente o campo aprovado com true ou false.'
        ]));

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
    }

    $situacoes[$id]['aprovado'] = $dados['aprovado'];

    salvarSituacoes($arquivo, $situacoes);

    $response->getBody()->write(json_encode($situacoes[$id]));

    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE - Excluir situação acadêmica
$app->delete('/situacao/{id}', function (Request $request, Response $response, array $args) use ($arquivo) {

    $situacoes = lerSituacoes($arquivo);
    $id = (int) $args['id'];

    if (!isset($situacoes[$id])) {
        $response->getBody()->write(json_encode([
            'erro' => 'Estudante não encontrado.'
        ]));

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
    }

    unset($situacoes[$id]);

    salvarSituacoes($arquivo, $situacoes);

    $response->getBody()->write(json_encode([
        'mensagem' => 'Registro removido com sucesso.'
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
