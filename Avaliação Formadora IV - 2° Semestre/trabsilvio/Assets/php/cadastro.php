<?php
$conn = mysqli_connect("localhost", "root", "", "loja");

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$cpf = mysqli_real_escape_string($conn, $_POST['cpf']);
$telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
$senha = mysqli_real_escape_string($conn, $_POST['senha']);

$sqlVerifica = "SELECT id_pessoa FROM pessoa WHERE email = '$email' OR cpf = '$cpf'";
$resultado = mysqli_query($conn, $sqlVerifica);

if (mysqli_num_rows($resultado) > 0) {
    echo "❌ O usuário já possui conta no sistema.";
    exit;
}

if (isset($_POST['cadastroEntregador'])) {
    $tipo_usuario = 'entregador';
} else {
    $tipo_usuario = 'cliente';
}

$sql = "INSERT INTO pessoa (nome, email, cpf, telefone, senha, tipo_usuario) 
        VALUES ('$nome', '$email', '$cpf', '$telefone', '$senha', '$tipo_usuario')";

if (mysqli_query($conn, $sql)) {
    $idPessoa = mysqli_insert_id($conn);

    if ($tipo_usuario === 'entregador') {

        $cnh = mysqli_real_escape_string($conn, $_POST['cnh']);
        $veiculo = mysqli_real_escape_string($conn, $_POST['veiculo']);

        $sqlEntregador = "INSERT INTO entregador (id_pessoa, cnh, tipo_veiculo) 
                          VALUES ('$idPessoa', '$cnh', '$veiculo')";

        if (mysqli_query($conn, $sqlEntregador)) {
            echo '✅ Cadastro de entregador realizado com sucesso!';
        } else {
            echo "⚠️ Erro ao cadastrar entregador: " . mysqli_error($conn);
        }

    } else {

        // >>>>> CRIA O CLIENTE AQUI <<<<<
        $sqlCliente = "INSERT INTO cliente (id_pessoa, metodo_pagamento, numero_cartao)
                       VALUES ('$idPessoa', '', '')";

        if (!mysqli_query($conn, $sqlCliente)) {
            echo "⚠️ Erro ao cadastrar cliente: " . mysqli_error($conn);
            exit;
        }

        echo '✅ Cadastro realizado com sucesso!';
    }

} else {
    echo "⚠️ Erro: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
