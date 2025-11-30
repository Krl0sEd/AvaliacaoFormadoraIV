<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: tela-login.html");
    exit();
}

// Pega o primeiro e segundo nome (fallback caso só tenha um nome)
$nomes = explode(" ", $_SESSION['nome']);
$saudacao = $nomes[0] . (isset($nomes[1]) ? " " . $nomes[1] : "");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Papelaria</title>
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
        <h1>Bem-vindo de volta, <?php echo $saudacao; ?>!</h1>
        <p>O que gostaria de ver hoje?</p>
    </div>

    <main class="main-conteudo">
        <div class="botao-container">
            <a href="tela-produtos.php" class="botao-escolha"><i class="bi bi-shop"></i></a>
            <p>Loja</p>
        </div>

        <div class="botao-container">
        <?php
            $linkPedidos = 'tela-pedidos.php'; // padrão
            if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
            $linkPedidos = 'tela-admin.php';
            }
        ?>
        <a href="<?php echo $linkPedidos; ?>" class="botao-escolha"><i class="bi bi-box-seam"></i></a>
        <p>Meus Pedidos</p>
        </div>
    </main>

    <script src="../Assets/js/tela-home.js"></script>
</body>

</html>
