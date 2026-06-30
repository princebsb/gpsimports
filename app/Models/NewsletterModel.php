<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsletterModel extends Model
{
    protected $table = 'newsletter_subscribers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'email',
        'name',
        'status',
        'source',
        'ip_address',
        'subscribed_at',
        'unsubscribed_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[newsletter_subscribers.email,id,{id}]',
    ];

    /**
     * Subscribe email
     */
    public function subscribe(string $email, ?string $name = null, string $source = 'website'): array
    {
        $existing = $this->where('email', $email)->first();

        if ($existing) {
            if ($existing['status'] === 'subscribed') {
                return ['success' => false, 'message' => 'Este email ja esta inscrito.'];
            }

            // Resubscribe
            $this->update($existing['id'], [
                'status' => 'subscribed',
                'subscribed_at' => date('Y-m-d H:i:s'),
                'unsubscribed_at' => null,
            ]);

            return ['success' => true, 'message' => 'Inscricao reativada com sucesso!'];
        }

        $this->insert([
            'email' => $email,
            'name' => $name,
            'status' => 'subscribed',
            'source' => $source,
            'ip_address' => service('request')->getIPAddress(),
            'subscribed_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Inscricao realizada com sucesso!'];
    }

    /**
     * Unsubscribe email
     */
    public function unsubscribe(string $email): bool
    {
        return $this->where('email', $email)
                    ->set([
                        'status' => 'unsubscribed',
                        'unsubscribed_at' => date('Y-m-d H:i:s'),
                    ])
                    ->update();
    }

    /**
     * Get active subscribers
     */
    public function getActiveSubscribers(): array
    {
        return $this->where('status', 'subscribed')->findAll();
    }

    /**
     * Export subscribers
     */
    public function export(): array
    {
        return $this->select('email, name, status, source, subscribed_at')
                    ->orderBy('subscribed_at', 'DESC')
                    ->findAll();
    }
}
