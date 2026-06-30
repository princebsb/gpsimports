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
    'system/Boot.php',
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
$writableDirs = ['writable', 'writable/cache', 'writable/logs', 'writable/session'];
foreach ($writableDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $writable = is_writable($path);
        $status = $writable ? '✓ Gravavel' : '✗ SEM PERMISSAO';
        $color = $writable ? 'green' : 'red';
        echo "<li style='color:$color'>$dir: $status</li>";
    } else {
        echo "<li style='color:orange'>$dir: Diretorio nao existe</li>";
    }
}
echo "</ul>";

// Verificar .env
echo "<h3>Conteudo .env (parcial):</h3>";
if (file_exists(__DIR__ . '/.env')) {
    $env = file_get_contents(__DIR__ . '/.env');
    // Mostrar apenas linhas nao sensiveis
    $lines = explode("\n", $env);
    echo "<pre>";
    foreach ($lines as $line) {
        if (strpos($line, 'password') === false && strpos($line, 'PASSWORD') === false) {
            echo htmlspecialchars($line) . "\n";
        } else {
            echo "[SENHA OCULTA]\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p style='color:red'>Arquivo .env NAO ENCONTRADO!</p>";
}

// Tentar carregar o CI4
echo "<h3>Teste de carregamento:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p style='color:green'>✓ Autoload OK</p>";

    require_once __DIR__ . '/app/Config/Paths.php';
    echo "<p style='color:green'>✓ Paths OK</p>";

} catch (Throwable $e) {
    echo "<p style='color:red'>ERRO: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
