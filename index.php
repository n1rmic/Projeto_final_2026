<?php
session_start();
require_once __DIR__ . '/config/db.php';
Database::getConnection();


// Roteamento simples via GET
// (padrão continua sendo o login do vendedor; a loja abre em index.php?controller=loja&action=index)
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'form';

// Só letras e números (evita carregar arquivo de caminho arbitrário)
if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string) $controller) || !preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string) $action)) {
    die("Requisição inválida.");
}


// Carregar controller
switch ($controller) {
    case 'auth':
        require_once __DIR__ . '/controllers/AuthController.php';
        $c = new AuthController();
        break;


    // CRUD PRODUTOS E VENDAS
    case 'produto':
        require_once __DIR__ . '/controllers/ProdutoController.php';
        $c = new ProdutoController();
        break;


    case 'entrada':
        require_once __DIR__ . '/controllers/EntradaController.php';
        $c = new EntradaController();
        break;


    case 'venda':
        require_once __DIR__ . '/controllers/VendaController.php';
        $c = new VendaController();
        break;


    case 'relatorio':
        require_once __DIR__ . '/controllers/RelatorioController.php';
        $c = new RelatorioController();
        break;

    case 'usuario':
        require_once __DIR__ . '/controllers/UsuarioController.php';
        $c = new UsuarioController();
        break;

    case 'categoria':
        require_once __DIR__ . '/controllers/CategoriaController.php';
        $c = new CategoriaController();
        break;

    case 'fornecedor':
        require_once __DIR__ . '/controllers/FornecedorController.php';
        $c = new FornecedorController();
        break;

    case 'cep':
        require_once __DIR__ . '/controllers/CepController.php';
        $c = new CepController();
        break;

    case 'variacao':
        require_once __DIR__ . '/controllers/VariacaoController.php';
        $c = new VariacaoController();
        break;

    // ==================== LOJA VIRTUAL (CLIENTE) ====================
 
    case 'loja':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/LojaController.php';
        $c = new LojaController();
        break;

    case 'carrinho':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/CarrinhoController.php';
        $c = new CarrinhoController();
        break;

    case 'cliente':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/ClienteController.php';
        $c = new ClienteController();
        break;

    case 'pedido':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $c = new PedidoController();
        break;


    // Caminho padrão caso o controller não exista
    default:
        die("Controller inválido.");
}


// Executar ação (só métodos públicos; protegidos/privados e __construct não são chamáveis pela URL)
if (strpos($action, '__') === 0 || !is_callable([$c, $action])) {
    die("Ação inválida.");
}


$c->$action();