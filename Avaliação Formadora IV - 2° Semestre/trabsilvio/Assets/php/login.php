<?php
session_start(); // Sempre antes de qualquer saída

$conn = mysqli_connect("localhost", "root", "", "loja");

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

$email = $_POST['email'];
$senha = $_POST['senha'];

// Buscar usuário pelo e-mail
$sql = "SELECT * FROM pessoa WHERE email = '$email' AND senha = '$senha'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {

    // Pega os dados do usuário
    $usuario = mysqli_fetch_assoc($result);

    // Salva na sessão
    $_SESSION['id'] = $usuario['id_pessoa'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo'] = $usuario['tipo_usuario'];

    // Redireciona de acordo com o tipo de usuário
    if ($_SESSION['tipo'] === 'admin') {
        header("Location: /trabsilvio/Paginas/tela-home.php");
    } elseif ($_SESSION['tipo'] === 'cliente') {
        header("Location: /trabsilvio/Paginas/tela-home.php");
    } elseif ($_SESSION['tipo'] === 'entregador') {
        header("Location: /trabsilvio/Paginas/tela-entregador.php");
    }

    exit();

} else {
    echo "❌ E-mail ou senha incorretos!";
}
?>
