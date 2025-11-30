<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "", "loja");
if (!$conn) { die("Erro na conexão: " . mysqli_connect_error()); }

$nome = $_POST['nome'] ?? null;
$descricao = $_POST['descricao'] ?? null;
$preco = $_POST['preco'] ?? null;
$estoque = $_POST['estoque'] ?? null;

$imagem = null;
if (!empty($_FILES['imagem']['name'])) {
    $nomeArquivo = basename($_FILES['imagem']['name']);
    $nomeArquivo = preg_replace("/[^A-Za-z0-9\.\-_]/", "_", $nomeArquivo);
    $destino = __DIR__ . "/../img/" . $nomeArquivo; // assets/img/
    if (!is_dir(dirname($destino))) {
        mkdir(dirname($destino), 0755, true);
    }
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
        $imagem = $nomeArquivo;
    } else {
        echo "Erro ao mover arquivo.";
        exit;
    }
}

// prepared statement
$sql = "INSERT INTO produto (nome_produto, descricao, preco, quant_estoque, imagem) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssdss", $nome, $descricao, $preco, $estoque, $imagem);

if (mysqli_stmt_execute($stmt)) {
    echo "OK";
} else {
    echo "Erro ao cadastrar: " . mysqli_error($conn);
}
