<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login do Motorista Combustivel</h1>
    <?php
if (isset($_GET['erro'])) {
    echo "<script>alert('Login inválido! Tente novamente.');</script>";
}
?>
    <form action="auth.php" method="post">
        Nome: <input type="text" name="nome" required><br>
        Senha: <input type="password" name="senha" required><br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>