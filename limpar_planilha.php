<?php
session_start();
if (!isset($_SESSION['nome'])) {
    header('Location: index.php');
    exit();
}

$planilha_litros = "planilhas/litros_mensais/" . date('Y-m') . ".csv";

// Verifica se o arquivo existe
if (file_exists($planilha_litros)) {
    // Abre o arquivo em modo de escrita (isso sobrescreve o conteúdo)
    $arquivo = fopen($planilha_litros, 'w');
    if ($arquivo) {
        // Escreve o cabeçalho no arquivo (planilha vazia, apenas com o cabeçalho)
        $cabecalho = ['Placa', 'Dia 1', 'Dia 2', 'Dia 3', 'Dia 4', 'Dia 5', 'Dia 6', 'Dia 7', 'Dia 8', 'Dia 9', 'Dia 10', 'Dia 11', 'Dia 12', 'Dia 13', 'Dia 14', 'Dia 15', 'Dia 16', 'Dia 17', 'Dia 18', 'Dia 19', 'Dia 20', 'Dia 21', 'Dia 22', 'Dia 23', 'Dia 24', 'Dia 25', 'Dia 26', 'Dia 27', 'Dia 28', 'Dia 29', 'Dia 30', 'Dia 31'];
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