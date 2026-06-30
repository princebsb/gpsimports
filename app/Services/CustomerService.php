<?php

namespace App\Services;

use App\Models\CustomerModel;
use App\Models\CustomerAddressModel;
use App\Models\PasswordResetModel;

class CustomerService
{
    protected CustomerModel $customerModel;
    protected CustomerAddressModel $addressModel;
    protected PasswordResetModel $resetModel;

    public function __construct()
    {
        $this->customerModel = model('CustomerModel');
        $this->addressModel = model('CustomerAddressModel');
        $this->resetModel = model('PasswordResetModel');
    }

    /**
     * Register new customer
     */
    public function register(array $data): array
    {
        // Validate email uniqueness
        if ($this->customerModel->getByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Este email ja esta cadastrado.'];
        }

        // Validate password confirmation
        if ($data['password'] !== ($data['password_confirm'] ?? '')) {
            return ['success' => false, 'message' => 'As senhas nao conferem.'];
        }

        // Create customer
        $customerId = $this->customerModel->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'cpf' => $data['cpf'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'newsletter' => $data['newsletter'] ?? 0,
            'status' => 'active',
        ]);

        if (!$customerId) {
            return ['success' => false, 'message' => 'Erro ao criar conta. Tente novamente.'];
        }

        // Log in automatically
        $customer = $this->customerModel->find($customerId);
        $this->login($customer);

        // Subscribe to newsletter if opted in
        if (!empty($data['newsletter'])) {
            model('NewsletterModel')->subscribe($data['email'], $data['name'], 'registration');
        }

