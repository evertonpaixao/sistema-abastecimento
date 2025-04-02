<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background-color: #d9d6c8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        header {
            background:#d9d6c8;
        }
        footer {
            margin-top:80px
        }

        img {
            max-width:200px;
            margin: 0 auto;
            display:table;
        }

        .text-center {
            text-align:center;
        }
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }
        
        .form-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .form-group input::placeholder {
            color: #bbb;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus::placeholder {
            transform: translateY(-20px);
            font-size: 12px;
            opacity: 0.7;
        }
        
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit {
            background-color: #3498db;
            color: white;
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: #1b43b3;
        }
    </style>
</head>
<body>
    
    <?php
    if (isset($_GET['erro'])) {
        echo "<script>alert('Login inválido! Tente novamente.');</script>";
    }
    ?>

    <div class="form-container">

        <header>
            <img src="images/logo-abastecimento.png" alt="" width="100%"/>
        </header>

        <h1 class="form-title">Login do Motorista</h1>

        <form id="loginForm">
            <div class="form-group">
                <label>Nome:</label> 
                <input type="text" name="nome" required><br>
            </div>

            <div class="form-group">
                <label>Senha:</label> 
                <input type="password" name="senha" required><br>
            </div>

            <div class="button-group">
                <button class="btn btn-submit" type="submit">Entrar</button>
            </div>
        </form>

        <footer>
            <img src="images/logo-santoamaro.png" alt="" width="100%"/>
        </footer>

    </div>

    <script>
    document.getElementById("loginForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Evita que o formulário recarregue a página

        let formData = new FormData(this);

        fetch("auth.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem("motorista_logado", data.user);
                window.location.href = "formulario.php";
            } else {
                alert("Login inválido!");
            }
        })
        .catch(error => console.error("Erro na requisição:", error));
    });
    </script>


</body>
</html>