<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$planilha = "../planilhas/" . date('Y-m') . ".csv";
if (file_exists($planilha)) {
    // Abre o arquivo CSV para leitura
    $dados = file($planilha);

    // Cabeçalho do CSV
    $cabecalho = [
        'Data/Hora', 'Motorista', 'KM Inicial', 'KM Final', 'KM Dia', 
        'Litros', 'Combustivel', 'Placa', 'Valor', 'Foto', 'Total KM Dia', 'Total Litros', 'Total Valor'
    ];

    // Inicializa as variáveis para os totais
    $total_km_dia = 0;
    $total_litros = 0;
    $total_valor = 0;

    // Abre a saída para o navegador
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="dados_abastecimento_' . date('Y-m') . '.csv"');

    // Abre o output para escrever o CSV
    $output = fopen('php://output', 'w');

    // Adiciona uma linha especial para o Excel ajustar as colunas
    fwrite($output, "sep=,\n"); // Define o delimitador como vírgula
    fwrite($output, "AutoAjuste\n"); // Instrução para o Excel ajustar as colunas

    // Escreve o cabeçalho
    fputcsv($output, $cabecalho, ',', '"');

    // Escreve os dados e realiza a soma dos totais
    foreach ($dados as $linha) {
        $colunas = explode(',', trim($linha)); // Remove espaços em branco e divide as colunas

        // Soma os totais
        $total_km_dia += (float) $colunas[4]; // KM Dia (índice 4)
        $total_litros += (float) $colunas[5]; // Litros (índice 5)
        $total_valor += (float) $colunas[8];  // Valor (índice 8)

        // Escreve a linha de dados
        fputcsv($output, $colunas, ',', '"');
    }

    // Adiciona a linha com os totais
    $totais = [
        '', '', '', '', number_format($total_km_dia, 2, ',', '.'), 
        number_format($total_litros, 2, ',', '.'), '', '', 
        number_format($total_valor, 2, ',', '.'), '', '' // Campos vazios para Foto e Ações
    ];

    // Escreve a linha de totais
    fputcsv($output, $totais, ',', '"');

    fclose($output);
} else {
    echo 'Nenhum dado encontrado para exportação.';
}
?>
