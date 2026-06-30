<?php
/**
 * Script para adicionar colunas faltantes nas tabelas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Adicionando colunas faltantes</h2>";

$host = 'localhost';
$db = 'u699148595_gpsimports';
$user = 'u699148595_gpsimports';
$pass = 'Gpsimports@2026';
$port = 3306;

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    if ($conn->connect_error) {
        die("Erro de conexao: " . $conn->connect_error);
    }
    echo "<p style='color:green'>✓ Conectado ao banco</p>";

    // Adicionar mp_preference_id na tabela orders
    $result = $conn->query("SHOW COLUMNS FROM orders LIKE 'mp_preference_id'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE orders ADD COLUMN mp_preference_id VARCHAR(100) NULL AFTER payment_gateway");
        echo "<p style='color:blue'>+ Coluna mp_preference_id adicionada em orders</p>";
    } else {
        echo "<p style='color:green'>✓ Coluna mp_preference_id ja existe em orders</p>";
    }

    // Adicionar mp_preference_id na tabela payments
    $result = $conn->query("SHOW COLUMNS FROM payments LIKE 'mp_preference_id'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE payments ADD COLUMN mp_preference_id VARCHAR(100) NULL AFTER refunded_at");
        echo "<p style='color:blue'>+ Coluna mp_preference_id adicionada em payments</p>";
    } else {
        echo "<p style='color:green'>✓ Coluna mp_preference_id ja existe em payments</p>";
    }

    $conn->close();
    echo "<h3 style='color:green'>Concluido!</h3>";
    echo "<p><a href='/'>Voltar para o site</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red'>ERRO: " . $e->getMessage() . "</p>";
}
