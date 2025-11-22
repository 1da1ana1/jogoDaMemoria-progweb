<?php
// Inclui o script de conexão e inicia a sessão
session_start();
require_once 'db-connection.php'; // Ajuste o caminho conforme a localização do seu arquivo.

header('Content-Type: application/json');

// 1. Verifica autenticação
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit();
}

// 2. Coleta de dados e mapeamento
$id_usuario = $_SESSION['user_id'];
$modo_jogo_js = $_POST['modo_jogo'] ?? null;
$tamanho_tabuleiro = $_POST['tamanho_tabuleiro'] ?? null;
$resultado = $_POST['resultado'] ?? null;
$jogadas = (int)($_POST['jogadas'] ?? 0);
$data_hora = date('Y-m-d H:i:s'); 

// Mapeia o valor do JS para o ENUM do banco ('classica' ou 'contratempo')
$modo_jogo_db = ($modo_jogo_js === 'contra') ? 'contratempo' : 'classico';

// 3. Validação básica (Adicione mais validações se necessário)
if (!$tamanho_tabuleiro || !$resultado || !in_array($resultado, ['Vitória', 'Derrota'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados da partida incompletos ou inválidos.']);
    exit();
}

try {
    // 4. Preparação e execução da consulta PDO
    $sql = "INSERT INTO partidas (id_usuario, modo_jogo, tamanho_tabuleiro, resultado, jogadas, data_hora) 
            VALUES (:id_usuario, :modo_jogo, :tamanho_tabuleiro, :resultado, :jogadas, :data_hora)";
            
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':modo_jogo' => $modo_jogo_db,
        ':tamanho_tabuleiro' => $tamanho_tabuleiro,
        ':resultado' => $resultado,
        ':jogadas' => $jogadas,
        ':data_hora' => $data_hora
    ]);

    echo json_encode(['success' => true, 'message' => 'Partida salva com sucesso.']);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erro ao salvar partida: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno ao salvar partida.']);
}
?>