<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$mes = date('Y-m'); // Mês atual
$planilha_abastecimentos = "../planilhas/abastecimentos/$mes.csv";
$planilha_litros = "../planilhas/litros_mensais/$mes.csv";

// Verifica se a pasta de litros mensais existe, se não, cria
if (!is_dir('../planilhas/litros_mensais')) {
    mkdir('../planilhas/litros_mensais', 0777, true);
}

if (file_exists($planilha_abastecimentos)) {
    // Lê os dados da planilha de abastecimentos
    $dados = file($planilha_abastecimentos);

    // Inicializa a estrutura da planilha de litros
    $litros_por_dia = [];
    $placas = [];

    // Processa os dados
    foreach ($dados as $linha) {
        $colunas = explode(',', trim($linha));
        $data_hora = $colunas[0];
        $placa = $colunas[7]; // Placa do veículo
        $litros = (float) $colunas[5]; // Litros abastecidos

        // Extrai o dia da data/hora
        $dia = date('d', strtotime($data_hora));

        // Organiza os litros por placa e por dia
        if (!isset($litros_por_dia[$placa])) {
            $litros_por_dia[$placa] = [];
            $placas[] = $placa;
        }
        $litros_por_dia[$placa][$dia] = ($litros_por_dia[$placa][$dia] ?? 0) + $litros;
    }

    // Gera a planilha de litros
    $cabecalho = ['Placa'];
    for ($dia = 1; $dia <= 31; $dia++) {
        $cabecalho[] = "Dia $dia";
    }
    $cabecalho[] = 'Total';

    $linhas = [];
    foreach ($placas as $placa) {
        $linha = [$placa];
        $total_placa = 0;
        for ($dia = 1; $dia <= 31; $dia++) {
            $litros = $litros_por_dia[$placa][$dia] ?? 0;
            $linha[] = number_format($litros, 2, ',', '.');
            $total_placa += $litros;
        }
        $linha[] = number_format($total_placa, 2, ',', '.');
        $linhas[] = $linha;
    }

    // Adiciona a linha de totais por dia
    $total_geral = 0;
    $linha_total = ['Total'];
    for ($dia = 1; $dia <= 31; $dia++) {
        $total_dia = 0;
        foreach ($placas as $placa) {
            $total_dia += $litros_por_dia[$placa][$dia] ?? 0;
        }
        $linha_total[] = number_format($total_dia, 2, ',', '.');
        $total_geral += $total_dia;
    }
    $linha_total[] = number_format($total_geral, 2, ',', '.');
    $linhas[] = $linha_total;

    // Salva a planilha de litros
    $conteudo = implode(',', $cabecalho) . "\n";
    foreach ($linhas as $linha) {
        $conteudo .= implode(',', $linha) . "\n";
    }
    file_put_contents($planilha_litros, $conteudo);

    echo 'Planilha de litros gerada com sucesso!';
} else {
    echo 'Nenhum dado de abastecimento encontrado para este mês.';
}
?>