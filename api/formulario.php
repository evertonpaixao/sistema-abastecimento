<?php
session_start();
if (!isset($_COOKIE['admin_logado']) || $_COOKIE['admin_logado'] !== 'true') {
    header('Location: admin/login_admin.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Abastecimento 2</title>
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
        
        .form-step {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .form-step.active {
            display: block;
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

        .button-group.first {
            justify-content: end;
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
        
        .btn-next {
            background-color: #3498db;
            color: white;
        }
        
        .btn-next:hover {
            background-color: #2980b9;
        }
        
        .btn-prev {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-prev:hover {
            background-color: #7f8c8d;
        }
        
        .btn-submit {
            background-color: #2ecc71;
            color: white;
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: #27ae60;
        }
        
        .progress-bar {
            height: 6px;
            background-color: #ecf0f1;
            border-radius: 3px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress {
            height: 100%;
            background-color: #3498db;
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .step-indicator {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .input-with-prefix {
            position: relative;
        }
        
        .input-prefix {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-weight: bold;
        }
        
        .input-with-prefix input {
            padding-left: 40px !important;
        }
        
        /* Estilos do Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            padding: 25px;
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #7f8c8d;
            transition: color 0.3s ease;
        }
        
        .modal-close:hover {
            color: #e74c3c;
        }
        
        .modal-content {
            color: #34495e;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }
        
        .modal-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .modal-btn-ok {
            background-color: #3498db;
            color: white;
        }
        
        .modal-btn-ok:hover {
            background-color: #2980b9;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }
            
            .form-title {
                font-size: 20px;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .modal-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">

        <header>
            <img src="images/logo-abastecimento.png" alt="" width="100%"/>
        </header>

        <h1 class="form-title">Registro de Abastecimento</h1>
        <!-- <?php echo "Bem-vindo, " . htmlspecialchars($_COOKIE['motorista_logado']) . "!"; ?> -->
        
        <div class="progress-bar">
            <div class="progress" id="progress"></div>
        </div>
        
        <div class="step-indicator" id="step-indicator">Passo 1 de 4</div>
        
        <form action="processar_formulario.php" method="post" enctype="multipart/form-data" id="multi-step-form">
            <!-- Passo 1 -->
            <div class="form-step active" id="step-1">
                <div class="form-group">
                    <label for="km_inicial">Quilometragem Inicial</label>
                    <input type="number" name="km_inicial" id="km_inicial" required placeholder="Digite a km inicial">
                </div>
                
                <div class="form-group">
                    <label for="km_final">Quilometragem Final</label>
                    <input type="number" name="km_final" id="km_final" required placeholder="Digite a km final">
                </div>
                
                <div class="button-group first">
                    <button type="button" class="btn btn-next" onclick="nextStep(1)">Próximo</button>
                </div>
            </div>
            
            <!-- Passo 2 -->
            <div class="form-step" id="step-2">
                <div class="form-group">
                    <label for="litros">Litros Abastecidos</label>
                    <input type="number" step="0.01" name="litros" id="litros" required placeholder="Digite os litros">
                </div>
                
                <div class="form-group">
                    <label for="combustivel">Tipo de Combustível</label>
                    <select name="combustivel" id="combustivel" required>
                        <option value="" disabled selected>Selecione o combustível</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Gasolina">Gasolina</option>
                        <option value="Etanol">Etanol</option>
                    </select>
                </div>
                
                <div class="button-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(2)">Voltar</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(2)">Próximo</button>
                </div>
            </div>
            
            <!-- Passo 3 -->
            <div class="form-step" id="step-3">
                <div class="form-group">
                    <label for="placa">Placa do Veículo</label>
                    <input type="text" name="placa" id="placa" required placeholder="XXX-XXXX" maxlength="8">
                </div>
                
                <div class="form-group input-with-prefix">
                    <label for="valor">Valor Abastecido</label>
                    <span class="input-prefix">R$</span>
                    <input type="text" class="moeda" name="valor" id="valor" required placeholder="0,00" inputmode="numeric">
                    <input type="hidden" name="valor_limpo" id="valor_limpo">        
                </div>
                
                <div class="button-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(3)">Voltar</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(3)">Próximo</button>
                </div>
            </div>
            
            <!-- Passo 4 -->
            <div class="form-step" id="step-4">
                <div class="form-group">
                    <label for="foto">Foto do Painel</label>
                    <input type="file" name="foto" id="foto" accept="image/*" required>
                </div>
                
                <div class="button-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(4)">Voltar</button>
                    <button type="submit" class="btn btn-submit">Enviar Formulário</button>
                </div>
            </div>
        </form>

        <footer>
            <img src="images/logo-santoamaro.png" alt="" width="100%"/>
        </footer>

    </div>

    <!-- Modal para mensagens -->
    <div class="modal-overlay" id="modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Atenção</h3>
                <button class="modal-close" id="modal-close">&times;</button>
            </div>
            <div class="modal-content" id="modal-content">
                Mensagem de exemplo
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-ok" id="modal-ok">OK</button>
            </div>
        </div>
    </div>

    <script>
        const totalSteps = 4;
        let currentStep = 1;
        
        // Elementos do modal
        const modal = document.getElementById('modal');
        const modalContent = document.getElementById('modal-content');
        const modalClose = document.getElementById('modal-close');
        const modalOk = document.getElementById('modal-ok');
        
        // Função para mostrar o modal
        function showModal(message) {
            modalContent.textContent = message;
            modal.classList.add('active');
        }
        
        // Fechar o modal
        function closeModal() {
            modal.classList.remove('active');
        }
        
        // Event listeners para fechar o modal
        modalClose.addEventListener('click', closeModal);
        modalOk.addEventListener('click', closeModal);
        
        // Fechar ao clicar fora do modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progress').style.width = `${progress}%`;
            document.getElementById('step-indicator').textContent = `Passo ${currentStep} de ${totalSteps}`;
        }
        
        function nextStep(step) {
            if (validateStep(step)) {
                document.getElementById(`step-${step}`).classList.remove('active');
                currentStep = step + 1;
                document.getElementById(`step-${currentStep}`).classList.add('active');
                updateProgress();
            }
        }
        
        function prevStep(step) {
            document.getElementById(`step-${step}`).classList.remove('active');
            currentStep = step - 1;
            document.getElementById(`step-${currentStep}`).classList.add('active');
            updateProgress();
        }
        
        function validateStep(step) {
            let isValid = true;
            let errorMessage = '';
            
            if (step === 1) {
                const kmInicial = document.getElementById('km_inicial').value;
                const kmFinal = document.getElementById('km_final').value;
                
                if (!kmInicial || !kmFinal) {
                    errorMessage = 'Por favor, preencha ambos os campos de quilometragem';
                    isValid = false;
                } else if (parseInt(kmFinal) <= parseInt(kmInicial)) {
                    errorMessage = 'A quilometragem final deve ser maior que a inicial';
                    isValid = false;
                }
            } else if (step === 2) {
                const litros = document.getElementById('litros').value;
                const combustivel = document.getElementById('combustivel').value;
                
                if (!litros || !combustivel) {
                    errorMessage = 'Por favor, preencha todos os campos';
                    isValid = false;
                } else if (parseFloat(litros) <= 0) {
                    errorMessage = 'Os litros abastecidos devem ser maiores que zero';
                    isValid = false;
                }
            } else if (step === 3) {
                const placa = document.getElementById('placa').value;
                const valor = document.getElementById('valor').value;
                
                if (!placa || !valor) {
                    errorMessage = 'Por favor, preencha todos os campos';
                    isValid = false;
                } else if (!validarPlaca(placa)) {
                    errorMessage = 'Por favor, insira uma placa válida no formato XXX-XXXX';
                    isValid = false;
                } else if (parseFloat(formatarValorParaNumero(valor)) <= 0) {
                    errorMessage = 'O valor abastecido deve ser maior que zero';
                    isValid = false;
                }
            }
            
            if (!isValid) {
                showModal(errorMessage);
            }
            
            return isValid;
        }
        
        // Máscara para placa do veículo (XXX-XXXX)
        document.getElementById('placa').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            if (value.length > 3) {
                value = value.substring(0, 3) + '-' + value.substring(3, 7);
            }
            
            e.target.value = value;
        });
        
        // Validação da placa
        function validarPlaca(placa) {
            const regex = /^[A-Z]{3}-[A-Z0-9]{4}$/;
            return regex.test(placa);
        }
        
        function formatarMoedaReal(valor) {
            // Remove tudo que não for número
            valor = valor.replace(/\D/g, ""); 

            if (valor === "") return "R$ 0,00"; 

            let numero = (parseInt(valor) / 100).toFixed(2); // Sempre mantém 2 casas decimais

            let numeroFormatado = numero.replace(".", ","); // Troca ponto decimal por vírgula

            // Adiciona os pontos de milhares corretamente
            let partes = numeroFormatado.split(",");
            partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Atualiza o input hidden com valor limpo para PHP
            document.getElementById('valor_limpo').value = numero;

            return "R$ " + partes.join(",");
        }

        document.querySelectorAll(".moeda").forEach(function (campo) {
            campo.addEventListener("input", function (e) {
                let valorFormatado = formatarMoedaReal(e.target.value);
                e.target.value = valorFormatado;
            });
        });



        
        // Converte o valor formatado para número
        function formatarValorParaNumero(valorFormatado) {
            return parseFloat(valorFormatado.replace(/\./g, '').replace(',', '.'));
        }
        
        // Formata o valor antes de enviar o formulário
        document.getElementById('multi-step-form').addEventListener('submit', function(e) {
            const valorInput = document.getElementById('valor');
            valorInput.value = formatarValorParaNumero(valorInput.value);
        });
        
        // Inicializa a barra de progresso
        updateProgress();
    </script>
</body>
</html>