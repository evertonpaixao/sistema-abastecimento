<?php
session_start();

// Credenciais do admin (substitua por um método mais seguro em produção)
$admin_usuario = 'admin';
$admin_senha = 'admin123';

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

if ($usuario === $admin_usuario && $senha === $admin_senha) {
    $_SESSION['admin_logado'] = true;
    header('Location: dashboard.php');
} else {
    echo 'Login inválido. <a href="login_admin.php">Tente novamente</a>';
}
?>