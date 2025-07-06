<?php

// Define as credenciais do banco de dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'usbw'); // Sua senha
define('DB_NAME', 'mv_invest'); // Seu novo nome de BD

// Cria a conexão
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checa a conexão
if ($mysqli->connect_error) {
    die("ERRO CONEXAO DB: " . $mysqli->connect_error); // Mensagem de erro mais clara
} else {
    echo "Conexão com o banco de dados 'mv_invest' bem-sucedida!"; // Mensagem de sucesso
    $mysqli->close(); // Feche a conexão após o teste
    exit(); // Pare a execução para ver apenas a mensagem de sucesso/erro
}
?>