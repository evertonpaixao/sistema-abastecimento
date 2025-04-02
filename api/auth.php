<?php
header('Content-Type: application/json'); // Define o tipo de resposta JSON

$motoristas = [
    'motorista1' => 'senha123',
    'motorista2' => 'senha456',
];

$nome = $_POST['nome'] ?? '';
$senha = $_POST['senha'] ?? '';

if (isset($motoristas[$nome]) && $motoristas[$nome] === $senha) {
    echo json_encode(['success' => true, 'user' => $nome]);
} else {
    echo json_encode(['success' => false]);
}
?>
