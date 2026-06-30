<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerAddressModel extends Model
{
    protected $table = 'customer_addresses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'customer_id',
        'name',
        'recipient',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'phone',
        'is_default',
        'type',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'customer_id' => 'required|integer',
        'recipient' => 'required|min_length[3]|max_length[200]',
        'zipcode' => 'required|min_length[8]|max_length[9]',
        'street' => 'required|min_length[3]|max_length[255]',
        'number' => 'required|max_length[20]',
        'neighborhood' => 'required|min_length[2]|max_length[100]',
        'city' => 'required|min_length[2]|max_length[100]',
        'state' => 'required|exact_length[2]',
    ];

    /**
     * Get addresses by customer
     */
    public function getByCustomer(int $customerId): array
    {
        return $this->where('customer_id', $customerId)
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get default address
     */
    public function getDefault(int $customerId): ?array
    {
        return $this->where('customer_id', $customerId)
                    ->where('is_default', 1)
                    ->first();
    }

    /**
     * Set address as default
     */
    public function setDefault(int $addressId, int $customerId): bool
    {
        // Remove default from all addresses
        $this->where('customer_id', $customerId)
             ->set('is_default', 0)
             ->update();

        // Set new default
        return $this->update($addressId, ['is_default' => 1]);
    }

    /**
     * Format address as string
     */
    public function formatAddress(array $address): string
    {
        $parts = [
            $address['street'] . ', ' . $address['number'],
        ];

        if (!empty($address['complement'])) {
            $parts[0] .= ' - ' . $address['complement'];
        }

        $parts[] = $address['neighborhood'];
        $parts[] = $address['city'] . '/' . $address['state'];
        $parts[] = 'CEP: ' . $address['zipcode'];

        return implode(' - ', $parts);
    }
}
