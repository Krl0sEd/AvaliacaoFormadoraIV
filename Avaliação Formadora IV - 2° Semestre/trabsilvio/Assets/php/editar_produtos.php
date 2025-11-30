<?php
$conn = mysqli_connect("localhost", "root", "", "loja");

if (!$conn) {
    echo "Erro na conexão ao banco.";
    exit;
}

$id      = $_POST["id_produto"] ?? null;
$nome    = $_POST["nome"] ?? null;
$desc    = $_POST["descricao"] ?? null;
$preco   = $_POST["preco"] ?? null;
$estoque = $_POST["estoque"] ?? null;

if (!$id || !$nome || !$desc || !$preco || !$estoque) {
    echo "Dados incompletos.";
    exit;
}

// Buscar imagem atual
$res = mysqli_query($conn, "SELECT imagem FROM produto WHERE id_produto = $id");
$dados = mysqli_fetch_assoc($res);
$imagemAtual = $dados["imagem"] ?? "";

// Verifica se enviou uma nova imagem
$novaImagem = $imagemAtual;
if (!empty($_FILES["imagem"]["name"])) {

    $permitidos = ["image/png", "image/jpg", "image/jpeg", "image/webp"];
    if (!in_array($_FILES["imagem"]["type"], $permitidos)) {
        echo "Formato de imagem não permitido!";
        exit;
    }

    $nomeArquivo = time() . "_" . basename($_FILES["imagem"]["name"]);
    $caminhoDestino = "../../Assets/img/" . $nomeArquivo;

    if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoDestino)) {
        $novaImagem = $nomeArquivo;
    } else {
        echo "Erro ao salvar imagem.";
        exit;
    }
}

$sql = "UPDATE produto SET 
        nome_produto   = '$nome',
        descricao      = '$desc',
        preco          = '$preco',
        quant_estoque  = '$estoque',
        imagem         = '$novaImagem'
        WHERE id_produto = $id";

if (mysqli_query($conn, $sql)) {
    echo "OK";
} else {
    echo "Erro ao atualizar: " . mysqli_error($conn);
}
