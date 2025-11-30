<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: tela-login.html");
    exit();
}

// 1. Conectar ao banco
$con = new mysqli("localhost", "root", "", "loja");

// Verificar erro
if ($con->connect_error) {
    die("Erro de conexão: " . $con->connect_error);
}

$idUsuario = $_SESSION['id'];

$sql = "SELECT * FROM pedido WHERE id_cliente = $idUsuario";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Papelaria</title>
    <link rel="stylesheet" href="../Assets/css/tela-admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <header>
        <div class="header">
            <img src="../Assets/img/papelaria 2.png" alt="Logo Papelaria">
        </div>
    </header>

    <div class="topo">
        <h1>Olá, 
            <?php 
                $partes = explode(" ", $_SESSION['nome']);
                $primeiroNome = $partes[0];
                $segundoNome = isset($partes[1]) ? $partes[1] : "";

                echo trim($primeiroNome . " " . $segundoNome);
            ?>!
        </h1>
        <p>Abaixo estão todos os pedidos efetuados por você.</p>
    </div>
    
    <main class="main-conteudo">
        <table border="1">
            <tr>
                <th>ID Pedido</th>
                <th>Produto</th>
                <th>Feito em</th>
                <th>Entregue em</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>

            <?php 
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_pedido'] . "</td>";
                echo "<td>" . $row['nome_produto'] . "</td>";
                echo "<td>" . $row['data_pedido'] . "</td>";
                echo "<td>" . $row['data_entrega'] . "</td>";
                echo "<td>" . $row['preco_final'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </main>

    <script src="../Assets/js/tela-home.js"></script>
</body>

</html>