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
$combustivel = $_POST['combustivel'];
$placa = $_POST['placa'];
$valor = $_POST['valor'];
$foto = $_FILES['foto'];

// Calcular km do dia
$km_dia = $km_final - $km_inicial;

// Salvar foto
$foto_nome = "motoristas/fotos/" . $nome . "_" . time() . ".jpg"; // Corrigido aqui
move_uploaded_file($foto['tmp_name'], $foto_nome);

// Salvar dados na planilha do admin
$data_hora = date('Y-m-d H:i:s');
$dados_planilha = "$data_hora,$nome,$km_inicial,$km_final,$km_dia,$litros,$combustivel,$placa,$valor,$foto_nome\n";
file_put_contents("planilhas/" . date('Y-m') . ".csv", $dados_planilha, FILE_APPEND);

echo 'Dados enviados com sucesso!';
?>