<?php
session_start();

// Verifica se o entregador está logado
if (!isset($_SESSION['id'])) {
    header("Location: tela-login.html");
    exit();
}

$con = new mysqli("localhost", "root", "", "loja");

if ($con->connect_error) {
    die("Erro de conexão: " . $con->connect_error);
}

$id_entregador = $_SESSION['id'];

// 1. QUERY PARA PEDIDOS DISPONÍVEIS (Trazendo dados do endereço junto)
// O INNER JOIN serve para juntar a tabela pedido com a tabela endereco usando o ID que elas têm em comum
$sql_disponiveis = "SELECT pedido.*, endereco.rua, endereco.bairro, endereco.cidade, endereco.complemento 
                    FROM pedido 
                    INNER JOIN endereco ON pedido.id_endereco = endereco.id_endereco
                    WHERE pedido.id_entregador IS NULL 
                    ORDER BY pedido.data_pedido DESC";

$result_disponiveis = $con->query($sql_disponiveis);

// 2. QUERY PARA MEUS PEDIDOS (Trazendo dados do endereço junto)
$sql_meus = "SELECT pedido.*, endereco.rua, endereco.bairro, endereco.cidade, endereco.complemento 
             FROM pedido 
             INNER JOIN endereco ON pedido.id_endereco = endereco.id_endereco
             WHERE pedido.id_entregador = $id_entregador 
             ORDER BY pedido.data_pedido DESC";

$result_meus = $con->query($sql_meus);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega - Papelaria</title>
    <link rel="stylesheet" href="../Assets/css/tela-admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .secao-tabela { margin-bottom: 50px; }
        .titulo-secao { 
            background-color: #f4f4f4; 
            padding: 10px; 
            border-left: 5px solid #004d00;
            margin: 20px 0;
            color: #333;
        }
        .btn-aceitar {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-aceitar:hover { background-color: #218838; }
    </style>
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
            echo trim($partes[0]);
            ?>!
        </h1>
        <p>Gerencie suas entregas abaixo.</p>
    </div>

    <main class="main-conteudo">

        <div class="secao-tabela">
            <h2 class="titulo-secao"><i class="bi bi-shop"></i> Mural de Pedidos Disponíveis</h2>
            
            <?php if ($result_disponiveis->num_rows > 0): ?>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Endereço de Entrega</th> <th>Produto</th>
                        <th>Data Pedido</th>
                        <th>Ação</th>
                    </tr>
                    <?php while ($row = $result_disponiveis->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_pedido']; ?></td>
                        
                        <td>
                            <?php 
                            echo $row['rua'] . ", " . $row['bairro'] . " - " . $row['cidade']; 
                            if(!empty($row['complemento'])) {
                                echo " (" . $row['complemento'] . ")";
                            }
                            ?>
                        </td>

                        <td><?php echo $row['nome_produto']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['data_pedido'])); ?></td>
                        <td>
                            <form class="form-status">
                                <input type="hidden" name="id_pedido" value="<?php echo $row['id_pedido']; ?>">
                                <input type="hidden" name="acao" value="aceitar"> 
                                <button type="submit" class="btn-aceitar">ACEITAR ENTREGA</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Nenhum pedido disponível no momento.</p>
            <?php endif; ?>
        </div>

        <div class="secao-tabela">
            <h2 class="titulo-secao"><i class="bi bi-bicycle"></i> Minhas Entregas em Andamento</h2>

            <?php if ($result_meus->num_rows > 0): ?>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Cliente ID</th>
                        <th>Endereço de Entrega</th> <th>Valor</th>
                        <th>Status Atual</th>
                    </tr>

                    <?php while ($row = $result_meus->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_pedido']; ?></td>
                        <td><?php echo $row['nome_produto']; ?></td>
                        <td><?php echo $row['id_cliente']; ?></td>
                        
                        <td>
                            <?php 
                            echo $row['rua'] . ", " . $row['bairro'] . " - " . $row['cidade']; 
                            if(!empty($row['complemento'])) {
                                echo " (" . $row['complemento'] . ")";
                            }
                            ?>
                        </td>

                        <td>R$ <?php echo $row['preco_final']; ?></td>
                        <td>
                            <form class="form-status">
                                <input type="hidden" name="id_pedido" value="<?php echo $row['id_pedido']; ?>">
                                <input type="hidden" name="acao" value="atualizar">

                                <select name="status">
                                    <option value="Aceito pelo entregador" <?php echo ($row['status'] == 'Aceito pelo entregador' ? 'selected' : ''); ?>>Aceito</option>
                                    <option value="Em trânsito" <?php echo ($row['status'] == 'Em trânsito' ? 'selected' : ''); ?>>Em trânsito</option>
                                    <option value="A caminho" <?php echo ($row['status'] == 'A caminho' ? 'selected' : ''); ?>>A caminho</option>
                                    <option value="Entregue" <?php echo ($row['status'] == 'Entregue' ? 'selected' : ''); ?>>Entregue</option>
                                </select>

                                <button type="submit">Salvar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Você não tem entregas ativas.</p>
            <?php endif; ?>
        </div>

    </main>

    <script src="../Assets/js/tela-home.js"></script>
    <script src="../Assets/js/tela-pedidos-entregador.js"></script>

</body>
</html>