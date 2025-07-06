<?php
// ===== INÍCIO DA BLINDAGEM DE ERRO =====
// Desabilita a exibição de erros na tela para não quebrar o JSON.
error_reporting(0);
ini_set('display_errors', 0);

// Define o cabeçalho JSON o mais cedo possível.
header('Content-Type: application/json; charset=utf-8');

// Tenta iniciar a sessão. Se falhar, captura o erro sem quebrar a aplicação.
if (session_status() == PHP_SESSION_NONE) {
    if (!@session_start()) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Erro crítico: Não foi possível iniciar a sessão do servidor.'
        ]);
        exit();
    }
}
// ===== FIM DA BLINDAGEM DE ERRO =====


require_once 'db.php'; // Inclui a conexão com o banco

$response = ['success' => false, 'message' => 'Ocorreu um erro inesperado.'];

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de requisição inválido.';
    http_response_code(405); // Method Not Allowed
    echo json_encode($response);
    exit();
}

// Coleta os dados do POST
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validação dos campos
if (empty($email) || empty($password)) {
    $response['message'] = 'Por favor, preencha todos os campos.';
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit();
}

// Prepara e executa a consulta de forma segura
$stmt = $mysqli->prepare("SELECT id, email, senha_hash FROM usuarios WHERE email = ?");
if (!$stmt) {
    $response['message'] = 'Erro interno do servidor ao preparar a consulta.';
    http_response_code(500);
} else {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verifica a senha com o hash do banco
        if (password_verify($password, $user['senha_hash'])) {
            // LOGIN BEM-SUCEDIDO
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            
            $response['success'] = true;
            $response['message'] = 'Login bem-sucedido!';
            http_response_code(200); // OK
        } else {
            // Senha incorreta
            $response['message'] = 'Usuário ou senha inválidos.';
            http_response_code(401); // Unauthorized
        }
    } else {
        // Usuário não encontrado
        $response['message'] = 'Usuário ou senha inválidos.';
        http_response_code(401); // Unauthorized
    }
    $stmt->close();
}

$mysqli->close();

// Envia a resposta final em JSON
echo json_encode($response);
exit();
?>