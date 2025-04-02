<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

if (!isset($_GET['mes_ano'])) {
    echo 'Mês/Ano não fornecido.';
    exit();
}

$mes_ano_filtro = $_GET['mes_ano'];
$dir_planilhas = "../planilhas/litros_mensais/";

// Planilhas para cada combustível
$planilhas = [
    "Etanol" => $dir_planilhas . $mes_ano_filtro . "_etanol.csv",
    "Diesel" => $dir_planilhas . $mes_ano_filtro . "_diesel.csv",
    "Gasolina" => $dir_planilhas . $mes_ano_filtro . "_gasolina.csv"
];

// Nome do arquivo de exportação
$nome_arquivo = "litros_por_placa_" . $mes_ano_filtro . ".csv";

// Cabeçalhos para download do CSV
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$nome_arquivo");

$output = fopen('php://output', 'w');

// Escreve a linha inicial para o Excel ajustar as colunas
fwrite($output, "sep=,\n");

// Cabeçalho do CSV
fputcsv($output, ['Tipo de Combustível', 'Placa', 'Total Litros'], ',');

// Processar cada planilha e calcular total de litros por placa
foreach ($planilhas as $tipo_combustivel => $planilha) {
    if (file_exists($planilha)) {
        $dados = file($planilha);

        foreach ($dados as $linha) {
            $colunas = explode(',', trim($linha));

            // Ignorar cabeçalho e linhas de "Total por Dia"
            if ($colunas[0] === "Placa" || $colunas[0] === "Total por Dia") {
                continue;
            }

            // A primeira coluna é a placa, o restante são os litros abastecidos
            $placa = array_shift($colunas);
            $total_litros = array_sum(array_map('floatval', $colunas));

            // Escreve a linha no CSV
            fputcsv($output, [$tipo_combustivel, $placa, number_format($total_litros, 2, '.', '')], ',');
        }
    }
}

// Fecha o arquivo de saída
fclose($output);
?>
