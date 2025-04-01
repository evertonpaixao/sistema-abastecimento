<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $planilha = "../planilhas/" . date('Y-m', strtotime($data)) . ".csv";
    
    if (file_exists($planilha)) {
        // Lê os dados do arquivo CSV
        $dados = file($planilha);

        // Cabeçalho do CSV
        $cabecalho = [
            'Data/Hora', 'Motorista', 'KM Inicial', 'KM Final', 'KM Dia', 
            'Litros', 'Combustivel', 'Placa', 'Valor'
        ];

        // Inicializa as variáveis de totalização
        $total_km_dia = 0;
        $total_litros = 0;
        $total_valor = 0;

        // Abre a saída para o navegador
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dados_abastecimento_' . date('Y-m', strtotime($data)) . '.csv"');

        // Abre o output para escrever o CSV
        $output = fopen('php://output', 'w');

        // Adiciona uma linha especial para o Excel ajustar as colunas
        fwrite($output, "sep=,\n"); // Define o delimitador como vírgula
        fwrite($output, "AutoAjuste\n"); // Instrução para o Excel ajustar as colunas

        // Escreve o cabeçalho no arquivo CSV
        fputcsv($output, $cabecalho, ',', '"');

        // Percorre as linhas e escreve no CSV
        foreach ($dados as $linha) {
            $colunas = explode(',', trim($linha));
            $linha_data = explode(' ', $colunas[0])[0]; // Extrai a data da coluna Data/Hora
            
            if ($linha_data === $data) {
                // Trata a coluna da foto, se necessário (exemplo: transformação de caminho relativo em URL completa, etc.)
                $colunas[9] = !empty($colunas[9]) ? '' : ''; // Verifica se existe um valor para a foto

                // Escreve a linha no CSV
                fputcsv($output, $colunas, ',', '"');

                // Soma os totais
                $total_km_dia += (float) $colunas[4]; // KM Dia (índice 4)
                $total_litros += (float) $colunas[5]; // Litros (índice 5)
                $total_valor += (float) $colunas[8];  // Valor (índice 8)
            }
        }

        // Adiciona a linha com os totais
        $totais = [
            '', '', '', '', number_format($total_km_dia, 2, ',', '.'), 
            number_format($total_litros, 2, ',', '.'), '', '', 
            number_format($total_valor, 2, ',', '.'), ''
        ];

        // Escreve a linha de totais no CSV
        fputcsv($output, $totais, ',', '"');

        fclose($output);
    } else {
        echo 'Nenhum dado encontrado para a data selecionada.';
    }
} else {
    echo 'Data não fornecida.';
}
?>
