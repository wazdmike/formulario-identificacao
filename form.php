<?php

declare(strict_types=1);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<h2>Identificação do Pedido</h2>
<form action="./includes/identificacao.php" method="POST">


    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
    >


    <label>Nome</label>
    <input type="text" id="nome" name="nome" maxlength="100" required>
    <br>

    <label>Tipo do pedido: </label>
    <input type="radio" id="entrega" name="tipo_pedido" value="entrega">
    <label for="entrega">Entrega</label>

    <input type="radio" id="entrega" name="tipo_pedido" value="retirada">
    <label for="retirada">retirada</label>
    <br>

    <label for="endereco">Endereço de entrega: </label>
    <input type="text" id="endereco" name="endereco">

    <label for="pagamento">Forma de pagamento: </label>
    <select name="pagamento" id="pagamento" required>
        <option value="">Selecione</option>
        <option value="pix">Pix</option>
        <option value="cartao">Cartao</option>
        <option value="dinheiro">dinheiro</option>
    </select>

    <br>

    <button type="submit">Confirmar</button>
</form>