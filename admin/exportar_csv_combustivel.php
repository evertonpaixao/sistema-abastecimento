<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

// Verifica se foi fornecida a data ou o mês/ano para filtrar
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    $planilha = "../planilhas/" . date('Y-m', strtotime($data)) . ".csv";
    
    if (file_exists($planilha)) {
        // Lê os dados do arquivo CSV
        $dados = file($planilha);

        // Cabeçalho do CSV
        $cabecalho = [
            'Data/Hora', 'Motorista', 'KM Inicial', 'KM Final', 'KM Dia', 
            'Litros', 'Combustivel', 'Placa', 'Valor', 'Foto'
        ];

        // Inicializa as variáveis de totalização
        /* $total_km_dia = 0;
        $total_litros = 0;
        $total_valor = 0; */

        // Abre a saída para o navegador
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dados_abastecimento_' . date('Y-m', strtotime($data)) . '.csv"');

        // Abre o output para escrever o CSV
        $output = fopen('php://output', 'w');

        // Adiciona uma linha especial para o Excel ajustar as colunas
        fwrite($output, "sep=,\n"); // Define o delimitador como vírgula
        fwrite($output, "AutoAjuste\n"); // Instrução para o Excel ajustar as colunas

        // Adiciona o título do combustível no CSV
        fwrite($output, "$tipo_combustivel\n");

        // Escreve o cabeçalho no arquivo CSV
        fputcsv($output, $cabecalho, ',', '"');

        // Percorre as linhas e escreve no CSV
        foreach ($dados as $linha) {
            $colunas = explode(',', trim($linha));
            $linha_data = explode(' ', $colunas[0])[0]; // Extrai a data da coluna Data/Hora
            
            if ($linha_data === $data) {
                // Verifica e trata a coluna de foto
                $colunas[9] = !empty($colunas[9]) ? '' : ''; // Substitui o valor da foto, se necessário

                // Escreve a linha no CSV
                fputcsv($output, $colunas, ',', '"');

                // Soma os totais
                //$total_km_dia += (float) $colunas[4]; // KM Dia (índice 4)
                //$total_litros += (float) $colunas[5]; // Litros (índice 5)
                //$total_valor += (float) $colunas[8];  // Valor (índice 8)
            }
        }

        // Adiciona a linha com os totais
        /* $totais = [
            '', '', '', '', number_format($total_km_dia, 2, ',', '.'), 
            number_format($total_litros, 2, ',', '.'), '', '', 
            number_format($total_valor, 2, ',', '.'), ''
        ]; */

        // Escreve a linha de totais no CSV
        //fputcsv($output, $totais, ',', '"');

        fclose($output);
    } else {
        echo 'Nenhum dado encontrado para a data selecionada.';
    }
} elseif (isset($_GET['mes_ano'])) {
    $mes_ano_filtro = $_GET['mes_ano']; // Recebe o filtro de mês/ano

    // Diretório das planilhas de combustíveis
    $dir_planilhas = "../planilhas/litros_mensais/";

    // Definir os arquivos CSV para cada tipo de combustível com o filtro do mês/ano
    $planilha_etanol = $dir_planilhas . $mes_ano_filtro . "_etanol.csv";
    $planilha_diesel = $dir_planilhas . $mes_ano_filtro . "_diesel.csv";
    $planilha_gasolina = $dir_planilhas . $mes_ano_filtro . "_gasolina.csv";

    // Função para exportar os dados de abastecimento de cada combustível
    function exportarCSV($planilha, $tipo_combustivel) {
        // Verifica se o arquivo existe
        if (!file_exists($planilha)) {
            die("Erro: Arquivo $planilha não encontrado.");
        }

        // Lê o arquivo CSV
        $dados = file($planilha);

        // Se o arquivo tem dados
        if (count($dados) > 1) {
            // Abrir o output para o navegador
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="dados_abastecimento_' . $tipo_combustivel . '.csv"');

            // Abrir o arquivo para escrever os dados
            $output = fopen('php://output', 'w');

            // Definir delimitador para Excel (vírgula)
            fwrite($output, "sep=,\n");
            fwrite($output, "AutoAjuste\n");

            // Adiciona o título do combustível no CSV
            fwrite($output, "$tipo_combustivel\n");

            // Exibir cabeçalho do CSV
            $cabecalho = explode(',', trim($dados[0]));
            fputcsv($output, $cabecalho, ',', '"'); // Cabeçalho do CSV

            // Inicializa variáveis de totalização
            /* $total_km_dia = 0;
            $total_litros = 0;
            $total_valor = 0; */

            // Exibir os dados do arquivo CSV
            foreach ($dados as $index => $linha) {
                if ($index == 0) continue; // Pula o cabeçalho

                $colunas = explode(',', trim($linha));

                // Calcular totais
                //$total_km_dia += (float) $colunas[4]; // KM Dia
                //$total_litros += (float) $colunas[5]; // Litros
                //$total_valor += (float) $colunas[8];  // Valor

                // Escrever os dados no CSV
                fputcsv($output, $colunas, ',', '"');
            }

            // Adicionar linha de totais
            /* $totais = [
                '', '', '', '', number_format($total_km_dia, 2, ',', '.'),
                number_format($total_litros, 2, ',', '.'), '', '',
                number_format($total_valor, 2, ',', '.'), ''
            ]; */

            // Escrever a linha de totais no CSV
            //fputcsv($output, $totais, ',', '"');

            // Fechar a escrita no arquivo
            fclose($output);
        } else {
            echo "<p>Nenhum dado disponível para $tipo_combustivel.</p>";
        }
    }

    // Exportar os dados de etanol, diesel e gasolina
    exportarCSV($planilha_etanol, "Etanol");
    exportarCSV($planilha_diesel, "Diesel");
    exportarCSV($planilha_gasolina, "Gasolina");
    
} else {
    echo 'Data ou Mês/Ano não fornecido.';
}
?>
