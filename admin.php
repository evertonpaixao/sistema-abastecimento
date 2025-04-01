<?php
// Verificar se o admin está logado (adicionar lógica de autenticação)
$planilha = "planilhas/" . date('Y-m') . ".csv";
if (file_exists($planilha)) {
    $dados = file($planilha);
    echo '<h1>Dados de Abastecimento</h1>';
    echo '<table border="1">';
    echo '<tr><th>Data/Hora</th><th>Motorista</th><th>KM Inicial</th><th>KM Final</th><th>KM Dia</th><th>Litros</th><th>Combustível</th><th>Placa</th><th>Valor</th></tr>';
    foreach ($dados as $linha) {
        echo '<tr><td>' . str_replace(',', '</td><td>', $linha) . '</td></tr>';
    }
    echo '</table>';
} else {
    echo 'Nenhum dado encontrado.';
}
?>