<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: tela-login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Papelaria</title>
    <link rel="stylesheet" href="../Assets/css/tela-home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <header>
        <div class="header">
            <img src="../Assets/img/papelaria 2.png" alt="Logo Papelaria">
        </div>
    </header>

    <div class="topo">
    <h1>Bem-vindo de volta, 
        <?php 
            $partes = explode(" ", $_SESSION['nome']);
            $primeiroNome = $partes[0];
            $segundoNome = isset($partes[1]) ? $partes[1] : "";
            echo trim($primeiroNome . " " . $segundoNome);
        ?>!
    </h1>
    <p>Abaixo estão todos os pedidos atribuídos a você.</p>
</div>

    <main class="main-conteudo">
        <div class="botao-container">
            <a href="tela-pedidos-entregador.php" class="botao-escolha"><i class="bi bi-box-seam"></i></a>
            <p>Pedidos</p>
        </div>
    </main>

    <script src="../Assets/js/tela-home.js"></script>
</body>

</html>