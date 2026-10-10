create table if not exists identifacaoes-pedidos(
    id          integer primary key autoincrement,
    nome        text not null,
    tipo_pedido text not null
        check (tipo_pedido in ('entrega', 'retirada')),
    endereco    text,
    forma_pagamento text not null
        check('dinheiro', 'pix', 'cartao'),
    created_at  datetime not null default current_timestamp,

        check(tipo_pedido = 'entrega' and endereco is not null and length(trim(endereco)) > 0)
        or
        (tipo_pedido = 'retirada', and endereco is null)
);

