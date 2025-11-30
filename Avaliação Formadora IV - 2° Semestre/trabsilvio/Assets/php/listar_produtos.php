<?php
header("Content-Type: application/json");

$conn = mysqli_connect("localhost", "root", "", "loja");

if (!$conn) {
    echo json_encode(["erro" => "Erro na conexão ao banco"]);
    exit;
}

$sql = "SELECT * FROM produto";
$result = mysqli_query($conn, $sql);

$produtos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $produtos[] = $row;
}

echo json_encode($produtos);
