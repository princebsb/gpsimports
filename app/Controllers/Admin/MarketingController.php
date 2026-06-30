<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MarketingController extends BaseController
{
    public function newsletter()
    {
        $db = \Config\Database::connect();

        $subscribers = $db->table('newsletter_subscribers')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/marketing/newsletter', [
            'title' => 'Newsletter',
            'subscribers' => $subscribers,
        ]);
    }

    public function exportNewsletter()
    {
        $db = \Config\Database::connect();

        $subscribers = $db->table('newsletter_subscribers')
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        $filename = 'newsletter_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Email', 'Data de Inscricao']);

        foreach ($subscribers as $sub) {
            fputcsv($output, [$sub['email'], $sub['created_at']]);
        }

        fclose($output);
        exit;
    }
}
