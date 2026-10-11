<?php

declare(strict_types=1);

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Requisição inválida.');
}

$nome = $_POST['nome'] ?? '';
$tipoPedido = $_POST['tipo_pedido'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$pagamento = $_POST['pagamento'] ?? '';

if (
    !is_string($nome) ||
    !is_string($tipoPedido) ||
    !is_string($endereco) ||
    !is_string($pagamento)
) {
    http_response_code(400);
    exit('Dados inválidos.');
}

$nome = trim($nome);
$endereco = trim($endereco);

$tiposPermitidos = ['entrega', 'retirada'];
$pagamentosPermitidos = ['dinheiro', 'pix', 'cartao'];

if (mb_strlen($nome) < 2 || mb_strlen($nome) > 100) {
    exit('O nome deve ter entre 2 e 100 caracteres.');
}

if (!in_array($tipoPedido, $tiposPermitidos, true)) {
    exit('Tipo de pedido inválido.');
}

if (!in_array($pagamento, $pagamentosPermitidos, true)) {
    exit('Forma de pagamento inválida.');
}

if ($tipoPedido === 'entrega' && $endereco === '') {
    exit('O endereço é obrigatório para entregas.');
}

if (mb_strlen($endereco) > 255) {
    exit('O endereço deve ter no máximo 255 caracteres.');
}

if ($tipoPedido === 'retirada') {
    $endereco = null;
}

try {
    require_once dirname(__DIR__) . '/config/database.php';

    $sql = '
        INSERT INTO identificacoes_pedido
            (nome, tipo_pedido, endereco, forma_pagamento)
        VALUES
            (:nome, :tipo_pedido, :endereco, :pagamento)
    ';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nome' => $nome,
        ':tipo_pedido' => $tipoPedido,
        ':endereco' => $endereco,
        ':pagamento' => $pagamento
    ]);

    unset($_SESSION['csrf_token']);

    header('Location: ../form.php?sucesso=1');
    exit;
} catch (PDOException $e) {
    error_log('Erro ao salvar pedido: ' . $e->getMessage());

    http_response_code(500);

    exit('Erro ao salvar: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
