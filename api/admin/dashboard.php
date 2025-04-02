<?php
session_start();
if (!isset($_COOKIE['admin_logado']) || $_COOKIE['admin_logado'] !== 'true') {
    header('Location: login_admin.php');
    exit();
}

$planilha = "../planilhas/" . date('Y-m') . ".csv";
$planilha_litros = "../planilhas/litros_mensais/" . date('Y-m') . ".csv";
$planilha_totais = "../planilhas/litros_totais/" . date('Y-m') . "_totais.csv"; // Caminho para a planilha de totais

// Verifica se o filtro de mês/ano foi enviado
$mes_ano_filtro = isset($_GET['mes_ano']) ? $_GET['mes_ano'] : date('Y-m');
$planilha_litros_filtrada = "../planilhas/litros_mensais/" . $mes_ano_filtro . ".csv";
$planilha_totais_filtrada = "../planilhas/litros_totais/" . $mes_ano_filtro . "_totais.csv"; // Planilha filtrada por mês/ano
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard do Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .table-litros-por-placa tr:nth-child(2) {
            display: none;
        }
        #box-abastecimento-geral {
            background:#f1f1f1;
        }
        #box-abastecimento-mes {
            background:#e1e1e1;
        }
        #box-combustivel {
            background:#e1e1e1;
        }
    </style>
