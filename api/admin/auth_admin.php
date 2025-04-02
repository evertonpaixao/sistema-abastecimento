<?php
// Inicia a sessão (não será usada na Vercel)
session_start();

// Credenciais do admin (substitua por um método mais seguro em produção)
$admin_usuario = 'admin';
$admin_senha = 'admin123';

// Obtém os dados do formulário
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

// Verifica as credenciais
if ($usuario === $admin_usuario && $senha === $admin_senha) {
    // Cria um cookie válido por 1 hora
    setcookie('admin_logado', 'true', time() + 3600, '/', '', false, true);
    
    // Redireciona para o dashboard
    header('Location: dashboard.php');
    exit();
} else {
    echo 'Login inválido. <a href="login_admin.php">Tente novamente</a>';
}
?>
