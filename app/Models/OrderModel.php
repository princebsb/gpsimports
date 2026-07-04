<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'customer_id',
        'order_number',
        'status',
        'payment_status',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
        'items_count',
        'coupon_id',
        'coupon_code',
        'coupon_discount',
        'pix_discount',
        'cashback_used',
        'cashback_earned',
        'payment_method',
        'payment_gateway',
        'installments',
        'shipping_method',
        'shipping_name',
        'shipping_phone',
        'shipping_zipcode',
        'shipping_street',
        'shipping_number',
        'shipping_complement',
        'shipping_neighborhood',
        'shipping_city',
        'shipping_state',
        'billing_name',
        'billing_cpf',
        'billing_phone',
        'tracking_code',
        'tracking_url',
        'estimated_delivery',
        'shipped_at',
        'delivered_at',
        'notes',
        'admin_notes',
        'ip_address',
        'user_agent',
        'mp_preference_id',
        'me_label_id',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Generate unique order number
     */
    public function generateOrderNumber(): string
    {
        $prefix = date('Ym');
        $lastOrder = $this->like('order_number', $prefix, 'after')
                          ->orderBy('id', 'DESC')
                          ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder['order_number'], -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get order by number
     */
    public function getByNumber(string $orderNumber): ?array
    {
        return $this->where('order_number', $orderNumber)->first();
    }

    /**
     * Get order with items
     */
    public function getWithItems(int $orderId): ?array
    {
        $order = $this->find($orderId);

        if (!$order) {
            return null;
        }

        $order['items'] = $this->getItems($orderId);
        $order['customer'] = model('CustomerModel')->find($order['customer_id']);
        $order['payments'] = model('PaymentModel')->getByOrder($orderId);
        $order['status_history'] = $this->getStatusHistory($orderId);

        return $order;
    }

    /**
     * Get order items
     */
    public function getItems(int $orderId): array
    {
        $db = \Config\Database::connect();

        $items = $db->table('order_items')
                    ->select('order_items.*, products.featured_image as product_image, products.fonte, products.url_origem, products.preco_usd, products.weight as product_weight, products.width as product_width, products.height as product_height, products.length as product_length')
                    ->join('products', 'products.id = order_items.product_id', 'left')
                    ->where('order_items.order_id', $orderId)
                    ->get()
                    ->getResultArray();

        // Se o item não tem imagem, usar a imagem do produto
        foreach ($items as &$item) {
            if (empty($item['image']) && !empty($item['product_image'])) {
                $item['image'] = $item['product_image'];
            }
        }

        return $items;
    }

    /**
     * Add order item
     */
    public function addItem(int $orderId, array $item): bool
    {
        $db = \Config\Database::connect();

        return $db->table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'variation_id' => $item['variation_id'] ?? null,
            'name' => $item['name'],
            'sku' => $item['sku'] ?? null,
            'image' => $item['image'] ?? null,
            'price' => $item['price'],
            'original_price' => $item['original_price'] ?? $item['price'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['price'] * $item['quantity'],
            'attributes' => isset($item['attributes']) ? json_encode($item['attributes']) : null,
            'weight' => $item['weight'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get status history
     */
    public function getStatusHistory(int $orderId): array
    {
        $db = \Config\Database::connect();

        return $db->table('order_status_history')
                  ->select('order_status_history.*, users.name as user_name')
                  ->join('users', 'users.id = order_status_history.user_id', 'left')
                  ->where('order_id', $orderId)
                  ->orderBy('created_at', 'DESC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Add status to history
     */
    public function addStatusHistory(int $orderId, string $status, ?string $comment = null, bool $notifyCustomer = false, ?int $userId = null): bool
    {
        $db = \Config\Database::connect();

        return $db->table('order_status_history')->insert([
            'order_id' => $orderId,
            'status' => $status,
            'comment' => $comment,
            'notify_customer' => $notifyCustomer ? 1 : 0,
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status, ?string $comment = null, bool $notifyCustomer = false, ?int $userId = null): bool
    {
        $result = $this->update($orderId, ['status' => $status]);

        if ($result) {
            $this->addStatusHistory($orderId, $status, $comment, $notifyCustomer, $userId);
        }

        return $result;
    }

    /**
     * Get orders by customer
     */
    public function getByCustomer(int $customerId, int $limit = 10): array
    {
        return $this->where('customer_id', $customerId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get filtered orders
     */
    public function getFiltered(array $filters = [], int $perPage = 20): array
    {
        $builder = $this->select('orders.*, customers.name as customer_name, customers.email as customer_email')
                        ->join('customers', 'customers.id = orders.customer_id', 'left');

        // Status filter
        if (!empty($filters['status'])) {
            $builder->where('orders.status', $filters['status']);
        }

        // Payment status filter
        if (!empty($filters['payment_status'])) {
            $builder->where('orders.payment_status', $filters['payment_status']);
        }

        // Date range
        if (!empty($filters['from_date'])) {
            $builder->where('orders.created_at >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('orders.created_at <=', $filters['to_date'] . ' 23:59:59');
        }

        // Search
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('orders.order_number', $filters['search'])
                    ->orLike('customers.name', $filters['search'])
                    ->orLike('customers.email', $filters['search'])
                    ->groupEnd();
        }

        $builder->orderBy('orders.created_at', 'DESC');

        return [
            'orders' => $builder->paginate($perPage),
            'pager' => $this->pager,
        ];
    }

    /**
     * Get order statistics
     */
    public function getStats(string $period = 'today'): array
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
            case 'year':
                $builder->where('created_at >=', date('Y-01-01'));
                break;
        }

        $total = $builder->selectSum('total')->where('payment_status', 'approved')->get()->getRowArray();
        $count = $this->builder()->where('DATE(created_at)', date('Y-m-d'))->countAllResults();

        return [
            'total_revenue' => $total['total'] ?? 0,
            'orders_count' => $count,
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'processing' => 'Em Preparacao',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Aguardando',
            'processing' => 'Processando',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'refunded' => 'Reembolsado',
            'chargeback' => 'Chargeback',
        ];

        return $labels[$status] ?? $status;
    }
}
