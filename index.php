<?php

/**
 * CodeIgniter 4 - Front Controller para hospedagem compartilhada
 * Este arquivo permite executar o CI4 a partir da raiz do site
 */

use CodeIgniter\Boot;
use Config\Paths;

// Versao minima do PHP
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Sua versao do PHP deve ser %s ou superior. Versao atual: %s',
        $minPhpVersion,
        PHP_VERSION,
    );
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

// Path to the front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
chdir(FCPATH);

// Load Paths config
require __DIR__ . '/app/Config/Paths.php';

$paths = new Paths();

// Load the framework bootstrap file
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
