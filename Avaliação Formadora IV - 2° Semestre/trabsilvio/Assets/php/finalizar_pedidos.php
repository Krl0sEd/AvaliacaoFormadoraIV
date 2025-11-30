<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "loja");
if (!$conn) {
    die("Erro na conexão!");
}

// VERIFICA LOGIN
if (!isset($_SESSION['id'])) {
    die("ERRO: Usuário não logado.");
}

$id_cliente = $_SESSION['id'];

// RECEBE DADOS DO FORM
$cep = $_POST['cep'];
$rua = $_POST['rua'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$complemento = $_POST['complemento'];

$metodo_pagamento = $_POST['metodo_pagamento'];
$numero_cartao = $_POST['numero_cartao'];

if ($metodo_pagamento == "Pix") {
    $numero_cartao = "";
}

// CARRINHO
$carrinho = json_decode($_POST['carrinho_json'], true);

if (!$carrinho || count($carrinho) == 0) {
    die("ERRO: Carrinho vazio.");
}

// SALVA ENDEREÇO
$sql_end = "INSERT INTO endereco (id_cliente, cep, rua, bairro, cidade, estado, complemento) 
            VALUES ('$id_cliente', '$cep', '$rua', '$bairro', '$cidade', '$estado', '$complemento')";

if (!mysqli_query($conn, $sql_end)) {
    die("Erro ao salvar endereço: " . mysqli_error($conn));
}

$id_endereco = mysqli_insert_id($conn);

// ATUALIZA PAGAMENTO
$sql_cliente = "UPDATE cliente 
                SET metodo_pagamento='$metodo_pagamento', numero_cartao='$numero_cartao'
                WHERE id_pessoa='$id_cliente'";

mysqli_query($conn, $sql_cliente);

// CRIA PEDIDO
// Note que aqui NÃO passamos o id_entregador, o banco agora preencherá com NULL automaticamente
$data_pedido = date("Y-m-d H:i:s");
$status = "Em separação";
$primeiro_nome = $carrinho[0]['nome_produto'] ?? "Pedido";

$sql_pedido = "INSERT INTO pedido (id_cliente, id_endereco, nome_produto, data_pedido, preco_final, status)
               VALUES ('$id_cliente', '$id_endereco', '$primeiro_nome', '$data_pedido', '0', '$status')";

if (!mysqli_query($conn, $sql_pedido)) {
    die("Erro ao criar pedido: " . mysqli_error($conn));
}

$id_pedido = mysqli_insert_id($conn);

// ITENS
$total = 0;

foreach ($carrinho as $item) {

    $id_produto = $item['id_produto'];
    $quantidade = $item['quantidade'];
    $preco = floatval($item['preco']);

    $total += ($quantidade * $preco);

    $sql_item = "INSERT INTO item_pedido (id_produto, id_pedido, quantidade, preco_unitario)
                 VALUES ('$id_produto', '$id_pedido', '$quantidade', '$preco')";

    mysqli_query($conn, $sql_item);

    $sql_est = "UPDATE produto 
                SET quant_estoque = quant_estoque - $quantidade 
                WHERE id_produto = $id_produto";

    mysqli_query($conn, $sql_est);
}

// ATUALIZA TOTAL
$sql_total = "UPDATE pedido SET preco_final = '$total' WHERE id_pedido = '$id_pedido'";
mysqli_query($conn, $sql_total);

echo "OK";

?>