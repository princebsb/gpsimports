<?php
$pdo = new PDO('mysql:host=localhost;port=3800;dbname=gpsimports', 'root', '');
$pdo->exec("DELETE FROM settings WHERE `key` = 'usd_exchange_rate'");
$pdo->exec("INSERT INTO settings (`key`, value, type) VALUES ('usd_exchange_rate', '5.29', 'number')");
echo "Cotacao atualizada para R$ 5,29\n";
