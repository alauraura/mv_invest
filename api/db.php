<?php

// Define as credenciais do banco de dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'usbw'); // Sua senha
define('DB_NAME', 'mv_invest'); // Seu novo nome de BD

// Define o charset para a conexão para evitar problemas de codificação
header('Content-Type: application/json; charset=utf-8');

// Cria a conexão
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checa a conexão
if ($mysqli->connect_error) {
    // Em caso de erro, prepara uma resposta JSON e encerra.
    http_response_code(500); // Erro Interno do Servidor
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão com o banco de dados: ' . $mysqli->connect_error
    ]);
    exit();
}

// Garante que a comunicação com o banco de dados use UTF-8
$mysqli->set_charset('utf8mb4');
?>