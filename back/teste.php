<?php
require_once 'config/database.php';

echo "✅ Conexão com banco de dados bem-sucedida!<br>";
echo "Banco: " . DB_NAME . "<br>";
echo "Host: " . DB_HOST . "<br>";

// Testar query
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = $db->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total de usuários: " . $row['total'] . "<br>";
}
?>