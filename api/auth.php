<?php
$motoristas = [
    'motorista1' => 'senha123',
    'motorista2' => 'senha456',
];

$nome = $_POST['nome'];
$senha = $_POST['senha'];

if (isset($motoristas[$nome]) && $motoristas[$nome] === $senha) {
    // Criando um cookie válido por 1 hora
    setcookie('motorista_logado', $nome, time() + 3600, '/', '', false, true);
    
    // Redireciona para a página protegida
    header('Location: formulario.php');
    exit();
} else {
    echo 'Login inválido. <a href="index.php">Tente novamente</a>';
}
?>
