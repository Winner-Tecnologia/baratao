<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once '../config/database.php';
require_once '../includes/security.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $acao = $dados['acao'] ?? '';

    // LOGIN
    if ($acao === 'login') {
        $cpf = sanitizar($dados['cpf'] ?? '');
        $senha = $dados['senha'] ?? '';

        if (empty($cpf) || empty($senha)) {
            responderJSON(['erro' => 'CPF e senha são obrigatórios'], 400);
        }

        if (fazerLogin($db, $cpf, $senha)) {
            responderJSON([
                'sucesso' => true,
                'mensagem' => 'Login realizado com sucesso',
                'usuario' => obterUsuarioAutenticado($db)
            ]);
        } else {
            responderJSON(['erro' => 'CPF ou senha incorretos'], 401);
        }
    }

    // REGISTRAR
    elseif ($acao === 'registrar') {
        $nome = sanitizar($dados['nome'] ?? '');
        $cpf = sanitizar($dados['cpf'] ?? '');
        $telefone = sanitizar($dados['telefone'] ?? '');
        $endereco = sanitizar($dados['endereco'] ?? '');
        $senha = $dados['senha'] ?? '';

        $resultado = registrarUsuario($db, $nome, $cpf, $telefone, $endereco, $senha);
        
        if (isset($resultado['sucesso'])) {
            responderJSON($resultado, 201);
        } else {
            responderJSON($resultado, 400);
        }
    }

    // LOGOUT
    elseif ($acao === 'logout') {
        fazerLogout();
        responderJSON(['sucesso' => true, 'mensagem' => 'Logout realizado']);
    }

    else {
        responderJSON(['erro' => 'Ação inválida'], 400);
    }
}
?>