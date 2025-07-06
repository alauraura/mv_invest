<?php

session_start();
header('Content-Type: application/json'); // Garante que a resposta será JSON

require_once 'db.php'; // Inclui o arquivo de conexão com o banco de dados

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validação básica no lado do servidor
    if (empty($email) || empty($password)) {
        $response['message'] = 'Por favor, preencha todos os campos.';
        echo json_encode($response);
        exit();
    }

    // Prepara a consulta SQL para evitar injeção SQL
    $stmt = $mysqli->prepare("SELECT id, email, senha_hash FROM usuarios WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email); // 's' indica que o parâmetro é uma string
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $db_email, $hashed_password);
            $stmt->fetch();

            // Verifica a senha
            if (password_verify($password, $hashed_password)) {
                // Login bem-sucedido
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['email'] = $db_email;
                $response['success'] = true;
                $response['message'] = 'Login bem-sucedido!';
            } else {
                // Senha incorreta
                $response['message'] = 'Usuário ou senha inválidos.';
            }
        } else {
            // Usuário não encontrado
            $response['message'] = 'Usuário ou senha inválidos.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro interno do servidor. Tente novamente mais tarde.';
    }

    $mysqli->close();
} else {
    // Método não permitido
    $response['message'] = 'Método de requisição inválido.';
}

echo json_encode($response);
exit();
?>