<?php
session_start();
$tipoUsuario = $_SESSION['tipo'] ?? 'cliente';
?>
<script>
    const tipoUsuario = "<?php echo $tipoUsuario; ?>";
    console.log("tipoUsuario:", tipoUsuario);
</script>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Papelaria</title>

    <link rel="stylesheet" href="../assets/css/tela-produtos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<header>
    <div class="header">
        <img src="../assets/img/papelaria 2.png" alt="Logo Papelaria" class="logo">
    </div>
</header>

<div class="topo">
    <h1>Produtos</h1>
    <p>Encontre o item perfeito para você!</p>

    <div class="busca-container">
        <input type="text" id="campoBusca" placeholder="Buscar produto...">
        <i class="bi bi-search"></i>
    </div>

    <!-- Botão cadastrar só aparece para admin -->
    <button id="btnCadastrar" class="btn-success" style="display:none;">
        <i class="bi bi-plus-circle"></i> Cadastrar Produto
    </button>

    <!-- Botão carrinho (cliente) -->
    <button id="btnCarrinho" class="btn-success" style="display:none;">
        <i class="bi bi-cart"></i> Carrinho
    </button>
</div>

<main class="produtos-container" id="listaProdutos">
    <!-- Produtos carregados via JS -->
</main>

<!--Modal cadastrar produto -->
<div id="modalCadastro" class="modal">
  <div class="modal-content">
      <span class="fechar" id="fecharModal">&times;</span>
      <h2 id="tituloModal">Cadastrar Produto</h2>

      <form id="formCadastro" enctype="multipart/form-data">
          <div class="input-icon">
              <i class="bi bi-card-text"></i>
              <input type="text" name="nome" placeholder="Nome do produto" required>
          </div>

          <div class="input-icon">
              <i class="bi bi-journal-text"></i>
              <textarea name="descricao" placeholder="Descrição do produto" required></textarea>
          </div>

          <div class="input-icon">
              <i class="bi bi-currency-dollar"></i>
              <input type="number" step="0.01" name="preco" placeholder="Preço (R$)" required>
          </div>

          <div class="input-icon">
              <i class="bi bi-box-seam"></i>
              <input type="number" name="estoque" placeholder="Quantidade em estoque" required>
          </div>

          <div class="input-icon">
              <i class="bi bi-image"></i>
              <input type="file" name="imagem" accept="image/*">
          </div>

          <button type="submit" class="btnSalvar">Salvar</button>
      </form>
  </div>
</div>

<!--Modal editar produto -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span id="fecharModalEditar" class="fechar">&times;</span>
        <h2 id="tituloModalEditar">Editar Produto</h2>

        <form id="formEditar" enctype="multipart/form-data">

            <input type="hidden" name="id_produto">

            <div class="input-icon">
                <i class="bi bi-card-text"></i>
                <input type="text" name="nome" placeholder="Nome do produto" required>
            </div>

            <div class="input-icon">
                <i class="bi bi-journal-text"></i>
                <textarea name="descricao" placeholder="Descrição do produto" required></textarea>
            </div>

            <div class="input-icon">
                <i class="bi bi-currency-dollar"></i>
                <input type="number" step="0.01" name="preco" placeholder="Preço (R$)" required>
            </div>

            <div class="input-icon">
                <i class="bi bi-box-seam"></i>
                <input type="number" name="estoque" placeholder="Quantidade em estoque">
            </div>

            <div class="input-icon">
                <i class="bi bi-image"></i>
                <input type="file" name="imagem" accept="image/*">
            </div>

            <button type="submit" class="btnSalvar btnEditarSalvar">Salvar Alterações</button>
            <a href="../Assets/php/excluir_produtos"></a>
        </form>
    </div>
</div>


<!--Modal carrinho -->
<div id="modalCarrinho" class="modal">
  <div class="modal-content">
      <span class="fechar" id="fecharModalCarrinho">&times;</span>
      <h2>Carrinho</h2>
      <ul id="listaCarrinho"></ul>
      <p>Total: R$ <span id="totalCarrinho">0.00</span></p>
      <button id="btnFinalizar" class="btnSalvar">Finalizar Compra</button>
  </div>
</div>

<!-- Modal Finalizar Pedido -->
<div id="modalFinalizar" class="modal">
  <div class="modal-content">
      <span class="fechar" id="fecharModalFinalizar">&times;</span>
      <h2>Finalizar Pedido</h2>

      <form id="formFinalizarPedido">

          <h3>Endereço</h3>

          <div class="input-icon">
              <input type="text" name="cep" placeholder="CEP" required>
          </div>

          <div class="input-icon">
              <input type="text" name="rua" placeholder="Rua" required>
          </div>

          <div class="input-icon">
              <input type="text" name="bairro" placeholder="Bairro" required>
          </div>

          <div class="input-icon">
              <input type="text" name="cidade" placeholder="Cidade" required>
          </div>

          <div class="input-icon">
              <input type="text" name="estado" placeholder="Estado" required>
          </div>

          <div class="input-icon">
              <input type="text" name="complemento" placeholder="Complemento">
          </div>

          <h3>Pagamento</h3>

          <select name="metodo_pagamento" required>
              <option value="">Selecione</option>
              <option value="Crédito">Crédito</option>
              <option value="Débito">Débito</option>
              <option value="Pix">Pix</option>
          </select>

          <div class="input-icon">
              <input type="text" name="numero_cartao" placeholder="Número do cartão">
          </div>

          <input type="hidden" name="carrinho_json" id="carrinhoJsonInput">

          <button class="btnSalvar" type="submit">Confirmar Pedido</button>

      </form>
  </div>
</div>


<!-- alert de produto add -->
<div id="alertaCarrinho" class="alerta-carrinho">
    <i class="bi bi-check-circle"></i> Produto adicionado ao carrinho!
</div>

<script>
    const tipoUsuario = '<?php echo $tipoUsuario; ?>';
</script>
<script src="../assets/js/tela-produtos.js"></script>

</body>
</html>
