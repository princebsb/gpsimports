<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\EmailService;

class TestEmail extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'email:test';
    protected $description = 'Test email sending';
    protected $usage       = 'email:test [to_email]';
    protected $arguments   = [
        'to_email' => 'Email address to send test to (optional, defaults to smtp_user)',
    ];

    public function run(array $params)
    {
        helper('settings');

        CLI::write('=== SMTP Settings ===', 'yellow');
        CLI::write('Host: ' . setting('smtp_host'));
        CLI::write('Port: ' . setting('smtp_port'));
        CLI::write('Crypto: ' . setting('smtp_crypto'));
        CLI::write('User: ' . setting('smtp_user'));
        CLI::write('Pass: ' . (setting('smtp_pass') ? '***SET***' : 'NOT SET'));
        CLI::write('Store Email: ' . setting('store_email'));
        CLI::newLine();

        $toEmail = $params[0] ?? setting('smtp_user');
        CLI::write('Sending test email to: ' . $toEmail, 'cyan');
        CLI::newLine();

        // Method 1: Direct email service
        $email = \Config\Services::email();

        $config = [
            'protocol'   => 'smtp',
            'SMTPHost'   => setting('smtp_host'),
            'SMTPUser'   => setting('smtp_user'),
            'SMTPPass'   => setting('smtp_pass'),
            'SMTPPort'   => (int) setting('smtp_port', 587),
            'SMTPCrypto' => setting('smtp_crypto', 'tls'),
            'SMTPTimeout' => 30,
            'mailType'   => 'html',
            'charset'    => 'UTF-8',
        ];

        CLI::write('Config: ' . json_encode($config, JSON_PRETTY_PRINT), 'dark_gray');
        CLI::newLine();

        $email->initialize($config);
        $email->setFrom(setting('smtp_user'), 'GPS Imports');
        $email->setTo($toEmail);
        $email->setSubject('Teste de Email - ' . date('Y-m-d H:i:s'));
        $email->setMessage('<h1>Teste</h1><p>Este e um email de teste do sistema GPS Imports.</p><p>Data: ' . date('Y-m-d H:i:s') . '</p>');

        CLI::write('Sending...', 'yellow');

        if ($email->send(false)) {
            CLI::write('SUCCESS: Email sent!', 'green');
        } else {
            CLI::write('FAILED: Could not send email', 'red');
            CLI::newLine();
            CLI::write('=== Debug Info ===', 'yellow');
            CLI::write($email->printDebugger(['headers', 'subject', 'body']));
        }
    }
}
