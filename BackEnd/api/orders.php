<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!estaAutenticado()) {
    responderJSON(['erro' => 'Usuário não autenticado'], 401);
}

$acao = $_GET['acao'] ?? '';
$usuarioId = $_SESSION['usuario_id'];

// CRIAR PEDIDO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $acao === 'criar') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $itens = $dados['itens'] ?? [];
    $dataAgendamento = $dados['data_agendamento'] ?? null;
    $retiradaEntrega = $dados['retirada_entrega'] ?? 'Retirada';
    
    if (empty($itens)) {
        responderJSON(['erro' => 'Pedido vazio'], 400);
    }

    // Calcular total
    $valorTotal = 0;
    foreach ($itens as $item) {
        $sql = "SELECT valor_venda FROM produtos WHERE id = ?";
        $result = $db->query($sql, [$item['id']]);
        if ($result && $result->num_rows > 0) {
            $produto = $result->fetch_assoc();
            $valorTotal += $produto['valor_venda'] * $item['quantidade'];
        }
    }

    // Inserir pedido
    $sql = "INSERT INTO pedidos (id_cliente, data_agendamento, retirada_entrega, valor_total) 
            VALUES (?, ?, ?, ?)";
    $db->execute($sql, [$usuarioId, $dataAgendamento, $retiradaEntrega, $valorTotal]);
    $pedidoId = $db->lastInsertId();

    // Inserir itens
    foreach ($itens as $item) {
        $sql = "SELECT valor_venda FROM produtos WHERE id = ?";
        $result = $db->query($sql, [$item['id']]);
        $produto = $result->fetch_assoc();

        $sql = "INSERT INTO itens_pedido (id_pedido, id_produto, quantidade) 
                VALUES (?, ?, ?)";
        $db->execute($sql, [$pedidoId, $item['id'], $item['quantidade']]);
    }

    responderJSON(['sucesso' => true, 'pedido_id' => $pedidoId], 201);
}

// LISTAR PEDIDOS DO USUÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $acao === 'listar') {
    $sql = "SELECT p.id, p.data_pedido, p.data_agendamento, p.status, 
                   p.valor_total, p.retirada_entrega
            FROM pedidos p
            WHERE p.id_cliente = ?
            ORDER BY p.data_pedido DESC";
    
    $result = $db->query($sql, [$usuarioId]);
    $pedidos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = [
                'id' => (int)$row['id'],
                'data_pedido' => $row['data_pe