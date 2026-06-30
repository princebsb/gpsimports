<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnostico GPS Imports</h2>";

// Verificar PHP
echo "<p><b>PHP Version:</b> " . PHP_VERSION . "</p>";

// Verificar arquivos essenciais
$files = [
    'app/Config/Paths.php',
    'app/Config/App.php',
    'app/Config/Database.php',
    'vendor/autoload.php',
    'vendor/codeigniter4/framework/system/Boot.php',
    '.env',
];

echo "<h3>Arquivos:</h3><ul>";
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? '✓ OK' : '✗ FALTANDO';
    $color = $exists ? 'green' : 'red';
    echo "<li style='color:$color'>$file: $status</li>";
}
echo "</ul>";

// Verificar permissoes writable
echo "<h3>Permissoes writable:</h3><ul>";
$writableDirs = ['writable', 'writable/cache', 'writable/logs', 'writable/session', 'writable/debugbar'];
foreach ($writableDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $writable = is_writable($path);
        $status = $writable ? '✓ Gravavel' : '✗ SEM PERMISSAO';
        $color = $writable ? 'green' : 'red';
        echo "<li style='color:$color'>$dir: $status</li>";
    } else {
        // Tentar criar
        if (@mkdir($path, 0755, true)) {
            echo "<li style='color:blue'>$dir: Criado agora!</li>";
        } else {
            echo "<li style='color:orange'>$dir: Diretorio nao existe</li>";
        }
    }
}
echo "</ul>";

// Verificar configuracoes criticas do .env
echo "<h3>Configuracoes criticas:</h3><ul>";
if (file_exists(__DIR__ . '/.env')) {
    $env = file_get_contents(__DIR__ . '/.env');

    // baseURL
    if (preg_match("/app\.baseURL\s*=\s*'([^']+)'/", $env, $m)) {
        $baseUrl = $m[1];
        $isCorrect = strpos($baseUrl, 'gpsimports.com.br') !== false;
        $color = $isCorrect ? 'green' : 'red';
        echo "<li style='color:$color'>app.baseURL: $baseUrl " . ($isCorrect ? '✓' : '✗ ERRADO - deve ser https://gpsimports.com.br/') . "</li>";
    }

    // Porta do banco
    if (preg_match("/database\.default\.port\s*=\s*(\d+)/", $env, $m)) {
        $port = $m[1];
        $isCorrect = $port == '3306';
        $color = $isCorrect ? 'green' : 'red';
        echo "<li style='color:$color'>database.port: $port " . ($isCorrect ? '✓' : '✗ ERRADO - deve ser 3306') . "</li>";
    }

    // Environment
    if (preg_match("/CI_ENVIRONMENT\s*=\s*(\w+)/", $env, $m)) {
        $ciEnv = $m[1];
        $isCorrect = $ciEnv == 'production';
        $color = $isCorrect ? 'green' : 'red';
        echo "<li style='color:$color'>CI_ENVIRONMENT: $ciEnv " . ($isCorrect ? '✓' : '✗ ERRADO - deve ser production') . "</li>";
    }
}
echo "</ul>";

// Teste de conexao com banco
echo "<h3>Teste de conexao com banco:</h3>";
try {
    $host = 'localhost';
    $db = 'u699148595_gpsimports';
    $user = 'u699148595_gpsimports';
    $pass = 'Gpsimports@2026';
    $port = 3306;

    $conn = new mysqli($host, $user, $pass, $db, $port);
    if ($conn->connect_error) {
        echo "<p style='color:red'>✗ Erro: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>✓ Conexao OK!</p>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Erro: " . $e->getMessage() . "</p>";
}

// Tentar carregar o CI4
echo "<h3>Teste de carregamento CI4:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p style='color:green'>✓ Autoload OK</p>";

    require_once __DIR__ . '/app/Config/Paths.php';
    $paths = new Config\Paths();
    echo "<p style='color:green'>✓ Paths OK</p>";

    echo "<p>System dir: " . realpath($paths->systemDirectory) . "</p>";

    if (file_exists($paths->systemDirectory . '/Boot.php')) {
        echo "<p style='color:green'>✓ Boot.php encontrado</p>";
    } else {
        echo "<p style='color:red'>✗ Boot.php NAO encontrado em: " . $paths->systemDirectory . "</p>";
    }

} catch (Throwable $e) {
    echo "<p style='color:red'>ERRO: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
