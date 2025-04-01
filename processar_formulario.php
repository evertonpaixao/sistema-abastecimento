<?php
session_start();
if (!isset($_SESSION['nome'])) {
    header('Location: index.php');
    exit();
}

$nome = $_SESSION['nome'];
$km_inicial = $_POST['km_inicial'];
$km_final = $_POST['km_final'];
$litros = $_POST['litros'];
$combustivel = strtolower($_POST['combustivel']); // Converte para minúsculas
$placa = $_POST['placa'];
$valor = isset($_POST['valor_limpo']) ? (float)str_replace(',', '.', $_POST['valor_limpo']) : 0.0;

// Garantir que sempre tenha 2 casas decimais ao salvar ou exibir
$valor_formatado = number_format($valor, 2, '.', '');

$foto = $_FILES['foto'];

// Calcular km do dia
$km_dia = $km_final - $km_inicial;

// Salvar foto
$foto_nome = "motoristas/fotos/" . $nome . "_" . time() . ".jpg";
move_uploaded_file($foto['tmp_name'], $foto_nome);

// Verifica se a pasta de planilhas existe, se não, cria
if (!is_dir('planilhas')) {
    mkdir('planilhas', 0777, true);
}
if (!is_dir('planilhas/litros_mensais')) {
    mkdir('planilhas/litros_mensais', 0777, true);
}

// Salvar dados na planilha geral de abastecimentos
date_default_timezone_set('America/Sao_Paulo');
$data_hora = date('Y-m-d H:i:s');
$data_hora_brasil = date('d/m/Y H:i:s', strtotime($data_hora));

$dados_planilha = "$data_hora,$nome,$km_inicial,$km_final,$km_dia,$litros,$combustivel,$placa,$valor,$foto_nome\n";
file_put_contents("planilhas/" . date('Y-m') . ".csv", $dados_planilha, FILE_APPEND);

// Definir os arquivos CSV para cada tipo de combustível
$planilha_etanol = "planilhas/litros_mensais/" . date('Y-m') . "_etanol.csv";
$planilha_diesel = "planilhas/litros_mensais/" . date('Y-m') . "_diesel.csv";
$planilha_gasolina = "planilhas/litros_mensais/" . date('Y-m') . "_gasolina.csv";

// Seleciona a planilha correta
switch ($combustivel) {
    case 'etanol':
        $planilha_combustivel = $planilha_etanol;
        break;
    case 'diesel':
        $planilha_combustivel = $planilha_diesel;
        break;
    case 'gasolina':
        $planilha_combustivel = $planilha_gasolina;
        break;
    default:
        die("Erro: Tipo de combustível inválido.");
}

// Obtém o dia do mês
$dia = date('d', strtotime($data_hora));

// Inicializa as variáveis
$litros_por_dia = [];
$total_por_dia = array_fill(0, 31, 0.00);

// Verifica se a planilha já existe
if (file_exists($planilha_combustivel)) {
    $dados_litros = file($planilha_combustivel);
    $cabecalho = array_shift($dados_litros);

    foreach ($dados_litros as $linha) {
        $colunas = explode(',', trim($linha));
        $placa_atual = array_shift($colunas);

        if ($placa_atual === "Total por Dia") {
            continue;
        }

        $litros_por_dia[$placa_atual] = array_map('floatval', $colunas);

        foreach ($colunas as $index => $valor) {
            if ($index < 31) {
                $total_por_dia[$index] += (float)$valor;
            }
        }
    }
}

// Se a placa não existir, inicializa
if (!isset($litros_por_dia[$placa])) {
    $litros_por_dia[$placa] = array_fill(0, 31, 0.00);
}

// Soma os litros ao dia correspondente
$litros_por_dia[$placa][$dia - 1] += $litros;

// Atualiza o total do dia
$total_por_dia[$dia - 1] += $litros;

// Cria cabeçalho
$cabecalho = array_merge(['Placa'], array_map(fn($d) => "Dia $d", range(1, 31)));

$conteudo = implode(',', $cabecalho) . "\n";

foreach ($litros_por_dia as $placa_atual => $litros_dias) {
    $conteudo .= implode(',', array_merge([$placa_atual], array_map(fn($v) => number_format($v, 2, '.', ''), $litros_dias))) . "\n";
}

$conteudo .= implode(',', array_merge(["Total por Dia"], array_map(fn($v) => number_format($v, 2, '.', ''), $total_por_dia))) . "\n";

// Salva os dados na planilha correta
if (file_put_contents($planilha_combustivel, $conteudo) === false) {
    die('Erro ao salvar a planilha de combustível.');
}

echo 'Dados registrados com sucesso!';
?>
