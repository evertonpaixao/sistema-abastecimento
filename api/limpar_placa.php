<?php
session_start();
if (!isset($_SESSION['nome'])) {
    header('Location: index.php');
    exit();
}

$planilha_totais = "planilhas/litros_totais/" . date('Y-m') . "_totais.csv";

// Verifica se o arquivo existe
if (file_exists($planilha_totais)) {
    // Abre o arquivo em modo de escrita (isso sobrescreve o conteúdo)
    $arquivo = fopen($planilha_totais, 'w');
    if ($arquivo) {
        // Escreve o cabeçalho no arquivo (planilha vazia, apenas com o cabeçalho)
        $cabecalho = array_merge(
            ['Placa']
        );
        fputcsv($arquivo, $cabecalho);
        fclose($arquivo);
        echo 'Planilha limpa com sucesso!';
    } else {
        echo 'Erro ao abrir a planilha para limpeza.';
    }
} else {
    echo 'A planilha não existe.';
}
?>

