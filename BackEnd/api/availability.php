<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';

$hoje = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
$diaSemana = strtolower($hoje->format('l')); // sunday, monday, etc
$horarioAtual = $hoje->format('H:i');

// Mapear dia em inglês para português
$mapaDias = [
    'sunday' => 'domingo',
    'monday' => 'segunda',
    'tuesday' => 'terca',
    'wednesday' => 'quarta',
    'thursday' => 'quinta',
    'friday' => 'sexta',
    'saturday' => 'sabado'
];

$diaSemanaPort = $mapaDias[$diaSemana] ?? '';

// Feriados brasileiros 2025 (exemplo)
$feriados = [
    '2025-01-01', // Ano Novo
    '2025-04-21', // Tiradentes
    '2025-09-07', // Independência
    '2025-12-25', // Natal
];

$dataAtual = $hoje->format('Y-m-d');
$ehFeriado = in_array($dataAtual, $feriados);

// Buscar serviços disponíveis
$sql = "SELECT id, nome, " . $diaSemanaPort . " as disponivel FROM servicos";
$result = $db->query($sql);

$servicosDisponiveis = [];
$mensagem = '';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $disponivel = (bool)$row['disponivel'];
        
        // Verificações especiais
        if ($row['nome'] === 'Almoço' && ($diaSemanaPort === 'domingo' || $diaSemanaPort === 'quinta' || $ehFeriado)) {
            if ($horarioAtual >= '11:00' && $horarioAtual &lt;= '15:00') {
                $servicosDisponiveis[] = $row['nome'];
            }
        }
        
        // Jantinha: Quarta a Sexta
        if ($row['nome'] === 'Jantinha' && in_array($diaSemanaPort, ['quarta', 'quinta', 'sexta'])) {
            if ($horarioAtual >= '18:00' && $horarioAtual &lt;= '22:00') {
                $servicosDisponiveis[] = $row['nome'];
            }
        }
        
        // Assados: Quinta, Sexta, Sábado, Domingo, Feriados
        if ($row['nome'] === 'Assados' && in_array($diaSemanaPort, ['quinta', 'sexta', 'sabado', 'domingo']) || $ehFeriado) {
            if ($horarioAtual >= '18:00' && $horarioAtual &lt;= '22:00') {
                $servicosDisponiveis[] = $row['nome'];
            }
        }
    }
}

$aberto = !empty($servicosDisponiveis);

responderJSON([
    'aberto' => $aberto,
    'servicos' => $servicosDisponiveis,
    'mensagem' => $aberto ? 'Estabelecimento aberto' : 'Estabelecimento fechado no momento',
    'horario_atual' => $horarioAtual,
    'data' => $dataAtual
]);
?>