</head>
<body>
    <h1>Dashboard do Admin</h1>
    <a href="logout.php">Logout</a>

    <form action="dashboard.php" method="get">
        Filtrar por data: <input type="date" name="data" required>
        <button type="submit">Filtrar</button>
    </form>
    
    <div id="box-abastecimento-mes">
        <h2>Dados por mês</h2>
        <?php 
            if (isset($_GET['data'])) {
                $data = $_GET['data'];
                $planilha = "../planilhas/" . date('Y-m', strtotime($data)) . ".csv";
                
                if (file_exists($planilha)) {
                    $dados = file($planilha);

                    // Inicializa as variáveis de totalização
                    $total_km_dia = 0;
                    $total_litros = 0;
                    $total_valor = 0;

                    echo '<h3>Dados do dia ' . date('d/m/Y', strtotime($data)) . '</h3>';
                    echo '<table border="1">';
                    echo '<tr><th>Data/Hora</th><th>Motorista</th><th>KM Inicial</th><th>KM Final</th><th>KM Dia</th><th>Litros</th><th>Combustível</th><th>Placa</th><th>Valor</th><th>Foto</th><th>Ações</th></tr>';
                    
                    foreach ($dados as $linha) {
                        $colunas = explode(',', trim($linha));
                        $linha_data = explode(' ', $colunas[0])[0]; // Extrai a data da coluna Data/Hora
                        
                        if ($linha_data === $data) {
                            echo '<tr>';
                            foreach ($colunas as $index => $valor) {
                                if ($index === 9) { // Coluna da foto
                                    echo '<td><a href="../' . $valor . '" target="_blank">Ver Foto</a></td>';
                                } else {
                                    //echo '<td>' . $valor . '</td>';
                                    if ($index === 0) { // Supondo que a data está na primeira coluna
                                        echo '<td>' . date('d/m/Y H:i:s', strtotime($valor)) . '</td>';
                                    } else {
                                        echo '<td>' . $valor . '</td>';
                                    }
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
                    echo '<p>Nenhum dado encontrado para esta data.</p>';
                }
            }
        ?>

        <a href="exportar_csv_dia.php?data=<?= urlencode($data) ?>">
            <button>Exportar CSV Dia</button>
        </a>

    </div>

    <div id="box-abastecimento-geral">
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
                        //echo '<td>' . $valor . '</td>';
                        if ($index === 0) { // Supondo que a data está na primeira coluna
                            echo '<td>' . date('d/m/Y H:i:s', strtotime($valor)) . '</td>';
                        } else {
                            echo '<td>' . $valor . '</td>';
                        }
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

        <a href="exportar_csv.php">Exportar para CSV</a>

    </div>

    <?php
        // Definir os arquivos CSV para cada tipo de combustível
        $planilha_etanol = "../planilhas/litros_mensais/" . date('Y-m') . "_etanol.csv";
        $planilha_diesel = "../planilhas/litros_mensais/" . date('Y-m') . "_diesel.csv";
        $planilha_gasolina = "../planilhas/litros_mensais/" . date('Y-m') . "_gasolina.csv";

        // Diretório das planilhas
        $dir_planilhas = "../planilhas/litros_mensais/";

        //$planilha_etanol = "../planilhas/litros_mensais/" . date('Y-m') . "_etanol.csv";

        // Planilhas para cada combustível
        $planilha_etanol = $dir_planilhas . $mes_ano_filtro . "_etanol.csv";
        $planilha_diesel = $dir_planilhas . $mes_ano_filtro . "_diesel.csv";
        $planilha_gasolina = $dir_planilhas . $mes_ano_filtro . "_gasolina.csv";

        // Criar arquivos CSV caso não existam
        foreach ([$planilha_etanol, $planilha_diesel, $planilha_gasolina] as $arquivo) {
            if (!file_exists($arquivo)) {
                file_put_contents($arquivo, "Data,Motorista,KM Inicial,KM Final,KM Dia,Litros,Combustível,Placa,Valor\n");
            }
        }

        // Função para exibir os dados de litros abastecidos por combustível
        function exibirDadosLitros($planilha, $tipo_combustivel) {
            echo "<h3>Litros Abastecidos - $tipo_combustivel</h3>";

            if (file_exists($planilha)) {
                $dados = file($planilha);

                if (count($dados) > 1) {
                    echo '<table border="1">';
                    
                    // Exibir cabeçalho corretamente: Placa e Dias do Mês
                    $cabecalho = explode(',', trim($dados[0]));
                    echo '<tr>';
                    foreach ($cabecalho as $titulo) {
                        echo "<th>$titulo</th>";
                    }
                    echo '</tr>';

                    // Exibir os dados de abastecimento
                    foreach ($dados as $index => $linha) {
                        if ($index == 0) continue; // Pular cabeçalho
                        
                        echo '<tr>';
                        $colunas = explode(',', trim($linha));
                        
                        foreach ($colunas as $valor) {
                            echo "<td>$valor</td>";
                        }
                        
                        echo '</tr>';
                    }

                    echo '</table>';
                } else {
                    echo "<p>Nenhum dado disponível para $tipo_combustivel.</p>";
                }
            } else {
                echo "<p>Arquivo de abastecimento para $tipo_combustivel não encontrado.</p>";
            }
        }

    ?>

    <div id="box-combustivel">

        <h1>Dashboard de Abastecimento</h1>

        <form action="dashboard.php" method="get">
            Filtrar por mês/ano:
            <input type="month" name="mes_ano" value="<?= $mes_ano_filtro ?>" required>
            <button type="submit">Filtrar</button>
        </form>
        
        <?php
            exibirDadosLitros($planilha_etanol, "Etanol");
            exibirDadosLitros($planilha_diesel, "Diesel");
            exibirDadosLitros($planilha_gasolina, "Gasolina");
        ?>

        <a href="exportar_csv_combustivel.php?mes_ano=<?= urlencode($mes_ano_filtro) ?>">
            <button>Exportar CSV - Geral - por Mês/Ano</button>
        </a>

        <!-- <a href="exportar_csv_combustivel.php?mes_ano=<?= urlencode($mes_ano_filtro) ?>&tipo_combustivel=etanol">
            <button>Exportar CSV - Etanol</button>
        </a>

        <a href="exportar_csv_combustivel.php?mes_ano=<?= urlencode($mes_ano_filtro) ?>&tipo_combustivel=diesel">
            <button>Exportar CSV - Diesel</button>
        </a>

        <a href="exportar_csv_combustivel.php?mes_ano=<?= urlencode($mes_ano_filtro) ?>&tipo_combustivel=gasolina">
            <button>Exportar CSV - Gasolina</button>
        </a> -->

    </div>



    <div style="display:none">
    <h2>Litros Abastecidos por Dia</h2>

    <form action="dashboard.php" method="get">
        Filtrar por mês/ano: 
        <input type="month" name="mes_ano" value="<?= $mes_ano_filtro ?>" required>
        <button type="submit">Filtrar</button>
    </form>

    <?php
    if (file_exists($planilha_litros_filtrada)) {
        $dados_litros = file($planilha_litros_filtrada);
        echo '<table border="1">';
        foreach ($dados_litros as $linha) {
            echo '<tr>';
            $colunas = explode(',', trim($linha));
            foreach ($colunas as $valor) {
                //echo '<td>' . $valor . '</td>';
                if ($index === 0) { // Supondo que a data está na primeira coluna
                    echo '<td>' . date('d/m/Y H:i:s', strtotime($valor)) . '</td>';
                } else {
                    echo '<td>' . $valor . '</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Nenhum dado de litros encontrado para o mês/ano selecionado.</p>';
    }
    ?>
    </div>

    <!-- Filtro de Mês/Ano para Totais de Litros por Placa -->
    <h2>Totais de Litros por Placa</h2>
    <form action="dashboard.php" method="get">
        Filtrar por mês/ano:
        <input type="month" name="mes_ano" value="<?= $mes_ano_filtro ?>" required>
        <button type="submit">Filtrar</button>
    </form>

    <?php
    // Diretório das planilhas
    $dir_planilhas = "../planilhas/litros_mensais/";

    //$planilha_etanol = "../planilhas/litros_mensais/" . date('Y-m') . "_etanol.csv";

    // Planilhas para cada combustível
    $planilha_etanol = $dir_planilhas . $mes_ano_filtro . "_etanol.csv";
    $planilha_diesel = $dir_planilhas . $mes_ano_filtro . "_diesel.csv";
    $planilha_gasolina = $dir_planilhas . $mes_ano_filtro . "_gasolina.csv";

    // Função para exibir os totais por combustível
    function exibirTotaisPorCombustivel($planilha, $tipo_combustivel) {
        if (file_exists($planilha)) {
            $dados_totais = file($planilha);
            echo "<h3>Total de Litros - $tipo_combustivel</h3>";
            echo '<table class="table-litros-por-placa" border="1">';
            echo '<tr><th>Placa</th><th>Total Litros</th></tr>';
            
            // Processa os dados
            foreach ($dados_totais as $linha) {
                $colunas = explode(',', trim($linha));

                // Ignorar linha de "Total por Dia"
                if ($colunas[0] === "Total por Dia") {
                    continue;
                }

                // Calcula o total de litros por placa somando os valores dos dias
                $placa = array_shift($colunas);
                $total_litros = array_sum(array_map('floatval', $colunas));

                echo "<tr><td>$placa</td><td>" . number_format($total_litros, 2, '.', '') . "</td></tr>";
            }
            echo '</table>';
        } else {
            echo "<p>Nenhum dado encontrado para $tipo_combustivel no mês/ano selecionado.</p>";
        }
    }

    // Exibir totais para cada combustível
    exibirTotaisPorCombustivel($planilha_etanol, "Etanol");
    exibirTotaisPorCombustivel($planilha_diesel, "Diesel");
    exibirTotaisPorCombustivel($planilha_gasolina, "Gasolina");

    // Depuração: Verificar se os arquivos existem antes de tentar ler
/* echo "<p>Verificando arquivos...</p>";
echo "<p>Etanol: " . (file_exists($planilha_etanol) ? "Encontrado ✅" : "Não encontrado ❌") . "</p>";
echo "<p>Diesel: " . (file_exists($planilha_diesel) ? "Encontrado ✅" : "Não encontrado ❌") . "</p>";
echo "<p>Gasolina: " . (file_exists($planilha_gasolina) ? "Encontrado ✅" : "Não encontrado ❌") . "</p>"; */

    ?>

    <a href="exportar_litros_por_placas.php?mes_ano=<?= urlencode($mes_ano_filtro) ?>">
        <button>Exportar CSV Litros por Placa</button>
    </a>

</body>
</html>
