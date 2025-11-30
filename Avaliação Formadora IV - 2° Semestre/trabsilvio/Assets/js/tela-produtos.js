let produtos = [];
let carrinho = [];

/* ===========================================
   INICIALIZAÇÃO
=========================================== */
document.addEventListener("DOMContentLoaded", () => {
    console.log("tipoUsuario:", tipoUsuario);

    /* BTN CADASTRAR */
    const btnCadastrar = document.getElementById("btnCadastrar");
    if (tipoUsuario === "admin" && btnCadastrar) {
        btnCadastrar.style.display = "inline-flex";
        btnCadastrar.addEventListener("click", abrirCadastro);
    }

    /* Modal Cadastro */
    document.getElementById("fecharModal")?.addEventListener("click", fecharCadastro);
    window.addEventListener("click", (e) => {
        if (e.target === document.getElementById("modalCadastro")) fecharCadastro();
    });

    /* Modal Editar */
    document.getElementById("fecharModalEditar")?.addEventListener("click", fecharEditar);
    window.addEventListener("click", (e) => {
        if (e.target === document.getElementById("modalEditar")) fecharEditar();
    });

    /* Form Cadastro */
    const formCadastro = document.getElementById("formCadastro");
    if (formCadastro) {
        formCadastro.addEventListener("submit", salvarProduto);
        formCadastro.querySelector('input[name="imagem"]')?.addEventListener("change", previewImagem);
    }

    /* Form Editar */
    const formEditar = document.getElementById("formEditar");
    if (formEditar) {
        formEditar.addEventListener("submit", atualizarProduto);
    }

    /* Carrinho */
    const btnCarrinho = document.getElementById("btnCarrinho");
    const modalCarrinho = document.getElementById("modalCarrinho");

    if (tipoUsuario === "cliente" && btnCarrinho) {
        btnCarrinho.style.display = "inline-flex";

        btnCarrinho.addEventListener("click", () => {
            modalCarrinho.classList.add("show");
            atualizarCarrinho();
        });

        document.getElementById("fecharModalCarrinho")?.addEventListener("click", () => {
            modalCarrinho.classList.remove("show");
        });

        modalCarrinho.addEventListener("click", (e) => {
            if (e.target === modalCarrinho) modalCarrinho.classList.remove("show");
        });
    }

    /* Busca */
    const campoBusca = document.getElementById("campoBusca");
    campoBusca?.addEventListener("input", () => filtrarProdutos(campoBusca.value.trim()));

    carregarProdutos();
});

/* ===========================================
   MODAL: CADASTRAR
=========================================== */
function abrirCadastro() {
    const modal = document.getElementById("modalCadastro");
    const form = document.getElementById("formCadastro");

    form.reset();
    delete form.dataset.idproduto;

    document.getElementById("tituloModal").textContent = "Cadastrar Produto";
    modal.classList.add("show");
}

function fecharCadastro() {
    document.getElementById("modalCadastro").classList.remove("show");
}

function previewImagem(event) {
    const input = event.target;
    const preview = document.getElementById("previewImg");
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ===========================================
   MODAL: EDITAR
=========================================== */
function editarProduto(id) {
    const produto = produtos.find(p => p.id_produto == id);
    if (!produto) return;

    const form = document.getElementById("formEditar");

    form.dataset.idproduto = produto.id_produto;
    form.querySelector('input[name="nome"]').value = produto.nome_produto;
    form.querySelector('textarea[name="descricao"]').value = produto.descricao;
    form.querySelector('input[name="preco"]').value = produto.preco;
    form.querySelector('input[name="estoque"]').value = produto.estoque;

    document.getElementById("modalEditar").classList.add("show");
}

function fecharEditar() {
    document.getElementById("modalEditar").classList.remove("show");
}

/* ===========================================
   BUSCA & LISTAGEM
=========================================== */
async function carregarProdutos() {
    try {
        const resp = await fetch("../Assets/php/listar_produtos.php");
        produtos = await resp.json().catch(() => []);

        const lista = document.getElementById("listaProdutos");
        if (!lista) return;

        lista.innerHTML = produtos.length
            ? produtos.map(p => criarCard(p)).join("")
            : "<p>Nenhum produto encontrado.</p>";

    } catch (err) {
        console.error("Erro ao carregar produtos:", err);
    }
}

function filtrarProdutos(texto) {
    const lista = document.getElementById("listaProdutos");
    if (!lista) return;

    const termo = texto.toLowerCase();

    const filtrados = produtos.filter(p =>
        p.nome_produto.toLowerCase().includes(termo) ||
        p.descricao.toLowerCase().includes(termo)
    );

    lista.innerHTML = filtrados.length
        ? filtrados.map(p => criarCard(p)).join("")
        : "<p>Nenhum produto encontrado.</p>";
}

function criarCard(p) {
    const imagemPath = p.imagem ? `../Assets/img/${p.imagem}` : "../Assets/img/placeholder.png";

    let botoes = tipoUsuario === "admin"
        ? `
        <button class="btn-editar" onclick="editarProduto(${p.id_produto})"><i class="bi bi-pencil"></i> Editar</button>
        <button class="btn-excluir" onclick="excluirProduto(${p.id_produto})"><i class="bi bi-trash"></i> Excluir</button>`
        : `<button class="btn-comprar" onclick="adicionarCarrinho(${p.id_produto})"><i class="bi bi-cart-plus"></i> Adicionar</button>`;

    return `
        <div class="produto-card">
            <img src="${imagemPath}" alt="${p.nome_produto}">
            <h3>${p.nome_produto}</h3>
            <p>${p.descricao}</p>
            <span class="preco">R$ ${parseFloat(p.preco).toFixed(2)}</span>
            <div class="admin-botoes">${botoes}</div>
        </div>
    `;
}

/* ===========================================
   CRUD: SALVAR
=========================================== */
async function salvarProduto(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);

    const resp = await fetch("../Assets/php/cadastrar_produtos.php", {
        method: "POST",
        body: data
    });

    const text = await resp.text();

    if (text.trim() === "OK") {
        alert("Produto cadastrado!");
        form.reset();
        fecharCadastro();
        carregarProdutos();
    } else {
        alert("Erro: " + text);
    }
}

