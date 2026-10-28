<?php
require_once __DIR__ . '/../models/Relatorio.php';
require_once __DIR__ . '/../models/Estoque.php';
 
class RelatorioController
{
    public function index(): void
    {
        $this->check();
 
        $relatorioModel = new Relatorio();
        $estoqueModel   = new Estoque();
 
        $topProdutos    = $relatorioModel->topProdutosMaisVendidos();
        $fornecedores   = $relatorioModel->fornecedoresComMaisEntregas();
        $entradas       = $relatorioModel->entradasRecentes();
        $baixoEstoque   = $estoqueModel->listarBaixoEstoque();
 
        require_once __DIR__ . '/../views/relatorios.php';
    }
 
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    public function exportarExcel(): void
{
    $this->check();
 
    $relatorioModel = new Relatorio();
    $topProdutos = $relatorioModel->topProdutosMaisVendidos(50);
 
    // Cabeçalhos HTTP que forçam o download como planilha
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="produtos_mais_vendidos.csv"');
 
    // BOM (Byte Order Mark) — evita que o Excel exiba acentos quebrados
    echo "\xEF\xBB\xBF";
 
    $saida = fopen('php://output', 'w');
 
    // Cabeçalho da planilha
    fputcsv($saida, ['Produto', 'Quantidade Vendida'], ';');
 
    foreach ($topProdutos as $linha) {
        fputcsv($saida, [$linha['nome'], $linha['total_vendido']], ';');
    }
 
    fclose($saida);
    exit;
}
public function exportarPdf(): void
{
    $this->check();
 
    require_once __DIR__ . '/../vendor/autoload.php';
 
    $relatorioModel = new Relatorio();
    $topProdutos  = $relatorioModel->topProdutosMaisVendidos(10);
    $fornecedores = $relatorioModel->fornecedoresComMaisEntregas(10);
 
    // Monta o HTML do relatório (a mesma lógica de uma view comum)
    ob_start();
    require_once __DIR__ . '/../views/relatorio_pdf.php';
    $html = ob_get_clean();
 
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
 
    $dompdf->stream('relatorio_loja_cosplay.pdf', ['Attachment' => true]);
    exit;
}

}
