<?php
// ===== INÍCIO DA BLINDAGEM DE SAÍDA (OUTPUT BUFFERING) =====
// Inicia o buffer de saída. A partir de agora, nada é enviado ao navegador diretamente.
ob_start();

// Define o cabeçalho JSON.
header('Content-Type: application/json; charset=utf-8');

// Tenta iniciar a sessão. O @ suprime a exibição do erro, mas ele ainda pode ocorrer.
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
// ===== FIM DA BLINDAGEM =====


require_once 'db.php';

$response = ['success' => false, 'message' => 'Ocorreu um erro inesperado.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de requisição inválido.';
    http_response_code(405);
} else {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $response['message'] = 'Por favor, preencha todos os campos.';
        http_response_code(400);
    } else {
        $stmt = $mysqli->prepare("SELECT id, email, senha_hash FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $response['message'] = 'Erro interno do servidor.';
            http_response_code(500);
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['senha_hash'])) {
                    // LOGIN BEM-SUCEDIDO
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    
                    $response['success'] = true;
                    $response['message'] = 'Login bem-sucedido!';
                    http_response_code(200);
                } else {
                    $response['message'] = 'Usuário ou senha inválidos.';
                    http_response_code(401);
                }
            } else {
                $response['message'] = 'Usuário ou senha inválidos.';
                http_response_code(401);
            }
            $stmt->close();
        }
    }
}

$mysqli->close();


// ===== LIMPEZA E ENVIO FINAL =====
// Limpa qualquer saída que tenha sido gerada até agora (como avisos de erro do PHP).
ob_clean();

// Envia a resposta JSON limpa e final.
echo json_encode($response);

// Finaliza a execução.
exit();
?>