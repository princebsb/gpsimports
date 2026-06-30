<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'order_id',
        'gateway',
        'method',
        'transaction_id',
        'external_id',
        'status',
        'amount',
        'fee',
        'net_amount',
        'installments',
        'installment_value',
        'card_brand',
        'card_last_digits',
        'card_holder_name',
        'pix_qrcode',
        'pix_qrcode_base64',
        'pix_copy_paste',
        'pix_expiration',
        'boleto_url',
        'boleto_barcode',
        'boleto_expiration',
        'gateway_response',
        'error_message',
        'paid_at',
        'refunded_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Removed cast to avoid double-encoding issues
    // Handle JSON encoding/decoding manually in insert/update methods

    /**
     * Get payments by order
     */
    public function getByOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get by transaction ID
     */
    public function getByTransactionId(string $transactionId): ?array
    {
        return $this->where('transaction_id', $transactionId)->first();
    }

    /**
     * Get by external ID
     */
    public function getByExternalId(string $externalId): ?array
    {
        return $this->where('external_id', $externalId)->first();
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $paymentId, string $status, ?array $response = null): bool
    {
        $data = ['status' => $status];

        if ($status === 'approved') {
            $data['paid_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'refunded') {
            $data['refunded_at'] = date('Y-m-d H:i:s');
        }

        if ($response) {
            $data['gateway_response'] = json_encode($response);
        }

        return $this->update($paymentId, $data);
    }

    /**
     * Get pending PIX payments
     */
    public function getPendingPix(): array
    {
        return $this->where('method', 'pix')
                    ->where('status', 'pending')
                    ->where('pix_expiration >', date('Y-m-d H:i:s'))
                    ->findAll();
    }

    /**
     * Get expired payments
     */
    public function getExpiredPayments(): array
    {
        return $this->where('status', 'pending')
                    ->groupStart()
                    ->where('method', 'pix')
                    ->where('pix_expiration <', date('Y-m-d H:i:s'))
                    ->groupEnd()
                    ->orGroupStart()
                    ->where('method', 'boleto')
                    ->where('boleto_expiration <', date('Y-m-d'))
                    ->groupEnd()
                    ->findAll();
    }

    /**
     * Get payment statistics
     */
    public function getStats(string $period = 'month'): array
    {
        $builder = $this->builder();

        switch ($period) {
            case 'today':
                $builder->where('DATE(created_at)', date('Y-m-d'));
                break;
            case 'week':
                $builder->where('created_at >=', date('Y-m-d', strtotime('-7 days')));
                break;
            case 'month':
                $builder->where('created_at >=', date('Y-m-01'));
                break;
        }

        return $builder->select('
                method,
                COUNT(*) as count,
                SUM(amount) as total,
                SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END) as approved_total
            ')
            ->groupBy('method')
            ->get()
            ->getResultArray();
    }
}
