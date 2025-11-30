<?php
$conn = mysqli_connect("localhost", "root", "", "loja");

if (!$conn) {
    die("Erro na conexão com o banco.");
}

$id = $_POST["id"] ?? null;

if (!$id) {
    echo "ID inválido.";
    exit;
}

// Buscar imagem para excluir arquivo
$res = mysqli_query($conn, "SELECT imagem FROM produto WHERE id_produto = $id");
$dados = mysqli_fetch_assoc($res);
$imagem = $dados["imagem"] ?? "";

if ($imagem && file_exists("../../Assets/img/" . $imagem)) {
    unlink("../../Assets/img/" . $imagem);
}

$sql = "DELETE FROM produto WHERE id_produto = $id";

if (mysqli_query($conn, $sql)) {
    echo "OK";
} else {
    echo "Erro ao excluir: " . mysqli_error($conn);
}
