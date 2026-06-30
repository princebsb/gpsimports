<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Checkout - Mercado Pago</h2>";

// Carregar CI4
require_once __DIR__ . '/vendor/autoload.php';

// Testar SDK MercadoPago
echo "<h3>Teste SDK MercadoPago:</h3>";

try {
    // Verificar se classes existem
    $classes = [
        'MercadoPago\SDK',
        'MercadoPago\Preference',
        'MercadoPago\Item',
        'MercadoPago\Payer',
    ];

    foreach ($classes as $class) {
        if (class_exists($class)) {
            echo "<p style='color:green'>✓ $class existe</p>";
        } else {
            echo "<p style='color:red'>✗ $class NAO existe</p>";
        }
    }

    // Tentar inicializar
    $accessToken = 'APP_USR-4714064342572260-063014-8c40d4428c1eb0393b3daea89d7ef8e5-48800322';

    echo "<h3>Inicializando SDK:</h3>";
    \MercadoPago\SDK::setAccessToken($accessToken);
    echo "<p style='color:green'>✓ SDK inicializado</p>";

    // Testar criacao de preferencia
    echo "<h3>Teste criar preferencia:</h3>";

    $preference = new \MercadoPago\Preference();

    $item = new \MercadoPago\Item();
    $item->id = "teste123";
    $item->title = "Produto Teste";
    $item->quantity = 1;
    $item->unit_price = 100.00;
    $item->currency_id = "BRL";

    $preference->items = [$item];
    $preference->external_reference = "TESTE-" . time();
    $preference->back_urls = [
        'success' => 'https://gpsimports.com.br/checkout/sucesso/teste',
        'failure' => 'https://gpsimports.com.br/checkout/falha/teste',
        'pending' => 'https://gpsimports.com.br/checkout/pendente/teste',
    ];
    $preference->auto_return = 'approved';

    $preference->save();

    if ($preference->id) {
        echo "<p style='color:green'>✓ Preferencia criada: " . $preference->id . "</p>";
        echo "<p>Init Point: <a href='" . $preference->init_point . "' target='_blank'>" . $preference->init_point . "</a></p>";
    } else {
        echo "<p style='color:red'>✗ Erro ao criar preferencia</p>";
        echo "<pre>" . print_r($preference, true) . "</pre>";
    }

} catch (Throwable $e) {
    echo "<p style='color:red'>ERRO: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Verificar logs
echo "<h3>Ultimos erros do log:</h3>";
$logFile = __DIR__ . '/writable/logs/log-' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -30);
    echo "<pre style='background:#333;color:#fff;padding:10px;font-size:11px;max-height:400px;overflow:auto'>";
    foreach ($lastLines as $line) {
        if (strpos($line, 'CRITICAL') !== false || strpos($line, 'ERROR') !== false) {
            echo "<span style='color:#ff6b6b'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "<p>Log de hoje nao encontrado: $logFile</p>";
}
