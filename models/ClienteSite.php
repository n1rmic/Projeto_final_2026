<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Cliente do site — tabela cliente_site (CPF é a chave primária).
 * Independente da tabela `cliente` do PDV e da tabela `usuario` do vendedor.
 */
class ClienteSite
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM cliente_site WHERE cpf = :cpf");
        $stmt->execute([':cpf' => $cpf]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM cliente_site WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /** E-mail já usado por OUTRO cliente? ($cpfAtual = ignora o próprio cliente na edição) */
    public function emailEmUso(string $email, ?string $cpfAtual = null): bool
    {
        $stmt = $this->db->prepare("SELECT cpf FROM cliente_site WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $cpf = $stmt->fetchColumn();
        return $cpf !== false && $cpf !== $cpfAtual;
    }

    /**
     * @param array $d cpf, nome, email, senha, endereco, telefone
     * @throws RegraNegocioException se CPF ou e-mail já existem
     */
    public function criar(array $d): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO cliente_site (cpf, nome, email, senha_hash, endereco, telefone)
             VALUES (:cpf, :nome, :email, :senha_hash, :endereco, :telefone)"
        );
        try {
            $stmt->execute([
                ':cpf'        => $d['cpf'],
                ':nome'       => $d['nome'],
                ':email'      => $d['email'],
                ':senha_hash' => password_hash($d['senha'], PASSWORD_DEFAULT),
                ':endereco'   => $d['endereco'],
                ':telefone'   => $d['telefone'] !== '' ? $d['telefone'] : null,
            ]);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') { // violação de PK/UNIQUE (corrida entre dois cadastros)
                throw new RegraNegocioException('Já existe um cadastro com esse CPF ou e-mail.');
            }
            throw $ex;
        }
    }

    public function atualizar(string $cpf, array $d): void
    {
        $stmt = $this->db->prepare(
            "UPDATE cliente_site
             SET nome = :nome, email = :email, endereco = :endereco, telefone = :telefone
             WHERE cpf = :cpf"
        );
        try {
            $stmt->execute([
                ':nome'     => $d['nome'],
                ':email'    => $d['email'],
                ':endereco' => $d['endereco'],
                ':telefone' => $d['telefone'] !== '' ? $d['telefone'] : null,
                ':cpf'      => $cpf,
            ]);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                throw new RegraNegocioException('Esse e-mail já está em uso por outro cadastro.');
            }
            throw $ex;
        }
    }

    public function atualizarSenha(string $cpf, string $novaSenha): void
    {
        $stmt = $this->db->prepare("UPDATE cliente_site SET senha_hash = :h WHERE cpf = :cpf");
        $stmt->execute([':h' => password_hash($novaSenha, PASSWORD_DEFAULT), ':cpf' => $cpf]);
    }

    /** Login por e-mail + senha. Retorna o cliente ou null. */
    public function autenticar(string $email, string $senha): ?array
    {
        $cliente = $this->buscarPorEmail($email);

        // Sempre roda password_verify (mesmo sem cliente) para o tempo de resposta não revelar se o e-mail existe.
        $hash = $cliente['senha_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuAbCdEfGhIjKlMnOpQrStUvWxYzAbCdE';
        $ok = password_verify($senha, $hash);

        if ($cliente && $ok) {
            if (password_needs_rehash($cliente['senha_hash'], PASSWORD_DEFAULT)) {
                $this->atualizarSenha($cliente['cpf'], $senha);
            }
            return $cliente;
        }
        return null;
    }


    /** Hard delete — só deve ser chamado quando o cliente NÃO tiver pedidos. */
public function excluir(string $cpf): void
{
    $stmt = $this->db->prepare("DELETE FROM cliente_site WHERE cpf = :cpf");
    $stmt->execute([':cpf' => $cpf]);
}

/** Anonimiza os dados pessoais, mantendo a linha (necessário para preservar o
 *  histórico em `pedido`, que tem FK para cliente_site.cpf sem ON DELETE CASCADE). */
public function anonimizar(string $cpf): void
{
    $stmt = $this->db->prepare(
        "UPDATE cliente_site
         SET nome = 'Conta excluída',
             email = CONCAT('excluido+', cpf, '@removido.local'),
             senha_hash = :hashInvalido,
             endereco = '—',
             telefone = NULL
         WHERE cpf = :cpf"
    );
    // hash que nunca vai bater em password_verify(), impedindo login futuro
    $stmt->execute([':hashInvalido' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), ':cpf' => $cpf]);
}
}
