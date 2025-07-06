// public/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const serverMessage = document.getElementById('serverMessage');
    const loginButton = document.getElementById('loginButton');

    // Função para validar um campo específico
    function validateField(inputElement, errorElement, message) {
        if (inputElement.value.trim() === '') {
            errorElement.textContent = message;
            inputElement.classList.add('error-input');
            return false;
        } else {
            errorElement.textContent = '';
            inputElement.classList.remove('error-input');
            return true;
        }
    }

    // Event listeners para validação em tempo real (on blur)
    emailInput.addEventListener('blur', () => {
        validateField(emailInput, emailError, 'O e-mail é obrigatório.');
    });

    passwordInput.addEventListener('blur', () => {
        validateField(passwordInput, passwordError, 'A senha é obrigatória.');
    });

    // Event listener para o envio do formulário
    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Impede o envio padrão do formulário

        // Limpa mensagens anteriores
        serverMessage.textContent = '';
        serverMessage.style.color = ''; // Resetar cor para padrão
        emailError.textContent = '';
        passwordError.textContent = '';
        emailInput.classList.remove('error-input');
        passwordInput.classList.remove('error-input');

        // Validação JS antes de enviar para o servidor
        const isEmailValid = validateField(emailInput, emailError, 'O e-mail é obrigatório.');
        const isPasswordValid = validateField(passwordInput, passwordError, 'A senha é obrigatória.');

        if (!isEmailValid || !isPasswordValid) {
            serverMessage.textContent = 'Por favor, corrija os erros nos campos.';
            serverMessage.style.color = 'var(--error-red)';
            return; // Impede o envio se a validação falhar
        }

        // Adiciona efeito de loading no botão
        loginButton.classList.add('loading-button');
        loginButton.disabled = true;

        const formData = new FormData(loginForm); // Coleta os dados do formulário

        try {
            const response = await fetch('../api/login.php', { // Caminho correto para o script PHP
                method: 'POST',
                body: formData
            });

            const data = await response.json(); // Assume que o PHP retorna JSON

            if (data.success) {
                serverMessage.textContent = data.message;
                serverMessage.style.color = 'green'; // Mensagem de sucesso
                // Redireciona para o dashboard após um pequeno atraso para a mensagem ser vista
                setTimeout(() => {
                    window.location.href = 'dashboard.html'; // Redireciona para o dashboard
                }, 1000);
            } else {
                serverMessage.textContent = data.message;
                serverMessage.style.color = 'var(--error-red)'; // Mensagem de erro
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            serverMessage.textContent = 'Ocorreu um erro ao conectar ao servidor. Tente novamente.';
            serverMessage.style.color = 'var(--error-red)';
        } finally {
            // Remove o efeito de loading e reabilita o botão
            loginButton.classList.remove('loading-button');
            loginButton.disabled = false;
        }
    });
});