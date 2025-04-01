<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$linha = urldecode($_GET['linha']);
$colunas = explode(',', $linha);
$placa_excluida = $colunas[7]; // Assume que a placa está na 8ª coluna (índice 7)

// Lê a planilha de abastecimentos
$planilha_abastecimentos = "../planilhas/" . date('Y-m') . ".csv";
$dados = file($planilha_abastecimentos);

// Remove a linha correspondente
foreach ($dados as $index => $dado) {
    if (trim($dado) === trim($linha)) {
        unset($dados[$index]);
        break;
    }
}

// Salva os dados atualizados na planilha de abastecimentos
file_put_contents($planilha_abastecimentos, implode('', $dados));

// Atualiza a planilha de litros abastecidos por dia
$planilha_litros = "../planilhas/litros_mensais/" . date('Y-m') . ".csv";
$litros_por_dia = [];
$total_por_dia = array_fill(0, 31, 0.00);

if (file_exists($planilha_litros)) {
    // Lê os dados existentes
    $dados_litros = file($planilha_litros);
    $cabecalho = array_shift($dados_litros); // Remove o cabeçalho

    // Processa os dados existentes e exclui os valores da placa removida
    foreach ($dados_litros as $linha) {
        $colunas = explode(',', trim($linha));
        $placa_atual = array_shift($colunas);

        if ($placa_atual === "Total por Dia") {
            continue; // Ignora a linha "Total por Dia"
        }

        // Exclui os dados da placa removida
        if ($placa_atual === $placa_excluida) {
            continue;
        }

        // Recalcula os totais por dia
        $litros_por_dia[$placa_atual] = array_map('floatval', $colunas);
        foreach ($colunas as $index => $valor) {
            if ($index < 31) {
                $total_por_dia[$index] += (float)$valor;
            }
        }
    }
}

// Reconstrói a planilha de litros abastecidos por dia
$cabecalho = array_merge(
    ['Placa'],
    array_map(fn($d) => "Dia $d", range(1, 31))
);

$conteudo = implode(',', $cabecalho) . "\n";

foreach ($litros_por_dia as $placa_atual => $litros_dias) {
    $conteudo .= implode(',', array_merge([$placa_atual], array_map(fn($v) => number_format($v, 2, '.', ''), $litros_dias))) . "\n";
}

// Adiciona a linha "Total por Dia"
$conteudo .= implode(',', array_merge(["Total por Dia"], array_map(fn($v) => number_format($v, 2, '.', ''), $total_por_dia))) . "\n";

// Salva a planilha de litros abastecidos por dia atualizada
file_put_contents($planilha_litros, $conteudo);

// Atualiza a planilha de totais de litros por placa
$planilha_totais = "../planilhas/litros_totais/" . date('Y-m') . "_totais.csv";
$novos_totais = [];

if (file_exists($planilha_totais)) {
    $dados_totais = file($planilha_totais);

    foreach ($dados_totais as $linha) {
        $colunas = explode(',', trim($linha));
        $placa_atual = $colunas[0];

        if ($placa_atual === $placa_excluida) {
            continue; // Exclui a placa removida
        }

        // Atualiza os totais de litros para as placas restantes
        $novos_totais[] = implode(',', $colunas);
    }
}

// Recalcula o total de litros para cada placa restante e gera a nova linha de totais
$conteudo_totais = "Placa,Total Litros\n";

foreach ($litros_por_dia as $placa_atual => $litros_dias) {
    $total_litros = array_sum($litros_dias);
    $conteudo_totais .= "{$placa_atual}," . number_format($total_litros, 2, '.', '') . "\n";
}

// Salva a planilha de totais de litros atualizada
file_put_contents($planilha_totais, $conteudo_totais);

// Redireciona para a página de dashboard
header('Location: dashboard.php');
exit();
?>
