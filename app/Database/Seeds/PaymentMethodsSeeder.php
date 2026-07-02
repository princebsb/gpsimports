<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        $paymentMethods = [
            [
                'name' => 'PIX',
                'code' => 'pix',
                'gateway' => 'mercadopago',
                'description' => 'Pagamento instantaneo via PIX',
                'instructions' => 'Apos finalizar o pedido, voce recebera um QR Code para efetuar o pagamento via PIX. O prazo para pagamento e de 30 minutos.',
                'icon' => 'pix.svg',
                'status' => 'active',
                'sort_order' => 1,
                'discount_percent' => 5.00,
                'max_installments' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Cartao de Credito',
                'code' => 'credit_card',
                'gateway' => 'mercadopago',
                'description' => 'Pague com seu cartao de credito em ate 6x',
                'instructions' => 'Parcele suas compras em ate 6x sem juros. Aceitamos Visa, Mastercard, Elo, American Express e Hipercard.',
                'icon' => 'credit-card.svg',
                'status' => 'active',
                'sort_order' => 2,
                'max_installments' => 6,
                'min_installment_value' => 10.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Cartao de Debito',
                'code' => 'debit_card',
                'gateway' => 'mercadopago',
                'description' => 'Pague a vista com cartao de debito',
                'instructions' => 'Aceitamos cartoes de debito Visa e Mastercard.',
                'icon' => 'debit-card.svg',
                'status' => 'active',
                'sort_order' => 3,
                'max_installments' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Boleto Bancario',
                'code' => 'boleto',
                'gateway' => 'mercadopago',
                'description' => 'Pague com boleto bancario',
                'instructions' => 'O boleto sera gerado apos a confirmacao do pedido. O prazo de vencimento e de 3 dias uteis. Apos o pagamento, a confirmacao pode levar ate 3 dias uteis.',
                'icon' => 'boleto.svg',
                'status' => 'active',
                'sort_order' => 4,
                'max_installments' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('payment_methods')->truncate();

        foreach ($paymentMethods as $method) {
            $this->db->table('payment_methods')->insert($method);
        }

        echo "Payment Methods seeded successfully.\n";
    }
}