/* ===========================================
   CRUD: ATUALIZAR
=========================================== */
async function atualizarProduto(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    data.append("id_produto", form.dataset.idproduto);

    const resp = await fetch("../Assets/php/editar_produtos.php", {
        method: "POST",
        body: data
    });

    const text = await resp.text();

    if (text.trim() === "OK") {
        alert("Produto atualizado!");
        fecharEditar();
        carregarProdutos();
    } else {
        alert("Erro: " + text);
    }
}

/* ===========================================
   CRUD: EXCLUIR
=========================================== */
function excluirProduto(id) {
    if (!confirm("Tem certeza que deseja excluir?")) return;

    const data = new FormData();
    data.append("id", id);

    fetch("../Assets/php/excluir_produtos.php", {
        method: "POST",
        body: data
    })
        .then(resp => resp.text())
        .then(text => {
            if (text.trim() === "OK") {
                alert("Produto excluído!");
                carregarProdutos();
            } else {
                alert("Erro ao excluir: " + text);
            }
        })
        .catch(err => console.error("Erro:", err));
}

/* ===========================================
   CARRINHO
=========================================== */
function adicionarCarrinho(id) {
    const produto = produtos.find(p => p.id_produto == id);
    if (!produto) return;

    const item = carrinho.find(i => i.id_produto == id);
    if (item) {
        item.quantidade++;
    } else {
        carrinho.push({
            id_produto: produto.id_produto,
            nome_produto: produto.nome_produto,
            preco: parseFloat(produto.preco),
            quantidade: 1
        });
    }

    atualizarCarrinho();
    mostrarAlertaCarrinho();
}

function atualizarCarrinho() {
    const lista = document.getElementById("listaCarrinho");
    const totalEl = document.getElementById("totalCarrinho");
    if (!lista || !totalEl) return;

    if (carrinho.length === 0) {
        lista.innerHTML = "<li>Carrinho vazio</li>";
        totalEl.textContent = "0.00";
        return;
    }

    lista.innerHTML = "";
    let total = 0;

    carrinho.forEach(item => {
        total += item.preco * item.quantidade;
        lista.innerHTML += `
            <li>
                ${item.nome_produto} x${item.quantidade} 
                - R$ ${(item.preco * item.quantidade).toFixed(2)}
            </li>`;
    });

    totalEl.textContent = total.toFixed(2);
}

/* ===========================================
   FINALIZAR PEDIDO
=========================================== */
document.getElementById("btnFinalizar")?.addEventListener("click", () => {
    const modal = document.getElementById("modalFinalizar");
    document.getElementById("carrinhoJsonInput").value = JSON.stringify(carrinho);
    modal.classList.add("show");
});

document.getElementById("fecharModalFinalizar")?.addEventListener("click", () => {
    document.getElementById("modalFinalizar").classList.remove("show");
});

document.getElementById("formFinalizarPedido")?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const metodo = document.querySelector("select[name='metodo_pagamento']").value;
    const cartao = document.querySelector("input[name='numero_cartao']").value.trim();
    const regexCartao = /^\d{4}\s\d{4}\s\d{4}\s\d{4}$/;

    if ((metodo === "Crédito" || metodo === "Débito") && !regexCartao.test(cartao)) {
        alert("Número de cartão inválido! Use 0000 0000 0000 0000");
        return;
    }

    const formData = new FormData(e.target);

    try {
        const resposta = await fetch("../Assets/php/finalizar_pedidos.php", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        });

        const texto = (await resposta.text()).trim();
        console.log("Resposta do PHP:", texto);

        if (texto === "OK") {
            alert("Seu pedido foi realizado com sucesso!");
            carrinho = [];
            atualizarCarrinho();
            document.getElementById("modalFinalizar").classList.remove("show");
            window.location.href = "../Paginas/tela-home.php";
            return;
        }

        alert("Erro: " + texto);

    } catch (erro) {
        alert("Erro ao finalizar pedido.");
        console.error(erro);
    }
});

/* ===========================================
   ALERTA CARRINHO
=========================================== */
function mostrarAlertaCarrinho() {
    const alerta = document.getElementById("alertaCarrinho");
    if (!alerta) return;
    alerta.classList.add("show");
    setTimeout(() => alerta.classList.remove("show"), 2500);
}
