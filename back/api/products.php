<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';

$servico = sanitizar($_GET['servico'] ?? '');
$grupo = sanitizar($_GET['grupo'] ?? '');

// Construir query
$sql = "SELECT p.id, p.nome, p.descricao, p.valor_venda, g.nome as grupo
        FROM produtos p
        JOIN grupos g ON p.id_grupo = g.id
        WHERE p.estoque > 0";

$params = [];

// Se especificou um serviço, buscar apenas produtos daquele serviço
if (!empty($servico)) {
    $sql .= " AND p.id IN (
        SELECT ip.id_produto 
        FROM itens_servico ip
        JOIN servicos s ON ip.id_servico = s.id
        WHERE s.nome = ?
    )";
    $params[] = $servico;
}

// Se especificou um grupo
if (!empty($grupo)) {
    $sql .= " AND g.nome = ?";
    $params[] = $grupo;
}

$sql .= " ORDER BY g.nome, p.nome";

$result = $db->query($sql, $params);
$produtos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = [
            'id' => (int)$row['id'],
            'nome' => $row['nome'],
            'descricao' => $row['descricao'],
            'valor' => (float)$row['valor_venda'],
            'grupo' => $row['grupo']
        ];
    }
}

responderJSON(['produtos' => $produtos]);
?>