        return [
            'success' => true,
            'message' => 'Conta criada com sucesso!',
            'customer' => $customer,
        ];
    }

    /**
     * Authenticate customer
     */
    public function authenticate(string $email, string $password): array
    {
        $customer = $this->customerModel->verifyCredentials($email, $password);

        if (!$customer) {
            return ['success' => false, 'message' => 'Email ou senha invalidos.'];
        }

        if ($customer['status'] !== 'active') {
            return ['success' => false, 'message' => 'Sua conta esta inativa. Entre em contato conosco.'];
        }

        $this->login($customer);

        // Merge guest cart if exists
        service('cart')->mergeOnLogin($customer['id']);

        return [
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'customer' => $customer,
        ];
    }

    /**
     * Set session for logged in customer
     */
    protected function login(array $customer): void
    {
        // Extract first name
        $nameParts = explode(' ', $customer['name']);
        $firstName = $nameParts[0] ?? $customer['name'];

        $session = session();
        $session->set([
            'customer_logged_in' => true,
            'customer_id' => $customer['id'],
            'customer_name' => $customer['name'],
            'customer_first_name' => $firstName,
            'customer_email' => $customer['email'],
        ]);
    }

    /**
     * Logout customer
     */
    public function logout(): void
    {
        $session = session();
        $session->remove(['customer_logged_in', 'customer_id', 'customer_name', 'customer_first_name', 'customer_email']);
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(string $email): array
    {
        $customer = $this->customerModel->getByEmail($email);

        // Don't reveal if email exists
        if (!$customer) {
            return ['success' => true, 'message' => 'Se o email estiver cadastrado, voce recebera um link de recuperacao.'];
        }

        $token = $this->resetModel->createToken($email, 'customer');

        // Send email
        $this->sendPasswordResetEmail($customer, $token);

        return [
            'success' => true,
            'message' => 'Se o email estiver cadastrado, voce recebera um link de recuperacao.',
        ];
    }

    /**
     * Reset password
     */
    public function resetPassword(string $token, string $password): array
    {
        $reset = $this->resetModel->validateToken($token, 'customer');

        if (!$reset) {
            return ['success' => false, 'message' => 'Link invalido ou expirado.'];
        }

        $customer = $this->customerModel->getByEmail($reset['email']);

        if (!$customer) {
            return ['success' => false, 'message' => 'Conta nao encontrada.'];
        }

        // Update password
        $this->customerModel->update($customer['id'], ['password' => $password]);

        // Mark token as used
        $this->resetModel->markAsUsed($reset['id']);

        return [
            'success' => true,
            'message' => 'Senha alterada com sucesso!',
        ];
    }

    /**
     * Update customer profile
     */
    public function updateProfile(int $customerId, array $data): array
    {
        $customer = $this->customerModel->find($customerId);

        if (!$customer) {
            return ['success' => false, 'message' => 'Cliente nao encontrado.'];
        }

        // Check email uniqueness if changed
        if ($data['email'] !== $customer['email']) {
            $existing = $this->customerModel->getByEmail($data['email']);
            if ($existing && $existing['id'] != $customerId) {
                return ['success' => false, 'message' => 'Este email ja esta em uso.'];
            }
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
        ];

        $this->customerModel->update($customerId, $updateData);

        // Update session
        session()->set([
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
        ]);

        return [
            'success' => true,
            'message' => 'Dados atualizados com sucesso!',
        ];
    }

    /**
     * Update password
     */
    public function updatePassword(int $customerId, string $currentPassword, string $newPassword): array
    {
        $customer = $this->customerModel->find($customerId);

        if (!$customer) {
            return ['success' => false, 'message' => 'Cliente nao encontrado.'];
        }

        // Verify current password
        if (!password_verify($currentPassword, $customer['password'])) {
            return ['success' => false, 'message' => 'Senha atual incorreta.'];
        }

        $this->customerModel->update($customerId, ['password' => $newPassword]);

        return [
            'success' => true,
            'message' => 'Senha alterada com sucesso!',
        ];
    }

    /**
     * Save address
     */
    public function saveAddress(int $customerId, array $data): array
    {
        // Get customer for recipient name
        $customer = $this->customerModel->find($customerId);

        // Map form fields to model fields
        $addressData = [
            'customer_id' => $customerId,
            'name' => $data['label'] ?? $data['name'] ?? 'Meu Endereco',
            'recipient' => $data['recipient'] ?? $customer['name'] ?? 'Destinatario',
            'zipcode' => preg_replace('/\D/', '', $data['zipcode'] ?? ''),
            'street' => $data['street'] ?? '',
            'number' => $data['number'] ?? '',
            'complement' => $data['complement'] ?? '',
            'neighborhood' => $data['neighborhood'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'is_default' => !empty($data['is_default']) ? 1 : 0,
        ];

        // Validate required fields
        if (empty($addressData['zipcode']) || empty($addressData['street']) ||
            empty($addressData['number']) || empty($addressData['neighborhood']) ||
            empty($addressData['city']) || empty($addressData['state'])) {
            return ['success' => false, 'message' => 'Preencha todos os campos obrigatorios.'];
        }

        $db = \Config\Database::connect();

        if (!empty($data['id'])) {
            // Update existing
            $address = $this->addressModel->find($data['id']);

            if (!$address || $address['customer_id'] != $customerId) {
                return ['success' => false, 'message' => 'Endereco nao encontrado.'];
            }

            $addressData['updated_at'] = date('Y-m-d H:i:s');
            $db->table('customer_addresses')->where('id', $data['id'])->update($addressData);
            $addressId = $data['id'];
        } else {
            // Create new
            $addressData['created_at'] = date('Y-m-d H:i:s');
            $addressData['updated_at'] = date('Y-m-d H:i:s');
            $db->table('customer_addresses')->insert($addressData);
            $addressId = $db->insertID();
        }

        // Set as default if requested or if it's the first address
        $addressCount = $this->addressModel->where('customer_id', $customerId)->countAllResults();
        if (!empty($data['is_default']) || $addressCount === 1) {
            $this->addressModel->setDefault($addressId, $customerId);
        }

        return [
            'success' => true,
            'message' => 'Endereco salvo com sucesso!',
            'address_id' => $addressId,
        ];
    }

    /**
     * Delete address
     */
    public function deleteAddress(int $customerId, int $addressId): array
    {
        $address = $this->addressModel->find($addressId);

        if (!$address || $address['customer_id'] != $customerId) {
            return ['success' => false, 'message' => 'Endereco nao encontrado.'];
        }

        $this->addressModel->delete($addressId);

        return [
            'success' => true,
            'message' => 'Endereco removido com sucesso!',
        ];
    }

    /**
     * Get customer addresses
     */
    public function getAddresses(int $customerId): array
    {
        return $this->addressModel->getByCustomer($customerId);
    }

    /**
     * Send password reset email
     */
    protected function sendPasswordResetEmail(array $customer, string $token): void
    {
        $resetUrl = base_url("redefinir-senha/{$token}");

        // This would use an email service
        log_message('info', "Password reset email would be sent to {$customer['email']} with URL: {$resetUrl}");
    }

    /**
     * Get customer dashboard data
     */
    public function getDashboardData(int $customerId): array
    {
        $customer = $this->customerModel->find($customerId);
        $orders = model('OrderModel')->getByCustomer($customerId, 5);
        $wishlistCount = model('WishlistModel')->countByCustomer($customerId);

        return [
            'customer' => $customer,
            'recent_orders' => $orders,
            'total_orders' => $customer['total_orders'],
            'total_spent' => $customer['total_spent'],
            'cashback_balance' => $customer['cashback_balance'],
            'wishlist_count' => $wishlistCount,
        ];
    }
}
