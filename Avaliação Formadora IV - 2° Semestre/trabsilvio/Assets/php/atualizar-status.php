<?php
session_start();

$con = new mysqli("localhost", "root", "", "loja");

if ($con->connect_error) {
    die("Erro: " . $con->connect_error);
}

// Verifica se o entregador está logado para garantir segurança
if (!isset($_SESSION['id'])) {
    die("Erro: Usuário não logado");
}

$id_pedido = $_POST['id_pedido'];
$acao = isset($_POST['acao']) ? $_POST['acao'] : 'atualizar'; // Define se é para aceitar ou só atualizar

if ($acao == 'aceitar') {
    // LÓGICA DE ACEITAR O PEDIDO
    $id_entregador = $_SESSION['id'];
    
    // Atualiza o entregador e já muda o status para "Em rota" ou similar
    $sql = "UPDATE pedido SET id_entregador = '$id_entregador', status = 'Aceito pelo entregador' WHERE id_pedido = $id_pedido";

} else {
    // LÓGICA ANTIGA (MUDAR STATUS DROPDOWN)
    $status = $_POST['status'];
    $sql = "UPDATE pedido SET status = '$status' WHERE id_pedido = $id_pedido";
}

if ($con->query($sql)) {
    echo "ok";
} else {
    echo "erro: " . $con->error;
}
?>