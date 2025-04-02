<?php
$motoristas = [
    'motorista1' => 'senha123',
    'motorista2' => 'senha456',
];

$nome = $_POST['nome'] ?? '';
$senha = $_POST['senha'] ?? '';

if (isset($motoristas[$nome]) && $motoristas[$nome] === $senha) {

    header('Set-Cookie: motorista_logado=' . $nome . '; Path=/; Secure; HttpOnly; SameSite=None');

    // Criar o cookie
    setcookie('motorista_logado', $nome, time() + 3600, '/', '', true, true);

    // Redireciona com um parâmetro para indicar sucesso
    header('Location: formulario.php?login=sucesso');
    exit();
} else {
    header('Location: index.php?erro=1');
    exit();
}
?>