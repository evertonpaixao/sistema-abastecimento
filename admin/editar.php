<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$linha = urldecode($_GET['linha']);
$colunas = explode(',', $linha);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtemos os dados do formulário
    $km_inicial = $_POST['km_inicial'];
    $km_final = $_POST['km_final'];
    $litros = $_POST['litros'];
    $combustivel = $_POST['combustivel'];
    $placa = $_POST['placa'];
    $valor = $_POST['valor'];
    $foto = $_POST['foto'];
    $data_hora = $_POST['data_hora'];
    $nome = $_POST['nome'];

    // Cálculo do KM Dia
    $km_dia = $km_final - $km_inicial;  // Cálculo do KM do dia

    // Atualiza os dados da linha com o cálculo do KM Dia
    $novos_dados = [
        $data_hora,
        $nome,
        $km_inicial,
        $km_final,
        $km_dia,  // Agora o "KM Dia" é calculado
        $litros,
        $combustivel,
        $placa,
        $valor,
        $foto
    ];

    // Lê o arquivo CSV da planilha de abastecimentos
    $planilha_abastecimentos = "../planilhas/" . date('Y-m') . ".csv";
    $dados = file($planilha_abastecimentos);

    // Substitui a linha antiga pela nova
    foreach ($dados as $index => $dado) {
        if (trim($dado) === trim($linha)) {
            $dados[$index] = implode(',', $novos_dados) . "\n";
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

        // Processa os dados existentes
        foreach ($dados_litros as $linha) {
            $colunas = explode(',', trim($linha));
            $placa_atual = array_shift($colunas);

            if ($placa_atual === "Total por Dia") {
                continue; // Ignora a linha "Total por Dia"
            }

            // Atualiza o total de cada dia
            $litros_por_dia[$placa_atual] = array_map('floatval', $colunas);
            foreach ($colunas as $index => $valor) {
                if ($index < 31) {
                    $total_por_dia[$index] += (float)$valor;
                }
            }
        }
    }

    // Atualiza os litros para a placa atual
    if (!isset($litros_por_dia[$placa])) {
        $litros_por_dia[$placa] = array_fill(0, 31, 0.00);
    }

    // Atualiza a linha com a placa
    $dia = date('d', strtotime($data_hora));
    $litros_por_dia[$placa][$dia - 1] = $litros;

    // Atualiza o total do dia
    $total_por_dia[$dia - 1] += $litros;

    // Adiciona os cabeçalhos dos dias
    $cabecalho = array_merge(
        ['Placa'],
        array_map(fn($d) => "Dia $d", range(1, 31))
    );

    // Reconstrói o conteúdo da planilha de litros abastecidos por dia
    $conteudo = implode(',', $cabecalho) . "\n";

    foreach ($litros_por_dia as $placa_atual => $litros_dias) {
        $conteudo .= implode(',', array_merge([$placa_atual], array_map(fn($v) => number_format($v, 2, '.', ''), $litros_dias))) . "\n";
    }

    // Adiciona a linha "Total por Dia" corretamente
    $conteudo .= implode(',', array_merge(["Total por Dia"], array_map(fn($v) => number_format($v, 2, '.', ''), $total_por_dia))) . "\n";

    // Salva a planilha de litros abastecidos por dia atualizada
    file_put_contents($planilha_litros, $conteudo);

    // Atualiza a planilha de totais de litros por placa
    $planilha_totais = "../planilhas/litros_totais/" . date('Y-m') . "_totais.csv";
    $conteudo_totais = "Placa,Total Litros\n";

    // Atualiza o total de litros por placa
    foreach ($litros_por_dia as $placa_atual => $litros_dias) {
        $total_litros = array_sum($litros_dias);
        $conteudo_totais .= "{$placa_atual}," . number_format($total_litros, 2, '.', '') . "\n";
    }

    // Salva a planilha de totais de litros atualizada
    file_put_contents($planilha_totais, $conteudo_totais);

    // Redireciona para o dashboard
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
</head>
<body>
    <h1>Editar Registro</h1>
    <form method="POST">
        Data/Hora: <input type="text" name="data_hora" value="<?= $colunas[0] ?>" required><br>
        Motorista: <input type="text" name="nome" value="<?= $colunas[1] ?>" required><br>
        KM Inicial: <input type="number" name="km_inicial" value="<?= $colunas[2] ?>" required><br>
        KM Final: <input type="number" name="km_final" value="<?= $colunas[3] ?>" required><br>
        KM Dia: <input type="number" name="km_dia" value="<?= $colunas[4] ?>" disabled><br> <!-- KM Dia agora é calculado automaticamente -->
        Litros: <input type="number" step="0.01" name="litros" value="<?= $colunas[5] ?>" required><br>
        Combustível: <input type="text" name="combustivel" value="<?= $colunas[6] ?>" required><br>
        Placa: <input type="text" name="placa" value="<?= $colunas[7] ?>" required><br>
        Valor: <input type="number" step="0.01" name="valor" value="<?= $colunas[8] ?>" required><br>
        Foto: <input type="text" name="foto" value="<?= $colunas[9] ?>" required><br>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>
