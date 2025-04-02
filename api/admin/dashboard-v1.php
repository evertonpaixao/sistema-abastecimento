<?php
session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$planilha = "../planilhas/" . date('Y-m') . ".csv";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard do Admin</title>
</head>
<body>
    <h1>Dashboard do Admin</h1>
    <a href="logout.php">Logout</a>

    <form action="dashboard.php" method="get">
        Filtrar por data: <input type="date" name="data" required>
        <button type="submit">Filtrar</button>
    </form>

    <a href="exportar_csv.php">Exportar para CSV</a>

    <?php
    if (isset($_GET['data'])) {
        $data = $_GET['data'];
        $planilha = "../planilhas/" . date('Y-m', strtotime($data)) . ".csv";
        if (file_exists($planilha)) {
            $dados = file($planilha);
            echo '<h3>Dados do dia ' . $data . '</h3>';
            echo '<table border="1">';
            echo '<tr><th>Data/Hora</th><th>Motorista</th><th>KM Inicial</th><th>KM Final</th><th>KM Dia</th><th>Litros</th><th>Combustível</th><th>Placa</th><th>Valor</th><th>Foto</th><th>Ações</th></tr>';
            foreach ($dados as $linha) {
                $colunas = explode(',', $linha);
                $linha_data = explode(' ', $colunas[0])[0]; // Extrai a data da coluna Data/Hora
                if ($linha_data === $data) {
                    echo '<tr>';
                    foreach ($colunas as $index => $valor) {
                        if ($index === 9) { // Coluna da foto
                            echo '<td><a href="../' . $valor . '" target="_blank">Ver Foto</a></td>';
                        } else {
                            echo '<td>' . $valor . '</td>';
                        }
                    }
                    // Adiciona botões de editar e excluir
                    echo '<td>
                            <a href="editar.php?linha=' . urlencode($linha) . '">Editar</a> |
                            <a href="excluir.php?linha=' . urlencode($linha) . '" onclick="return confirm(\'Tem certeza que deseja excluir este registro?\')">Excluir</a>
                          </td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        } else {
            echo '<p>Nenhum dado encontrado para esta data.</p>';
        }
    }
    ?>

    <h2>Dados de Abastecimento</h2>
    <?php
    if (file_exists($planilha)) {
        // Lê todas as linhas do arquivo CSV
        $dados = file($planilha);

        // Ordena os dados pela coluna de Data/Hora (ordem decrescente)
        usort($dados, function($a, $b) {
            $coluna_a = explode(',', $a)[0]; // Pega a Data/Hora da linha A
            $coluna_b = explode(',', $b)[0]; // Pega a Data/Hora da linha B
            return strtotime($coluna_b) - strtotime($coluna_a); // Ordena do mais recente para o mais antigo
        });

        // Inicializa as variáveis para os totais
        $total_km_dia = 0;
        $total_litros = 0;
        $total_valor = 0;

        // Exibe a tabela
        echo '<table border="1">';
        echo '<tr><th>Data/Hora</th><th>Motorista</th><th>KM Inicial</th><th>KM Final</th><th>KM Dia</th><th>Litros</th><th>Combustível</th><th>Placa</th><th>Valor</th><th>Foto</th><th>Ações</th></tr>';
        foreach ($dados as $linha) {
            $colunas = explode(',', trim($linha));
            echo '<tr>';
            foreach ($colunas as $index => $valor) {
                if ($index === 9) { // Coluna da foto
                    echo '<td><a href="../' . $valor . '" target="_blank">Ver Foto</a></td>';
                } else {
                    echo '<td>' . $valor . '</td>';
                }
            }
            // Adiciona botões de editar e excluir
            echo '<td>
                    <a href="editar.php?linha=' . urlencode($linha) . '">Editar</a> |
                    <a href="excluir.php?linha=' . urlencode($linha) . '" onclick="return confirm(\'Tem certeza que deseja excluir este registro?\')">Excluir</a>
                  </td>';
            echo '</tr>';

            // Soma os totais
            $total_km_dia += (float) $colunas[4]; // KM Dia (índice 4)
            $total_litros += (float) $colunas[5]; // Litros (índice 5)
            $total_valor += (float) $colunas[8];  // Valor (índice 8)
        }

        // Exibe a linha de totais
        echo '<tr style="font-weight: bold;">';
        echo '<td colspan="4">Total</td>';
        echo '<td>' . number_format($total_km_dia, 2, ',', '.') . '</td>'; // KM Dia
        echo '<td>' . number_format($total_litros, 2, ',', '.') . '</td>'; // Litros
        echo '<td colspan="2"></td>'; // Colunas vazias
        echo '<td>' . number_format($total_valor, 2, ',', '.') . '</td>'; // Valor
        echo '<td></td>'; // Coluna da foto vazia
        echo '<td></td>'; // Coluna de ações vazia
        echo '</tr>';

        echo '</table>';
    } else {
        echo '<p>Nenhum dado encontrado.</p>';
    }
    ?>
</body>
</html>

no dashboard precisa criar uma aba, que liste uma planilha do mês com todos os dados da coluna litros, o valor desses dados precisam ser resgatado da outra planilha. 