<?php
/**
 * Funções auxiliares usadas pelo lado CLIENTE da loja.
 * Incluído pelo index.php (require_once) — não depende de nenhuma classe.
 */

define('BASE_PATH', dirname(__DIR__));

/** Erro de regra de negócio: a mensagem PODE ser mostrada ao cliente. */
class RegraNegocioException extends Exception {}

/** Escapa texto para HTML. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** 1234.5 -> "R$ 1.234,50" */
function moeda($valor): string
{
    return "R$\u{00A0}" . number_format((float) $valor, 2, ',', '.'); // espaço não-quebrável: "R$" nunca fica sozinho na linha
}

function somenteDigitos($valor): string
{
    return preg_replace('/\D+/', '', (string) $valor);
}

/** Monta URL no padrão do projeto: index.php?controller=X&action=Y&... */
function url(string $controller, string $action = 'index', array $params = []): string
{
    $query = array_merge(['controller' => $controller, 'action' => $action], $params);
    return 'index.php?' . http_build_query($query);
}

function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

/* ------------------------------------------------------------------ */
/* Imagem do produto: convenção public/uploads/produtos/{id}.{ext}     */
/* ------------------------------------------------------------------ */
function imagemProduto(int $produtoId): string
{
    foreach (['jpg', 'png', 'webp'] as $ext) {
        $relativo = "public/uploads/produtos/{$produtoId}.{$ext}";
        if (is_file(BASE_PATH . '/' . $relativo)) {
            return $relativo;
        }
    }
    return 'public/assets/img/placeholder.svg';
}

/* CPF VALIDA CPF REAL!                                                            

function validarCpf(string $cpf): bool
{
    $cpf = somenteDigitos($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int) $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ((int) $cpf[$t] !== $digito) {
            return false;
        }
    }
    return true;
}
 */
function formatarCpf(string $cpf): string
{
    $cpf = somenteDigitos($cpf);
    if (strlen($cpf) !== 11) {
        return $cpf;
    }
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

function formatarCep(string $cep): string
{
    $cep = somenteDigitos($cep);
    return strlen($cep) === 8 ? substr($cep, 0, 5) . '-' . substr($cep, 5) : $cep;
}

function formatarTelefone(?string $telefone): string
{
    $t = somenteDigitos((string) $telefone);
    if (strlen($t) === 11) {
        return '(' . substr($t, 0, 2) . ') ' . substr($t, 2, 5) . '-' . substr($t, 7);
    }
    if (strlen($t) === 10) {
        return '(' . substr($t, 0, 2) . ') ' . substr($t, 2, 4) . '-' . substr($t, 6);
    }
    return (string) $telefone;
}

/* ------------------------------------------------------------------ */
/* Mensagens rápidas (flash)                                           */
/* ------------------------------------------------------------------ */
function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'msg' => $mensagem];
}

function pegarFlashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/* ------------------------------------------------------------------ */
/* CSRF: todo formulário POST leva um token da sessão                  */
/* ------------------------------------------------------------------ */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCampo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">';
}

function csrfValido(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return is_string($enviado) && $enviado !== '' && hash_equals(csrfToken(), $enviado);
}

/** Só aceita redirecionamento interno (evita open redirect). */
function destinoInternoSeguro(?string $destino, string $padrao): string
{
    if ($destino && strpos($destino, 'index.php?') === 0) {
        return $destino;
    }
    return $padrao;
}
