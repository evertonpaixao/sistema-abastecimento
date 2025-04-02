<?php
// Inicia a sessão (não será usada na Vercel)
session_start();

// Credenciais do admin (substitua por um método mais seguro em produção)
$admin_usuario = 'motorista1';
$admin_senha = 'admin123';

// Obtém os dados do formulário
$usuario = $_POST['nome'];
$senha = $_POST['senha'];

if ($usuario === $admin_usuario && $senha === $admin_senha) {
    // Criar o cookie
    setcookie('motorista_logado', $usuario, time() + 3600, '/', '', false, true);

    // Redireciona com um parâmetro para indicar sucesso
    header('Location: formulario.php?login=sucesso');
    exit();
} else {
    header('Location: index.php?erro=1');
    exit();
}
?>
