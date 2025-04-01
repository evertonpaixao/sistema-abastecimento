<?php
$motoristas = [
    'motorista1' => 'senha123',
    'motorista2' => 'senha456',
];

$nome = $_POST['nome'];
$senha = $_POST['senha'];

if (isset($motoristas[$nome]) && $motoristas[$nome] === $senha) {
    session_start();
    $_SESSION['nome'] = $nome;
    header('Location: formulario.php');
} else {
    echo 'Login inválido. <a href="index.php">Tente novamente</a>';
}
?>